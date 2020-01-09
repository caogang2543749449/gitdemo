<?php

namespace app\admin\controller\merchant;

use app\admin\common\MerchantBackend;

/**
 * 商家设施表

 *
 * @icon fa fa-circle-o
 */
class Facilities extends MerchantBackend
{
    
    /**
     * Facilities模型对象
     * @var \app\admin\model\merchant\Facilities
     */
    protected $model = null;
    protected $modelValidate = true;

    //定义快速搜索字段
    protected $searchFields = 'facility_name,description';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\merchant\Facilities;

        $this->fetchParentFacilities();
        $this->fetchTags();

    }

    private function fetchParentFacilities() {
        if ($this->request->isAjax()){
            return;
        }
        $parentFacilities = $this->model
                ->where(function($query){
                    if( session('?merchant') ) {
                        $query->where("merchant_id", session('merchant')['id']);
                    }
                    $query->where(function($q) {
                        $q->where("pid", 'null')->whereOr("pid", '0');
                    });
                })
                ->select();
        $viewParentFacilities = [];
        if($parentFacilities) {
            foreach($parentFacilities as $parentFacility) {
                $viewParentFacilities[$parentFacility->id] = $parentFacility->facility_name;
            }
        }
        $this->view->assign("parentFacilities", $viewParentFacilities);
        $this->assignConfig("parentFacilities", $parentFacilities);
    }

    private function fetchTags() {
        $tagList = $this->model->getTagList();
        $this->view->assign("tagList", $tagList);
        $this->assignConfig("tagList", $tagList);
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
                    ->with(['merchant'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['merchant'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                $row->visible(['id', 'pid', 'tag', 'weigh', 'merchant_id', 'facility_name', 'updatetime']);
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
                $row->visible(['id','merchant_id','ssid','deletetime']);
                $row->visible(['merchant']);
                $row->getRelation('merchant')->visible(['merchant_name']);
            }

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }
}
