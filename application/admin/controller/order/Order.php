<?php

namespace app\admin\controller\order;

use app\admin\common\OrderBackend;
use app\common\library\LakalaUtil;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 订单管理
 *
 * @icon fa fa-circle-o
 */
class Order extends OrderBackend
{

    /**
     * Order模型对象
     * @var \app\admin\model\Order
     */
    protected $model = null;

    /**
     * 快速搜索时执行查找的字段
     */
    protected $searchFields = 'order_sn,checkin_name,checkin_room';


    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Order;
        $statusTextList = self::$statusList;
        array_walk($statusTextList, function (&$status) {
            $status = __('Status '.$status);
        });
        $this->view->assign("statusList", array_combine(self::$statusList, $statusTextList));
        $this->assignconfig("statusList", array_combine(self::$statusList, $statusTextList));
        $this->view->assign("paidTypeList", $this->model->getPaidTypeList());
        $this->view->assign("deliverTypeList", $this->model->getDeliverTypeList());

        $refundReasons = self::$refundReasonList;
        array_walk($refundReasons, function (&$text) {
            $text = __($text);
        });
        $this->view->assign("refundReasonList", $refundReasons);
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = false;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null,null,['status', 'in', '201,301,302']);
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {
                $row->visible(['id','order_sn','status', 'real_money', 'delivery_hope_date', 'delivery_hope_time', 'checkin_room', 'checkin_name', 'createtime']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 查看详情
     * @param null $ids
     */
    public function detail($ids = null)
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        $order = $this->model->find($ids);
        if (!$order) {
            $this->error(__('No Results were found'));
        }
        $currentStatus = $order->getAttr('status');
        $refundedMoney = intval($order->getAttr('refunded_money'));
        $lastRefundTime = $order->getAttr('last_refund_time');

        if ($this->request->isAjax()) {
            $list = $order->orderGoods()
                ->alias('order_goods')
                ->join('goods', 'goods_id=goods.id')
                ->field('thumb_image', false, 'goods')
                ->field('*', false, 'order_goods')
                ->select();

            foreach ($list as $row) {
                $row->visible(['id', 'thumb_image', 'goods_id', 'goods_name', 'goods_sn', 'price', 'quantity', 'refunded_quantity']);
            }
            $list = collection($list)->toArray();
            $result = array(
                "rows" => $list,
                "status" => __('Status '.$currentStatus),
                "refundedMoney" => $refundedMoney,
                "lastRefundTime" => $lastRefundTime
            );

            return json($result);
        }

        $nextStep = $this->getMerchantNextStep($currentStatus);
        if( $nextStep ) {
            $this->view->assign("nextStep", $nextStep);
        }
        if( $this->canRefund($currentStatus) ) {
            $this->assignconfig("canRefund", 'true');
        }
        $this->view->assign("row", $order);
        return $this->view->fetch();
    }

    /**
     * 按钮后订单状态滚动
     * @param null $ids
     */
    public function next_step($ids = null)
    {
        $row = $this->model->find($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $currentStep = $row->getAttr('status');
        $nextStep = $this->request->post("nextStep");
        if(key_exists($currentStep, self::$merchantRolloverList)) {
            $nextAction = key(self::$merchantRolloverList[$currentStep]);
            if($nextStep == $nextAction) {
                switch ($nextStep) {
                    case self::STATUS_PAID:
                        if(empty($row->getAttr('paid_id'))) {
                            [$ret, $msg] = $this->refreshPaid($row);
                            if($ret) {
                                $this->success('', null, $msg);
                            } else {
                                $this->error($msg);
                            }
                        }
                        $this->success();
                        break;
                    case self::STATUS_DELIVERED:
                        $this->deliver($row);
                        $this->success();
                        break;
                    case self::STATUS_USER_MISSED:
                        $this->holdOrder($row);
                        $this->success();
                        break;
                }
            }
        }
        $this->error(__('Operation failed'));
    }

    /**
     * 退款处理
     * @param null $ids
     */
    public function refund($ids = null)
    {
        $order = $this->model->find($ids);
        if (!$order) {
            $this->error(__('No Results were found'));
        }

        $refundId = $this->request->param('refund_id');
        $orderGoods = $order->orderGoods()
            ->alias('order_goods')
            ->join('goods', 'goods_id=goods.id')
            ->where('order_goods.id', '=', $refundId)
            ->field('thumb_image', false, 'goods')
            ->field('*', false, 'order_goods')
            ->find();

        //处理提交的退款申请
        if ($this->request->isAjax()) {
            $refundParams = $this->request->param('refund/a');
            $result = $this->processRefund($refundParams, $order, $orderGoods);
            //退款成功时，更新DB
            if($result===true) {
                $this->success();
            }
            $this->error($result);
        }
        //初始化退款画面
        $refundableQuantity = $orderGoods['quantity'] - $orderGoods['refunded_quantity'];
        $this->view->assign("refundableQuantity", $refundableQuantity);
        $this->view->assign("refundId", $refundId);
        $this->view->assign("orderGoods", $orderGoods->getData());
        return $this->view->fetch();
    }

    /**
     * 根据NihaoPay的支付查询接口返回的结果更新订单状态，并写入支付信息
     * @param $row Object 订单
     * @return $paidId, message
     */
    private function refreshPaid($row)
    {
        $nihaoPay = LakalaUtil::instance();
        [$paidId, $paidExpand] = $nihaoPay->checkPaidStatus($row);
        if(!$paidId) {
            return [false, $paidExpand];
        }
        $params = [
            'status' => self::STATUS_PAID,
            'paid_id' => $paidId,
            'paid_type' => '1',
            'paid_time' => date ('Y-m-d H:i:s', strtotime($paidExpand))
        ];
        $result = $this->updateOrder($params, $row);
        if ($result === false) {
            $this->error(__('No rows were updated'));
        }
        return [true, '{"paid_id": "'.$paidId.'", "paid_time": "'.$params['paid_time'].'"}'];
    }

    /**
     * 更新订单状态为已发货，并写入发货时间
     * @param $row Object 订单
     */
    private function deliver($row)
    {
        $params = [
            'status' => self::STATUS_DELIVERED,
            'deliver_time' => datetime(time())
        ];
        $result = $this->updateOrder($params, $row);
        if ($result === false) {
            $this->error(__('No rows were updated'));
        }
    }

    /**
     * 更新订单状态为用户不在，不进行自动收货处理
     * @param $row Object 订单
     */
    private function holdOrder($row)
    {
        $params = [
            'status' => self::STATUS_USER_MISSED,
        ];
        $result = $this->updateOrder($params, $row);
        if ($result === false) {
            $this->error(__('No rows were updated'));
        }
    }

    /**
     * 更新订单
     * @param $params array 要更新的数据
     * @param $row Object 订单
     * @return bool|string 更新成功或者失败原因
     */
    private function updateOrder($params, $row)
    {
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException(true)->validate($validate);
            }
            $result = $row->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException $e) {
            Db::rollback();
            return $e->getMessage();
        } catch (PDOException $e) {
            Db::rollback();
            return $e->getMessage();
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return $result;
    }

    /**
     * 处理退款信息
     * @param $refundParams array 退款数据
     * @param $order Object 订单
     * @param $orderGoods Object 订单商品
     * @return bool|string 成功或者失败原因
     */
    private function processRefund($refundParams, &$order, &$orderGoods)
    {
        //退款数量不能大于订单商品剩余数量
        $refundQuantity = intval($refundParams['refund_quantity']);
        $refundedQuantity = $orderGoods->getAttr('refunded_quantity');
        $leftQuantity = $orderGoods->getAttr('quantity') - $refundedQuantity;
        if($refundQuantity > $leftQuantity) {
            return __('Refund quantity is more then left');
        }

        //退款金额不能大于剩余金额
        $refundedMoney = intval($order->getAttr('refunded_money'));
        $refundMoney = intval($orderGoods->getAttr('price')) * $refundQuantity;
        $totalMoney = intval($order->getAttr('real_money'));
        if($refundMoney <= 0) {
            return __('Total refund amount is zero');
        }
        if(($refundedMoney+$refundMoney)>$totalMoney) {
            return __('Total refund amount is more then paid amount');
        }

        //退款原因为其他原因时理由不能为空
        $refundReasonCode = intval($refundParams['refund_reason_code']);
        $refundReason = self::$refundReasonList[$refundReasonCode];
        if(self::REFUND_OTHER_REASONS === $refundReasonCode) {
            $refundReasonCustom = $refundParams['refund_reason'];
            if(empty($refundReasonCustom)) {
                return __('Parameter %s can not be empty', __('Refund_reason'));
            }
            $refundReason = $refundReasonCustom;
        }

        //处理退款
        [$result, $refundId] = LakalaUtil::instance()->orderRefund($order, $refundMoney, $refundReason);
        //写入退款记录
        if($result===true) {
            $result = $this->saveOrderForRefund($order, $refundedMoney, $refundMoney, $totalMoney);
            $result = $this->saveOrderGoodsForRefund($orderGoods, $refundedQuantity, $refundQuantity) && $result;
            $result = $this->saveOrderRefundHisForRefund($order, $orderGoods, $refundMoney, $refundReasonCode, $refundReason, $refundQuantity, $refundId) && $result;
            if($result===false) {
                return __('No rows were updated');
            }
            return true;
        }
        return $refundId;
    }

    private function saveOrderForRefund($order, $refundedMoney, $refundMoney, $totalMoney) {
        $refundTime = datetime(time());
        $params = [];
        $result = false;
        Db::startTrans();
        try {
            // 更新订单表
            $params['refunded_money'] = $refundedMoney + $refundMoney;
            $params['last_refund_time'] = $refundTime;
            if($params['refunded_money']==$totalMoney) {
                $orderGoods = $order->orderGoods()
                ->where('price', '0')
                ->find();
                if($orderGoods == null) {
                    $params['status'] = self::STATUS_REFUNDED;
                }
            }
            $result = $order->allowField(true)->save($params);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        if($result===false) {
            return __('No rows were updated');
        }
        return true;

    }

    private function saveOrderGoodsForRefund($orderGoods, $refundedQuantity, $refundQuantity) {
        $params = [];
        $result = false;
        Db::startTrans();
        try {
           //更新订单商品表
            $params['refunded_quantity'] = $refundedQuantity + $refundQuantity;
            $result = $orderGoods->allowField(true)->save($params);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        if($result===false) {
            return __('No rows were updated');
        }
        return true;
    }

    private function saveOrderRefundHisForRefund($order, $orderGoods, $refundMoney, $refundReasonCode, $refundReason, $refundQuantity, $refundId) {
        $refundTime = datetime(time());
        $params = [];
        $result = false;
        Db::startTrans();
        try {
            //写入退款记录表
            $params['order_id'] = $order->getAttr('id');
            $params['merchant_id'] = $order->getAttr('merchant_id');
            $params['order_goods_id'] = $orderGoods->getAttr('id');
            $params['goods_id'] = $orderGoods->getAttr('goods_id');
            $params['refund_time'] = $refundTime;
            $params['refund_money'] = $refundMoney;
            $params['refund_reason_code'] = $refundReasonCode;
            $params['refund_reason'] = $refundReason;
            $params['refund_number'] = $refundQuantity;
            $params['refund_id'] = $refundId;
            
            $result = model('app\admin\model\order\OrderRefundHis')->allowField(true)->save($params);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        if($result===false) {
            return __('No rows were updated');
        }
        return true;

    }
}