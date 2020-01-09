<?php

namespace app\admin\controller\service;

use app\admin\common\MerchantBackend;
use app\admin\model\service\Category;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 订单商品管理
 *
 * @icon fa fa-circle-o
 */
class Goods extends MerchantBackend
{
    
    /**
     * Goods模型对象
     * @var \app\admin\model\service\Goods
     */
    protected $model = null;

    //定义快速搜索字段
    protected $searchFields = 'name, local_name';

    protected $modelValidate = true;

    protected $multiFields = 'is_for_sale';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\service\Goods;
        list($where, $sort, $order, $offset, $limit) = $this->buildparams();
        $categoryModel = new \app\admin\model\service\Category;
        $categorys = $categoryModel->where($where)->select();
        $categoryList = [];
        foreach ($categorys as $category) {
            $categoryList[$category['id']] = __($category['name']);
        }
        $this->view->assign('categoryList', $categoryList);
        $this->assignconfig('categoryList', $categoryList);
        if( session('?merchant') ) {
            $merchant = session('merchant');
            \app\admin\model\service\Category::dataInitialize($merchant['id']);
        }
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
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
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
                $row->visible(['id','goods_sn','name','local_name','category_id','thumb_image','is_for_sale','price','weigh']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 复制商品
     */
    public function copy($ids = "")
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    unset($params['id']);
                    $result = $this->model->allowField(true)->save($params);
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
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $row->setAttr('name', '');
        $row->setAttr('local_name', '');
        $row->setAttr('goods_sn', '');
        $this->view->assign("row", $row);
        return $this->view->fetch('edit');
    }


}
