<?php

namespace app\admin\controller\merchant;

use app\admin\model\AuthGroup;
use app\common\controller\Backend;
use fast\Random;
use fast\Tree;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 商家管理员管理
 *
 * @icon fa fa-circle-o
 */
class MerchantAdmin extends Backend
{
    
    /**
     * MerchantAdmin模型对象
     * @var \app\admin\model\merchant\MerchantAdmin
     */
    protected $model = null;

    /**
     * 商家groupIds
     */
    protected $merchantGroupIds = [];

    /**
     * 绑定商家查询语句
     */
    protected $merchantWhere = "1 = 1";

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\merchant\MerchantAdmin;

        //获取商家管理组
        $groupList = collection(AuthGroup::where('type', '=', AuthGroup::GROUP_TYPE_MERCHANT)->order('pid')->select())->toArray();
        Tree::instance()->init($groupList);
        $childlist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray("1"));
        $result = [];
        foreach ($childlist as $k => $v)
        {
            $this->merchantGroupIds[] = $v['id'];
            $result[$v['id']] = $v['name'];
        }
        $this->view->assign('groupdata', $result);

        //初始化绑定商家查询语句
        if( session('?merchant') ) {
            $mid = session('merchant')['id'];
            $this->merchantWhere = 'merchant_id = '.$mid.' or merchant.pid = '.$mid;
        }
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

            $searchFields = [
                'merchant.merchant_name',
                'admin.username',
                'admin.nickname',
            ];
            list($where, $sort, $order, $offset, $limit) = $this->buildparams($searchFields);

            $total = $this->model
                    ->with(['merchant','admin'])
                    ->where($where)->whereRaw($this->merchantWhere)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['merchant','admin'])
                    ->where($where)->whereRaw($this->merchantWhere)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                $row->visible(['id','admin_id','merchant_id']);
                $row->visible(['merchant']);
                $row->getRelation('merchant')->visible(['merchant_name']);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['username','nickname','avatar']);
            }

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     * @throws \think\Exception
     */
    public function add($ids = null)
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params)
            {
                $params['salt'] = Random::alnum();
                $params['password'] = md5(md5($params['password']) . $params['salt']);
                $params['avatar'] = '/assets/img/avatar.png'; //设置新管理员默认头像。

                $result = false;
                Db::startTrans();
                try {
                    $adminModel = model('Admin');
                    $result = $adminModel->validate('Admin.add')->allowField(true)->save($params);
                    if ($result === false)
                    {
                        $this->error($adminModel->getError());
                    }
                    $params['admin_id'] = $adminModel->id;
                    $group = $this->request->post("group/a");

                    //过滤不允许的组别,避免越权
                    $myGroups = $this->auth->getGroups();
                    $hasRight = false;
                    foreach ($myGroups as $value) {
                        if( is_null($value["type"]) || $value["type"]==AuthGroup::GROUP_TYPE_PLATFORM ) {
                            $hasRight = true;
                            break;
                        }
                    }
                    if( !$hasRight ) {
                        $this->error(__('You have no permission'));
                    }
                    $dataset = [];
                    $dataset[] = ['uid' => $adminModel->id, 'group_id' => $group[0]];
                    model('AuthGroupAccess')->saveAll($dataset);

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

        $merchant = new \app\admin\model\Merchant;

        $row = $merchant->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        $this->view->assign("row", $row);

        return $this->view->fetch();
    }

    /**
     * 删除
     * @throws \think\Exception
     */
    public function del($ids = "")
    {
        if ($ids)
        {
            // 获取管理员ID
            $list = $this->model->distinct(true)->field('admin_id')->where('id', 'in', $ids)->select();
            if ($list) {
                $adminIds = [];
                foreach ($list as $k => $v) {
                    $adminIds[] = $v->getData('admin_id');
                }
            }

            // 避免越权删除管理员
            $myGroups = $this->auth->getGroups();
            $hasRight = false;
            foreach ($myGroups as $value) {
                if( is_null($value["type"]) || $value["type"]==AuthGroup::GROUP_TYPE_PLATFORM ) {
                    $hasRight = true;
                    break;
                }
            }
            if( !$hasRight ) {
                $this->error(__('You have no permission'));
            }
            $merchantGroupIds = $this->merchantGroupIds;
            $adminModel = model('Admin');
            $adminList = $adminModel->where('id', 'in', $adminIds)->where('id', 'in', function($query) use($merchantGroupIds) {
                $query->name('auth_group_access')->where('group_id', 'in', $merchantGroupIds)->field('uid');
            })->select();
            if ($adminList)
            {
                $deleteIds = [];
                foreach ($adminList as $k => $v)
                {
                    $deleteIds[] = $v->id;
                }
                $deleteIds = array_diff($deleteIds, [$this->auth->id]);
                if ($deleteIds)
                {
                    $adminModel->destroy($deleteIds);
                    model('AuthGroupAccess')->where('uid', 'in', $deleteIds)->delete();
                    $this->model->destroy($ids);
                    $this->success();
                }
            }
        }
        $this->error();
    }

}
