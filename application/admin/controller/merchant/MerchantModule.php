<?php

namespace app\admin\controller\merchant;

use app\admin\common\MerchantBackend;
use fast\Http;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Log;


/**
 * 商家开通模块管理
 *
 * @icon fa fa-circle-o
 */
class MerchantModule extends MerchantBackend
{
    
    /**
     * Merchant_module模型对象
     * @var \app\admin\model\merchant\MerchantModule
     */
    protected $model = null;
    /**
     * 绑定商家查询语句
     */
    protected $merchantWhere = "1 = 1";

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
        $this->model = new \app\admin\model\merchant\MerchantModule;

        $moduleModel = model('Module');
        $this->assignconfig('targetTypeList', json_encode($moduleModel->getTargetTypeList()));

        // 按位置整理功能列表
        $this->locations = $moduleModel->group('location')->column('location');
        foreach ($this->locations as $location) {
            $rows = $moduleModel->where('location', '=', $location)->where(function($query) {
                $query->where("merchant_id", '0');
                if( !session('?merchant') ) {
                    return;
                }
                $query->whereOr("merchant_id", session('merchant')['id']);
            })->order('weigh desc')->select();
            if( $rows ) {
                foreach ($rows as $module) {
                    $this->moduleList[$location][$module['id']] = $module;
                }
            }
        }

