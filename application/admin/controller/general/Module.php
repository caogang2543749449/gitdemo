<?php

namespace app\admin\controller\general;

use app\common\controller\Backend;
use fast\Random;
use think\Config;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Log;

/**
 * 功能模块表

 *
 * @icon fa fa-circle-o
 */
class Module extends Backend
{
    
    
    use \app\admin\library\traits\Backend {
        add as protected traitadd;
        edit as protected traitedit;
    }
    /**
     * Module模型对象
     * @var \app\admin\model\Module
     */
    protected $model = null;

    /**
     * 全部功能列表，按照展示位置组织的2维数组：[展示位置][功能定义]
     */
    protected $moduleList = [];
    /**
     * 全部展示位置列表
     */
    protected $locations = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Module;
        $this->view->assign("targetTypeList", $this->model->getTargetTypeList());
        $this->assignconfig('targetTypeList', json_encode($this->model->getTargetTypeList()));

        // 按位置整理功能列表
        $this->locations = $this->model->group('location')->column('location');
        foreach ($this->locations as $location) {
            $rows = $this->model->with("merchant")->where('location', '=', $location)->order('weigh desc')->select();
            if( $rows ) {
                foreach ($rows as $module) {
                    $this->moduleList[$location][$module['id']] = $module;
                }
            }
        }

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    /**
     * 当前商家自定义功能查看
     * @return string|\think\response\Json
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            return $this->view->fetch();
        }

        // isAjax
        //如果发送的来源是Selectpage，则转发到Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        // 取得展示位置参数
        $location = $this->request->get('location');
        if(is_null($location)) {
            //如果展示位置参数不存在则返回所有展示位置列表
            $result = array("rows" => $this->locations);
            return json($result);
        }
        $locations = $this->fetchModulesOfLocation($location);
        return json(array("rows" => $locations));
    }

    private function fetchModulesOfLocation($location) {
        $moduleModel =  model('merchant_module','model\\merchant');
        $row = $moduleModel->where('merchant_id', '0')->find();
        if($row == null) {
            $this->updateDefaultModuleWeigh($moduleModel);
            $row = $moduleModel->where('merchant_id', '0')->find();
        }
        $defaultModules = [];
        //则显示所有功能并选中自定义功能
        // 取出自定义功能列表
        $moduleIds = json_decode($row['module_ids'], true);
        $subIds = $moduleIds[$location];
        $total = sizeof($this->moduleList[$location]);
        $index = 0;
        // 选中功能排在前面
        foreach ($subIds as $id) {
            $module = $this->moduleList[$location][$id];
            $module['pid'] = ['location'=>$location, 'standard'=>$module['is_standard']];
            $module['select_index'] = $total - $index;
            $defaultModules[] = $module;
            $index++;
        }
        // 未选中功能排在后面
        foreach ($this->moduleList[$location] as $module) {
            if (!in_array($module['id'], $subIds)) {
                $defaultModules[] = $module;
                $module['pid'] = $location;
                $module['select_index'] = 0;
                $index++;
            }
        }

        $list = collection($defaultModules)->toArray();
        return $list;
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
     * 默认功能的排序
     * @param $ids string 最新顺序的功能ID列表字符串
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sort($ids) {
        if (!$ids) {
            $this->error(__('Unknown data format'));
        }
        //取得前台传入的排序后功能ID列表
        $frontModules = explode(',', $ids);
        //取得排序对象所属的展示位置
        $target = $this->request->post("pid/a");
        //取得默认功能排序列表
        $row = model('merchant_module','model\\merchant')->where('merchant_id', '0')->find();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }

        //以前的功能ID排序
        $orgModules = json_decode($row->getAttr('module_ids'), true);
        // 差分比较前台传入的功能ID排序与以前的功能ID排序，得到未开通ID
        $diff = array_diff($frontModules, $orgModules[$target['location']]);
        // 从前台ID中去掉未开通ID
        foreach ($diff as $id) {
            $ret = array_search($id, $frontModules);
            if( $ret!==false){
                unset($frontModules[$ret]);
            }
        }
        $orgModules[$target['location']] = array_merge($frontModules);

        $openedModules = json_encode($orgModules, JSON_NUMERIC_CHECK);
        //结果写入DB
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $this->model->validateFailException(true)->validate($validate);
            }
            $result = $row->setAttr('module_ids', $openedModules)->allowField(true)->save();
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
        $this->error(__('Unknown data format'));
    }

    /**
     * 上传功能icon图片
     */
    public function upload()
    {
        Config::set('default_return_type', 'json');
        $file = $this->request->file('file');
        if (empty($file)) {
            $this->error(__('No file upload or server upload limit exceeded'));
        }

        //判断是否已经存在附件
        $sha1 = $file->hash();
        $extparam = $this->request->post();

        $upload = Config::get('upload');

        preg_match('/(\d+)(\w+)/', $upload['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int)$upload['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);
        $fileInfo = $file->getInfo();
        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';

        $mimetypeArr = explode(',', strtolower($upload['mimetype']));
        $typeArr = explode('/', $fileInfo['type']);

        //验证文件后缀
        if ($upload['mimetype'] !== '*' &&
            (
                !in_array($suffix, $mimetypeArr)
                || (stripos($typeArr[0] . '/', $upload['mimetype']) !== false && (!in_array($fileInfo['type'], $mimetypeArr) && !in_array($typeArr[0] . '/*', $mimetypeArr)))
            )
        ) {
            $this->error(__('Uploaded file format is limited'));
        }
        $replaceArr = [
            '{.suffix}'  => $suffix ? '.' . $suffix : '',
            '{filemd5}'  => md5_file($fileInfo['tmp_name']),
        ];
        $savekey = '/uploads/module_icon/{filemd5}{.suffix}';
        $savekey = str_replace(array_keys($replaceArr), array_values($replaceArr), $savekey);

        $uploadDir = substr($savekey, 0, strripos($savekey, '/') + 1);
        $fileName = substr($savekey, strripos($savekey, '/') + 1);
        //
        $splInfo = $file->validate(['size' => $size])->move(ROOT_PATH . '/public' . $uploadDir, $fileName);
        if ($splInfo) {
            $imagewidth = $imageheight = 0;
            if (in_array($suffix, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'])) {
                $imgInfo = getimagesize($splInfo->getPathname());
                $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
            }
            $params = array(
                'admin_id'    => 0,
                'user_id'     => 0,
                'filesize'    => $fileInfo['size'],
                'imagewidth'  => $imagewidth,
                'imageheight' => $imageheight,
                'imagetype'   => $suffix,
                'imageframes' => 0,
                'mimetype'    => $fileInfo['type'],
                'url'         => $uploadDir . $splInfo->getSaveName(),
                'uploadtime'  => time(),
                'storage'     => 'local',
                'sha1'        => $sha1,
                'extparam'    => json_encode($extparam),
            );
            $attachment = model("attachment");
            $attachment->data(array_filter($params));
            $attachment->save();
            \think\Hook::listen("upload_after", $attachment);
            $this->success(__('Upload successful'), null, [
                'url' => $uploadDir . $splInfo->getSaveName()
            ]);
        } else {
            // 上传失败获取错误信息
            $this->error($file->getError());
        }
    }

    /**
     * 更新默认的功能排序
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function updateDefaultModuleWeigh($moduleModel) {
        $defaultModules = [];
        foreach ($this->moduleList as $location => $modules) {
            $defaultModules[$location] = [];
            // 写入标准功能
            foreach ($modules as $id => $module) {
                if ($module['is_standard'] == 1) {
                    $defaultModules[$location][] = $module['id'];
                }
            }
        }
        $defaultModules = json_encode($defaultModules, JSON_NUMERIC_CHECK);

        // 结果写入DB
        $result = false;
        Db::startTrans();
        $row = $moduleModel->where('merchant_id', '0')->find();
        try {
            if( !$row ) {
                $params = [
                    'merchant_id' => 0,
                    'module_ids' => $defaultModules,
                ];
                $result = $moduleModel->allowField(true)->save($params);
            } else {
                $orgModuleIds = $row->getAttr('module_ids');
                if($orgModuleIds!==$defaultModules) {
                    $result = $row->setAttr('module_ids', $defaultModules)->allowField(true)->save();
                } else {
                    $result = true;
                }
            }
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
        if ($result === false) {
            Log::error('updateDefaultModuleWeigh() '.__('Fatal error'));
            $this->error(__('Fatal error'));
        }
    }

}
