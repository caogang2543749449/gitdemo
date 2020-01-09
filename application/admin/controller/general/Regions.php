<?php

namespace app\admin\controller\general;

use app\common\controller\Backend;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Regions extends Backend
{
    
    use \app\admin\library\traits\Backend {
        edit as protected traitedit;
        multi as protected traitmulti;
    }
    /**
     * Regions模型对象
     * @var \app\admin\model\general\Regions
     */
    protected $model = null;
    protected $multiFields = 'open_switch,is_popular';

    public function _initialize()
    {
        parent::_initialize();
        $this->modelValidate = true;
        $this->model = new \app\admin\model\general\Regions;

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
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $this->updateSubItemForOpenSwitch($ids, $params);
        }
        return $this->traitedit($ids);
    }
    /**
     * 批量更新
     */
    public function multi($ids = "")
    {
        if (!$this->request->isPost()) {
            return;
        }
        $ids = $ids ? $ids : $this->request->param("ids");
        if (!$ids) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }

        if (!$this->request->has('params')) {
            return;
        }
        parse_str($this->request->post("params"), $values);
        $values = array_intersect_key($values, array_flip(is_array($this->multiFields) ? $this->multiFields : explode(',', $this->multiFields)));
        $this->updateSubItemForOpenSwitch($ids, $values);
        $this->traitmulti($ids);
    }

    private function updateSubItemForOpenSwitch($ids, $values)
    {
        if (!$ids) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }
        if(!isset($values['open_switch'])) {
            return;
        }
        $params = array('open_switch' => $values['open_switch']);
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        Db::startTrans();
        try {
            $list = $this->model->where($this->model->getPk(), 'in', $ids)->select();
            foreach ($list as $index => $item) {
                $subRegions = [];
                if($item->level == 1) {
                    $subRegions = $this->model->where('code', '<>', $item->code)->where('prefecture', $item->prefecture)->select();
                } else if($item->level == 2) {
                    $subRegions= $this->model->where('code', '<>', $item->code)->where('city', $item->city)->select();
                } 
                foreach ($subRegions as $subIndex => $subItem) {
                    $subItem->allowField(true)->isUpdate(true)->save($params);
                }
            }
            Db::commit();
        } catch (PDOException $e) {
            Db::rollback();
            $this->error($e->getMessage());
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
    }

}
