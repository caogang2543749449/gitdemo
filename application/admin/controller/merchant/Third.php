<?php

namespace app\admin\controller\merchant;

use app\common\controller\Backend;
use fast\Http;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Log;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Third extends Backend
{
    
    /**
     * Third模型对象
     * @var \app\admin\model\merchant\Third
     */
    protected $model = null;
    /**
     * 绑定商家查询语句
     */
    protected $merchantWhere = "1 = 1";

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\merchant\Third;
        $this->view->assign("thirdTypeList", $this->model->getThirdTypeList());
        $this->assignconfig("thirdTypeList", $this->model->getThirdTypeList());
        $this->view->assign("pymentList", $this->model->getPymentList());
        
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
                ->with('merchant')
                ->where($where)->whereRaw($this->merchantWhere)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with('merchant')
                ->where($where)->whereRaw($this->merchantWhere)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {
                $row->visible(['id','merchant_id','third_type','app_name']);
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
     * 添加
     * @throws \think\Exception
     */
    public function add($ids = null)
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);

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
                    //这里需要针对username和email做唯一验证
                    $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                    $result = $this->model->allowField(true)->validate($name.'.add')->save($params);
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
//                    $this->error(__('No rows were inserted'));
                    $this->error($this->model->getError());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $row = model('Merchant')->get($ids);
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
     * 编辑
     * @throws \think\Exception
     */
    public function edit($ids = null)
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        $row = $this->model->with("merchant")->find($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //这里需要针对username和email做唯一验证
                    $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                    $row->data($params);
                    $scancodeRet = true;
                    if( $row->getChangedData() ) {
                        if($row->getAttr('third_type')==1 ) {//微信小程序
                            $scancodeRet = $this->getWechatScancode($row);
                        }
                    }
                    $result = $row->allowField(true)->validate($name.'.edit')->save();
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
                    if($scancodeRet) {
                        $this->success();
                    } else {
                        $this->error('Get scan-code failed');
                    }
                } else {
                    $this->error($row->getError());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 获取微信小程序码
     * @param $row
     */
    private function getWechatScancode($row)
    {
        $thirdId = $row->id;
        $merchantId = $row->getAttr('merchant_id');

        // 通过微信API取得小程序码
        $appId = $row->getAttr('appid');
        $appSecret = $row->getAttr('app_secret');
        $authUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $appSecret;
        $res = Http::get($authUrl);
        if(!$res) {
            Log::error('Get wechat access_token failed with network error.');
            return false;
        }
        $result = json_decode($res);
        if(property_exists($result, 'errcode')) {
            Log::error('Get wechat access_token failed with error-code('.$result->errcode.') : '.$result->errmsg);
            return false;
        }
        $accessToken = $result->access_token;
        $miniCodeUrl = 'https://api.weixin.qq.com/wxa/getwxacode?access_token=' . $accessToken;
        $postJson = '{"path" : "pages/index/index?merchantId=' . $merchantId . '", "width" : 1280}';
        $res = Http::post($miniCodeUrl, $postJson, [CURLOPT_TIMEOUT => 10]);
        if(!$res) {
            Log::error('Get wxa-code failed with network error.');
            return false;
        }
        $result = json_decode($res);
        if($result) {
            Log::error('Get wxa-code failed with error-code('.$result->errcode.') : '.$result->errmsg);
            return false;
        }

        // 保存取得的小程序码
        $uploadurl = $this->view->config['upload']['uploadurl'];
        $filename = md5($merchantId.'1'.$appId).'.jpg';
        $filepath = '/uploads/merchant_scancode/';
        $filefullpath = $filepath;
        if( preg_match("/^((?:[a-z]+:)?\\/\\/|data:image\\/)/i", $uploadurl) ) {
            //use cdn
            //TODO
        } else {
            $filefullpath = ROOT_PATH . '/public' . $filepath;
        }
        if( !file_exists($filefullpath)){
            mkdir($filefullpath);
        }
        $ret = file_put_contents($filefullpath.$filename, $res);
        if( !$ret ) {
            Log::error('Save wxa-code to file failed.');
            return false;
        }

        // 小程序码图片路径存到DB
        $scanModel = model('merchant_scancode','model\\merchant');
        $scan = $scanModel->where([
                'merchant_id' => $merchantId,
                'merchant_third_id' => $thirdId
            ])
            ->find();
        $params = [];
        $params['url'] = $filepath.$filename.'?'.time();
        $params['third_updatetime'] = $row->getAttr('updatetime');
        $qcos_key = $filepath.$filename;
        \think\Hook::listen("upload_to_qcos", $qcos_key);

        if( $scan ) {
            $scan->save($params);
        } else {
            $params['merchant_id'] = $merchantId;
            $params['merchant_third_id'] = $thirdId;
            $scanModel->save($params);
        }
        return true;
    }

}
