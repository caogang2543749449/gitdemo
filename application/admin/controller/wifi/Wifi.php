<?php

namespace app\admin\controller\wifi;

use app\admin\common\MerchantBackend;

/**
 * 商家wifi列管理
 *
 * @icon fa fa-circle-o
 */
class Wifi extends MerchantBackend
{
    
    /**
     * Wifi模型对象
     * @var \app\admin\model\merchant\Wifi
     */
    protected $model = null;

    //定义快速搜索字段
    protected $searchFields = 'ssid';

    public function _initialize()
    {
        parent::_initialize();
        $this->modelValidate = true;
        $this->model = new \app\admin\model\merchant\Wifi;
        $this->view->assign("wifiTypeList", $this->model->getWifiTypeList());
        $this->view->assign("DynamicContentList", [
            '房间号' => __('room No.'), 
            '楼层' => __('floor'), 
            '酒店提供信息' => __('Information from hotel')
        ]);
        $this->view->assign("verifyTypeList", $this->model->getVerifyTypeList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     * @throws \think\Exception
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with('merchant')
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with('merchant')
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                $row->visible(['id','merchant_id','wifi_type','weigh','ssid','security_key','description','show_policy_flg']);
                $row->visible(['merchant']);
                $row->getRelation('merchant')->visible(['merchant_name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 回收站
     * @throws \think\Exception
     */
    public function recyclebin()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->onlyTrashed()
                ->with('merchant')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->onlyTrashed()
                ->with('merchant')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {
                $row->visible(['id','merchant_id','ssid','show_policy_flg','deletetime']);
                $row->visible(['merchant']);
                $row->getRelation('merchant')->visible(['merchant_name']);
            }

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }
}
