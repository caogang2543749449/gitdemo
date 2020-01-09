<?php

namespace app\admin\controller;

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
     * @var \app\admin\model\Third
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();

        $this->modelValidate = true;

        $this->model = new \app\admin\model\Third;
        $this->view->assign("productList", $this->model->getProductList());
        $this->assignconfig("productList", $this->model->getProductList());
        $this->view->assign("thirdTypeList", $this->model->getThirdTypeList());
        $this->assignconfig("thirdTypeList", $this->model->getThirdTypeList());
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
        $row = $this->model->get($ids);
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
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    $this->generateWxaCode($row);
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
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 获取平台制品微信小程序码
     * @param $ids
     */
    public function wxacode($ids = null)
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        //小程序设定取得
        $row = $this->model->find($ids);
        if (!$row ) {
            $this->view->assign('result', __("No_wechat"));
        }

        // 通过微信API取得小程序码
        $appId = $row->getAttr('appid');

        // 生成小程序码图片地址
        $uploadurl = $this->view->config['upload']['uploadurl'];
        $filename = md5($ids.$appId).'.jpg';
        $filepath = '/uploads/sys_scancode/';
        $filefullpath = $filepath;
        if( preg_match("/^((?:[a-z]+:)?\\/\\/|data:image\\/)/i", $uploadurl) ) {
            //use cdn
            //TODO
        } else {
            $filefullpath = ROOT_PATH . '/public' . $filepath;
        }
        if( !file_exists($filefullpath.$filename)){
            $this->generateWxaCode($row);
        }

        $url = $filepath.$filename.'?'.time();
        $this->view->assign('wxaCode', $url);
        return $this->view->fetch();
    }

    /**
     * 获取平台制品微信小程序码
     * @param $row
     */
    private function generateWxaCode($row)
    {

        // 通过微信API取得小程序码
        $appId = $row->getAttr('appid');
        $appSecret = $row->getAttr('app_secret');
        $authUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $appSecret;
        $res = Http::get($authUrl);
        if(!$res) {
            Log::error('Get wechat access_token failed with network error.');
            $this->error('Get scan-code failed');
        }
        $result = json_decode($res);
        if(property_exists($result, 'errcode')) {
            Log::error('Get wechat access_token failed with error-code('.$result->errcode.') : '.$result->errmsg);
            $this->error('Get scan-code failed');
        }
        $accessToken = $result->access_token;
        $miniCodeUrl = 'https://api.weixin.qq.com/wxa/getwxacode?access_token=' . $accessToken;
        $postJson = '{"path" : "pages/index/index", "width" : 1280}';
        $res = Http::post($miniCodeUrl, $postJson, [CURLOPT_TIMEOUT => 10]);
        if(!$res) {
            Log::error('Get wxa-code failed with network error.');
            $this->error('Get scan-code failed');
        }
        $result = json_decode($res);
        if($result) {
            Log::error('Get wxa-code failed with error-code('.$result->errcode.') : '.$result->errmsg);
            $this->error('Get scan-code failed');
        }

        // 保存取得的小程序码
        $uploadurl = $this->view->config['upload']['uploadurl'];
        $filename = md5($row['id'].$appId).'.jpg';
        $filepath = '/uploads/sys_scancode/';
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
            $this->error('Save scan-code failed');
        }

    }

}
