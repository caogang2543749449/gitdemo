<?php

namespace app\admin\controller\merchant;

use app\admin\common\MerchantBackend;
use app\common\controller\Backend;
use app\common\library\Email;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 商家留言信息管理
 *
 * @icon fa fa-circle-o
 */
class Message extends MerchantBackend
{
    
    /**
     * MerchantMsg模型对象
     * @var \app\admin\model\MerchantMsg
     */
    protected $model = null;

    //定义快速搜索字段
    protected $searchFields = 'id,user.nickname,content,local_content';

    /**
     * 状态转换
     */
    protected $nextSteps = ['1'=>'2', '2'=>'3', '3'=>'9', '9'=>'1'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\MerchantMsg;
        $this->view->assign("msgTypeList", $this->model->getMsgTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
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
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null,null,['merchant_msg.status', '<>', '9']);
            $total = $this->model
                ->with(['merchant', 'user'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['merchant', 'user'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {
                $row->visible(['id','merchant_id','user_id','msg_type','checkin_name','checkout_date','status','createtime','updatetime']);
                $row->visible(['merchant']);
				$row->getRelation('merchant')->visible(['merchant_name']);
                $row->visible(['user']);
                $row->getRelation('user')->visible(['nickname']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 查看
     */
    public function edit($ids=null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if($params["sendmail"]) {
                $merchantId = 0;
                if( session('?merchant') ) {
                    $merchantId = session('merchant')['id'];
                }
                $model = new \app\admin\model\MailContent;
                $model->dataInitialize($merchantId);
                $content = $model
                    ->where('type', 'message_system_receiving')
                    ->where('merchant_id', $merchantId)->find();
                $isSent = $this->send($row, $content);
                $this->saveHistory($row, $content, $isSent);
            }
            if ($params) {
                $params = $this->preExcludeFields($params);
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
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        } else {
            $mailList = $this->getMessageMailList($ids);
            $this->view->assign("mailList", $mailList);

            $contentList = $this->getMailContentList();
            $this->view->assign("contentList", $contentList);
        }
        $this->view->assign("row", $row);

        // 状态滚动处理
        $status = $row->getAttr('status');
        $next_step = 9;
        if(key_exists($status, $this->nextSteps)) {
            $next_step = $this->nextSteps[$status];
        }
        $this->view->assign("next_step", $next_step);

        return $this->view->fetch();
    }
    private function getMessageMailList($ids) {
        $mailModel = new \app\admin\model\merchant\MessageMail;

        $mailWhere = "message_id = ".$ids;
        $list = $mailModel->where($mailWhere)->select();
        return collection($list)->toArray();
    }
    private function getMailContentList() {
        if (!session('?merchant')) {
            return array();
        }
        $merchant = session('merchant');//['merchant_id', '=', session('merchant')['id']];
        $model = new \app\admin\model\MailContent;
        $model->dataInitialize($merchant['id']);
        $list = $model->where("merchant_id", $merchant['id'])->select();
        if(!$list) {
            return array();
        }
        return collection($list)->toArray();
    }

    /**
     * Email
     */
    public function sendmail() {
        if (!$this->request->isPost()) {
            return null;
        }
        if (!$this->request->isAjax())
        {
            return null;
        }
        $params = $this->request->post();
        $message = $this->model->get($params['message_id']);
        $model = new \app\admin\model\MailContent;
        $content = $model->get($params['mail_content_id']);
        if(!isset($message["email"]) || empty($message["email"])){
            $this->error(__('Parameter %s can not be empty', 'email'));
        }
        $isSent = $this->send($message, $content);
        $this->saveHistory($message, $content, $isSent);

        $this->success();
    }
    private function saveHistory($message, $content, $isSent = false){

        Db::startTrans();
        try {
            $messageMailModel = new \app\admin\model\merchant\MessageMail;
            $messageMailModel->allowField(true)->save(array(
                'message_id' => $message->id,
                'content' => $content->content,
                'content_trans' => $content->content_trans,
                'is_sent' => !!$isSent,
            ));
            Db::commit();
        } catch (ValidateException $e) {
            Db::rollback();
            $this->error($e->getMessage());
        } catch (PDOException $e) {
            Db::rollback();
            $this->error($e->getMessage());
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
    }

    /**
     * @internal
     */
    private function send($message, $content)
    {
        if(!isset($message["email"]) || empty($message["email"])){
            $this->error(__('Parameter %s can not be empty', 'email'));
        }
        $createtime = date("Y年m月d日", $message->createtime);
        $merchant_name = $message->merchant->merchant_name;
        $body = <<<EOF
<div>$message->checkin_name 先生/女士</div>

<div>您好。</div>

<div style="padding: 20px; line-height:30px;">
    <div>您于 $createtime 提交的失物问询，</div>

    <div style="padding: 20px;">$content->content_trans</div>

    <div>欢迎您再次使用本酒店的服务。</div>
</div>
<div>$merchant_name</div>

<div style="margin-top: 20px;">请不要回复本电子邮件。发送到本地址的电子邮件将不予答复。</div>
EOF;
        $email = new Email;
        $result = $email
            ->from("no-reply@miniprogram.jp", $merchant_name)
            ->to($message["email"])
            // ->to("test.eb@kansea.com", $message->checkin_name)
            ->subject("[" . $createtime . "]失物问询的回复")
            ->message($body)
            ->send();
        return $result;
    }
    
}
