<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class MailContent extends Backend
{
    use \app\admin\library\traits\Backend {
        edit as protected traitedit;
        del as protected traitdel;
        preExcludeFields as protected traitPreExcludeFields;
    }
    // use \app\admin\library\traits\Backend {
    //     del as public traitdel;
    // }
    /**
     * MailContent模型对象
     * @var \app\admin\model\MailContent
     */
    protected $model = null;
    
    protected $merchant=null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\MailContent;
        $this->view->assign("typeList", $this->model->getTypeList());
        
        $this->view->assign('isSuperAdmin', $this->auth->isSuperAdmin());

    }
    protected function preExcludeFields($params)
    {
        $params = $this->traitPreExcludeFields($params);
        if ($this->request->isPost()) {
            $params["merchant_id"] = '0';
        }
        return $params;
    }


    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $systemWhere = [];
            $systemWhere['merchant_id'] = "0";
            $total = $this->model
                ->where($where)
                ->where($systemWhere)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where($systemWhere)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $this->typeCheckForEdit($ids);
        return $this->traitedit($ids);
    }

    private function typeCheckForEdit($ids = null) {
        if (!$this->request->isPost()) {
            return;
        }
        $params = $this->request->post("row/a");
        if(!isset($params['type']) || $params['type'] == 'message_system_receiving'){
            return;
        }
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if($row->type=='message_system_receiving'){
            $count = $this->model->where('merchant_id', $row['merchant_id'])->count();
            if($count == '1') {
                $this->error(__('type cannot be changed due to being used by System'));
            }
        }
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            $pk = $this->model->getPk();
            $list = $this->model->where($pk, 'in', $ids)->select();

            foreach ($list as $k => $v) {
                if($v->type == 'message_system_receiving') {
                    $this->error(__('cannot be deleted due to being used by System'));
                    return;
                }
            }
        }
        $this->traitdel($ids);
    }
}
