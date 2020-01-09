<?php

namespace app\admin\controller\general;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class MailTemplate extends Backend
{
    
    use \app\admin\library\traits\Backend {
        add as protected traitadd;
        edit as protected traitedit;
    }
    
    /**
     * MailTemplate模型对象
     * @var \app\admin\model\general\MailTemplate
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\general\MailTemplate;

    }

    /**
     * 添加
     */
    public function add()
    {
        $this->fetchMerchantList();
        return $this->traitadd();
    }
    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $this->fetchMerchantList();
        return $this->traitedit($ids);
    }

    private function fetchMerchantList()
    {
        if ($this->request->isAjax()) {
            return;
        }
        if ($this->request->isPost()) {
            return;
        }
        $model = new \app\admin\model\Merchant;
        $result = $model->where('status', '1')->whereOr('status', '2')->select();
        $this->view->assign("merchantList", $result);
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
