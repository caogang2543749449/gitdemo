<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;

/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class User extends Backend
{

    protected $relationSearch = true;


    /**
     * @var \app\admin\model\User
     */
    protected $model = null;

    //定义快速搜索字段
    protected $searchFields = 'id,username,nickname';

    /**
     * 绑定商家查询语句
     */
    protected $merchantWhere = "1 = 1";

    /**
     * 绑定log查询语句
     */
    protected $lastjoin = '(select user_id, MAX(updatetime) as lasttime from %s where %s group by user_id)';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('User');
        //初始化绑定商家查询语句
        $logWhere = "1 = 1";
        if( session('?merchant') ) {
            $this->merchantWhere = "usermerchant.merchant_id = ".session('merchant')['id'];
            $logWhere = 'merchant_id = '.session('merchant')['id'];
        }
        $this->lastjoin = sprintf($this->lastjoin, $this->model->getTable('user_action_log'), $logWhere);

    }

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with('group')
                ->where($where)
                ->join('user_merchant usermerchant', 'user.id = usermerchant.user_id', 'LEFT' )
                ->whereRaw($this->merchantWhere)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with('group')
                ->join('user_merchant usermerchant', 'user.id = usermerchant.user_id', 'LEFT' )
                ->where($where)
                ->whereRaw($this->merchantWhere)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->join($this->lastjoin.' userlog', 'user.id = userlog.user_id', 'LEFT' )
                ->field('userlog.lasttime')
                ->select();

            foreach ($list as $k => $v) {
                $v->hidden(['password', 'salt']);
            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $this->view->assign('groupList', build_select('row[group_id]', \app\admin\model\UserGroup::column('id,name'), $row['group_id'], ['class' => 'form-control selectpicker']));
        return parent::edit($ids);
    }

}
