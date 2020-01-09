<?php

namespace app\admin\controller\merchant;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Coupon extends Backend
{
    use \app\admin\library\traits\Backend {
        preExcludeFields as protected traitPreExcludeFields;
    }
    /**
     * Coupon模型对象
     * @var \app\admin\model\Coupon
     */
    protected $model = null;
    protected $multiFields = 'open_switch,tax_free_switch';

    public function _initialize()
    {   
        parent::_initialize();
        $this->modelValidate = true;
        $this->model = new \app\admin\model\merchant\Coupon;
        $this->view->assign("getRuleList", $this->model->getGetRuleList());
        $this->view->assign("useRuleList", $this->model->getUseRuleList());
        $this->view->assign("validSwitchList", $this->model->getValidSwitchList());

    }
    /**
     * 排除前台提交过来的字段
     * @param $params
     * @return array
     */
    protected function preExcludeFields($params)
    {
        $params = $this->traitPreExcludeFields($params);
        if (!$this->request->isPost()) {
            return $params;
        }
        if(!session('?merchant') ) {
            return $params;
        }
        $params["merchant_id"] = session('merchant')['id'];
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
            $tabWhereFunction = $this->getTabWhereFunction();
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['merchant'])
                ->where($where)
                ->where($tabWhereFunction)
                ->where($this->getMerchantConditionFunction())
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['merchant'])
                ->where($where)
                ->where($tabWhereFunction)
                ->where($this->getMerchantConditionFunction())
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

                
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    private function getMerchantConditionFunction() {
        return function($query) {
            if( session('?merchant') ) {
                $query->where("merchant_id", session('merchant')['id']);
            }
        };
    }
    private function getTabWhereFunction() {
        return function($query) {
            $tab_valid = $this->request->get('valid', '');// == 't-valid';
            if($tab_valid == 't-valid') {
                $query
                    ->where('valid_switch', '1')
                    ->whereOr(function($orquery){
                        $date_now = date("Y-m-d H:i:s");
                        $orquery
                            ->whereOr('start_time', '<=', $date_now)
                            ->where('end_time', '>=', $date_now);
                    });
            } else if($tab_valid == 't-not-valid') {
                $query
                    ->where('valid_switch', '0')
                    ->where(function($orquery){
                        $date_now = date("Y-m-d H:i:s");
                        $orquery
                            ->where('start_time', '>', $date_now)
                            ->whereOr('end_time', '<', $date_now);
                    });
            }
        };
    }
    // commonsearch_tab_selector
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