        //初始化绑定商家查询语句
        if( session('?merchant') ) {
            $this->merchantWhere = "merchant_id = ".session('merchant')['id'];
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
        if ($this->request->isAjax()) {
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
            //检索商家的自定义功能
            $row = $this->model->whereRaw($this->merchantWhere)->find();
            $merchantModules = [];
            if (!$row) {//商家无自定义功能，则按照展示位置、默认排序显示所有功能
                $modules = $this->moduleList[$location];
                // 标准功能排在前列，并默认选中
                foreach ($modules as $id => $module) {
                    if ($module['is_standard'] == 1) {
                        $module['pid'] = $location; // pid 用于子表动作是，传递子表所属展示位置
                        $module['is_selected'] = true; // is_selected用于展示功能是否开通，发生动作是用于传递功能开通或者关闭
                        $module['select_index'] = $module['weigh']; // select_index用于传递页面展示时的排序
                        $merchantModules[] = $module;
                    }
                }
                //可选功能排在后面
                foreach ($modules as $module) {
                    if ($module['is_standard'] == 0) {
                        $module['pid'] = $location;
                        $module['is_selected'] = false;
                        $module['select_index'] = 0;
                        $merchantModules[] = $module;
                    }
                }
            } else {//商家有自定义功能，则显示所有功能并选中自定义功能
                // 取出自定义功能列表
                $merchantModuleIds = json_decode($row['module_ids'], true);
                $ids = $merchantModuleIds[$location];
                // 选中功能排在前面
                $total = sizeof($this->moduleList[$location]);
                $index = 0;
                foreach ($ids as $id) {
                    if(!isset($this->moduleList[$location][$id])) {
                        continue;
                    }
                    $module = $this->moduleList[$location][$id];
                    $module['pid'] = $location;
                    $module['is_selected'] = true;
                    $module['select_index'] = $total - $index;
                    $merchantModules[] = $module;
                    $index++;
                }
                // 未选中功能排在后面
                foreach ($this->moduleList[$location] as $module) {
                    if (!in_array($module['id'], $ids)) {
                        $merchantModules[] = $module;
                        $module['pid'] = $location;
                        $module['is_selected'] = false;
                        $module['select_index'] = 0;
                        $index++;
                    }
                }
            }

            $list = collection($merchantModules)->toArray();
            $result = array("rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 开通或关闭指定功能功能
     * @param null $ids 指定功能的ID
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function toggleOpen($ids=null)
    {
        if (!$ids) {
            $this->error(__('Unknown data format'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("params");
            if ($params) {
                // 取得前台传入的功能开关flg
                $toggleOpen = str_replace('is_selected=', '', $params);
                if(!is_numeric($toggleOpen)) {
                    Log::error('is_selected is not a number.');
                    $this->error(__('No rows were updated'));
                }

                // 取得商家自定义功能列表
                $merchantId = session('merchant')['id'];
                $openedModules = [];
                $row = $this->model->whereRaw($this->merchantWhere)->find();
                if (!$row) {
                    /*
                     * 当前商家没有自定义开通服务，则写入最新服务（默认全部标准功能，再根据开关服务追加）
                     */
                    foreach ($this->moduleList as $location => $modules) {
                        $inLocation = false;
                        foreach ($modules as $module) {
                            if ($module['is_standard'] == 1) {
                                $openedModules[$location][] = $module['id'];
                            }
                            if ($module['id'] == $ids) {
                                $inLocation = true;
                            }
                        }
                        if($inLocation) {
                            $ret = array_search($ids, $openedModules[$location]);
                            if ($ret !== false && $toggleOpen === '0') {
                                unset($openedModules[$location][$ret]);
                            } else if ($ret === false && $toggleOpen === '1') {
                                $openedModules[$location][] = $ids;
                            }
                        }
                        // 去掉数组key，保存时不保存自定义key
                        $openedModules[$location] = array_merge($openedModules[$location]);
                    }
                    // 最新的开通功能列表转换为json格式字符串
                    $openedModules = json_encode($openedModules, JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES);
                    // 结果写入DB
                    $this->addnew($this->model, $merchantId, $openedModules);
                } else {
                    /*
                     * 当前商家已经开通服务，则更新服务ID
                     */
                    $adminIds = $this->getDataLimitAdminIds();
                    if (is_array($adminIds)) {
                        if (!in_array($row[$this->dataLimitField], $adminIds)) {
                            $this->error(__('You have no permission'));
                        }
                    }

                    $targetLocation = model('Module')->find($ids)['location'];
                    $openedModules = json_decode($row->getAttr('module_ids'), true);
                    foreach ($openedModules as $location => $modules) {
                        if( $targetLocation != $location ) {
                            continue;
                        }
                        $ret = array_search($ids, $openedModules[$location]);
                        if( $ret!==false && $toggleOpen==='0') {
                            unset($openedModules[$location][$ret]);
                        } else if( $ret===false && $toggleOpen==='1') {
                            $openedModules[$location][] = $ids;
                        }
                        // 去掉数组key，保存时不保存自定义key
                        $openedModules[$location] = array_merge($openedModules[$location]);
                    }
                    // 最新的开通功能列表转换为json格式字符串
                    $openedModules = json_encode($openedModules, JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES);
                    // 结果写入DB
                    $this->updateOrg($this->model, $row, $openedModules);
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $this->error(__('Unknown data format'));
    }

    /**
     * 自定义功能的排序
     * @param $ids string 最新顺序的功能ID列表字符串
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sort($ids) {
        if (!$ids) {
            $this->error(__('Unknown data format'));
        }
        $openedModules=[];
        //取得前台传入的排序后功能ID列表
        $frontModules = explode(',', $ids);
        //取得排序对象所属的展示位置
        $targetLocation = $this->request->post("pid");
        //取得商家自定义功能列表
        $merchantId = session('merchant')['id'];
        $row = $this->model->whereRaw($this->merchantWhere)->find();
        if (!$row) {
            /*
             * 当前商家未开通服务的数据，则写入最新服务
             */
            foreach ($this->moduleList as $location => $modules) {
                // 如果是当前展示位置，则从前台传入的全部id中去掉可选功能
                if($targetLocation == $location) {
                    foreach ($modules as $module) {
                        if ($module['is_standard'] == 0) {
                            $ret = array_search($module['id'], $frontModules);
                            unset($frontModules[$ret]);
                        }
                    }
                    $openedModules[$location] = array_merge($frontModules);
                }
                // 如果非当前展示位置，则写入默认的标准功能
                else {
                    $openedModules[$location] = [];
                    foreach ($modules as $module) {
                        if ($module['is_standard'] == 1) {
                            $openedModules[$location][] = $module['id'];
                        }
                    }
                }
            }
            // 最新的开通功能列表转换为json格式字符串
            $openedModules = json_encode($openedModules, JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES);
            // 结果写入DB
            $this->addnew($this->model, $merchantId, $openedModules);

        } else {
            /*
             * 当前商家已开通服务，则更新服务ID（从前台传入的全部id中去掉未开通功能id）
             */
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                if (!in_array($row[$this->dataLimitField], $adminIds)) {
                    $this->error(__('You have no permission'));
                }
            }

            //取得开通功能ID
            $openedModules = json_decode($row->getAttr('module_ids'), true);
            // 差分比较前台传入的功能ID与已开通功能ID，得到未开通ID
            $diff = array_diff($frontModules, $openedModules[$targetLocation]);
            // 从前台ID中去掉未开通ID
            foreach ($diff as $id) {
                $ret = array_search($id, $frontModules);
                if( $ret!==false){
                    unset($frontModules[$ret]);
                }
            }
            $openedModules[$targetLocation] = array_merge($frontModules);
            // 最新的开通功能列表转换为json格式字符串
            $openedModules = json_encode($openedModules, JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES);
            // 结果写入DB
            $this->updateOrg($this->model, $row, $openedModules);
        }

        $this->error(__('Unknown data format'));
    }

    /**
     * 取得或更新功能模块需要的类目
     * @param $ids
     * @return string
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function categories($ids) {
        if (!$ids) {
            $this->error(__('Parameter %s can not be empty', 'id'));
        }
        $merchantId = session('merchant')['id'];
        $row = $this->model->whereRaw($this->merchantWhere)->find();

        if( $this->request->isPost() ){
            $categories = $this->request->post('categories/a');
            $type = $this->request->post('type');
            $method = $this->request->post('method');
            $typedCategories = [];
            $typedCategories[$type] = [];
            $typedCategories[$type]['method'] = $method;
            $typedCategories[$type]['categories'] = array_merge(array_filter($categories));
            $typedCategories = json_encode($typedCategories, JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES);
            // 结果写入DB
            //取得商家自定义功能列表
            if($row) {
                // 当前商家有自定义开通服务，则更新最新类目
                $this->updateOrg($this->model, $row, null, $typedCategories);
            } else {
                // 当前商家没有自定义开通服务，则写入最新服务（默认全部标准功能，再根据开关服务追加）和类目
                foreach ($this->moduleList as $location => $modules) {
                    foreach ($modules as $module) {
                        if ($module['is_standard'] == 1) {
                            $openedModules[$location][] = $module['id'];
                        }
                    }
                    // 去掉数组key，保存时不保存自定义key
                    $openedModules[$location] = array_merge($openedModules[$location]);
                }
                // 最新的开通功能列表转换为json格式字符串
                $openedModules = json_encode($openedModules, JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES);
                // 结果写入DB
                $this->addnew($this->model, $merchantId, $openedModules, $typedCategories);
            }
        }
        // 取得类目服务的地址
        $categoryUrl = null;
        $type = null;
        foreach ($this->moduleList as $modules) {
            foreach ($modules as $module) {
                if( $module['id']==$ids) {
                    $categoryUrl = $module['category_url'];
                    $type = $module['key'];
                }
            }
        }
        if(!$categoryUrl || !$type) {
            Log::error('Undefined Category url or type.');
            $this->error(__('Unknown data format'));
        }
        // 从类目服务地址获取类目
        $jsonBody = '{"categoryId": [], "type": "'.$type.'"}';
        $res = Http::post($categoryUrl, $jsonBody, [CURLOPT_HTTPHEADER=>array (
            "Content-Type: application/json",
        )]); //[CURLINFO_CONTENT_TYPE=>'application/json']
        if(!$res) {
            Log::error('Get category failed with network error.');
            $this->error('Get category failed for type : '.$type);
        }
        $result = json_decode($res, true);
        $this->view->assign('categories', $result);

        $selected = '';
        $method = 'include';
        if($row) {
            $selectCate = json_decode($row->getAttr('categories'));
            if($selectCate) {
//                $refClass = new ReflectionClass($selectCate);
                $selected = $selectCate->$type->categories;
                $method = $selectCate->$type->method;
            }
        }
        $this->view->assign('select_ids', $selected);
        $this->view->assign('method', $method);
        $this->view->assign('type', $type);
        return $this->view->fetch();
    }


    private function addnew($model, $merchantId, $openedModules, $categories=null)
    {
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $model->validateFailException(true)->validate($validate);
            }
            $params = [
                'merchant_id' => $merchantId,
                'module_ids' => $openedModules,
            ];
            if(!is_null($categories)) {
                $params['categories'] = $categories;
            }
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

    private function updateOrg($model, $row, $openedModules=null, $categories=null)
    {
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                $model->validateFailException(true)->validate($validate);
            }
            $params = [];
            if (!is_null($openedModules)) {
                $params['module_ids'] = $openedModules;
            };
            if(!is_null($categories)) {
                $params['categories'] = $categories;
            }
            $result = $row->allowField(true)->save($params);
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
    }

}