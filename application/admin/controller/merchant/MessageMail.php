<?php

namespace app\admin\controller\merchant;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class MessageMail extends Backend
{
    use \app\admin\library\traits\Backend {
        del as protected traitdel;
        preExcludeFields as protected traitPreExcludeFields;
    }
    /**
     * MessageMail
     * @var \app\admin\model\merchant\MessageMail
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\merchant\MessageMail;

    }
    /**
     * 排除前台提交过来的字段
     * @param $params
     * @return array
     */
    protected function preExcludeFields($params)
    {
        $params = $this->traitPreExcludeFields($params);
        if ($this->request->isPost()) {
            $message_id = $this->request->get("message_id");
            if($message_id) {
                $params["message_id"] = $message_id;
            }
        }
        return $params;
    }
    // /**
    //  * 添加
    //  */
    // public function add()
    // {
    //     // exit;
    //     if ($this->request->isPost()) {
    //         $params = $this->request->post("row/a");
    //         if ($params) {
    //             $params = $this->preExcludeFields($params);

    //             if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
    //                 $params[$this->dataLimitField] = $this->auth->id;
    //             }
    //             $result = false;
    //             Db::startTrans();
    //             try {
    //                 //是否采用模型验证
    //                 if ($this->modelValidate) {
    //                     $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
    //                     $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
    //                     $this->model->validateFailException(true)->validate($validate);
    //                 }
    //                 $result = $this->model->allowField(true)->save($params);
    //                 Db::commit();
    //             } catch (ValidateException $e) {
    //                 Db::rollback();
    //                 $this->error($e->getMessage());
    //             } catch (PDOException $e) {
    //                 Db::rollback();
    //                 $this->error($e->getMessage());
    //             } catch (Exception $e) {
    //                 Db::rollback();
    //                 $this->error($e->getMessage());
    //             }
    //             if ($result !== false) {
    //                 $this->success();
    //             } else {
    //                 $this->error(__('No rows were inserted'));
    //             }
    //         }
    //         $this->error(__('Parameter %s can not be empty', ''));
    //     }
    //     return $this->view->fetch();
    // }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
