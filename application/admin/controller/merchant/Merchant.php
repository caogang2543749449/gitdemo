<?php

namespace app\admin\controller\merchant;

use app\admin\model\Sequence;
use app\common\controller\Backend;
use fast\Http;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Log;

/**
 * 商家管理
 *
 * @icon fa fa-hotel
 */
class Merchant extends Backend
{
    
    /**
     * Merchant模型对象
     * @var \app\admin\model\Merchant
     */
    protected $model = null;

    /*
     * 采番种类
     */
    protected $seqName = 'merchant';

    //定义快速搜索字段
    protected $searchFields = 'id,merchant_name,merchantproperties.url,merchantproperties.email,merchantproperties.tel,pmerchant.merchant_name';

    /**
     * 绑定商家查询语句
     */
    protected $merchantWhere = "1 = 1";

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Merchant;
        $productList = \app\admin\model\Third::getProductList();
        $this->view->assign("merchantTypeList", $productList);
        $this->assignConfig("merchantTypeList", $productList);
        $hotelTypeList = \app\admin\model\merchant\MerchantPropertiesHotel::getHotelTypeList();
        $this->view->assign("hotelTypeList", $hotelTypeList);
        $this->assignConfig("hotelTypeList", $hotelTypeList);

        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("servicesList", \app\admin\model\merchant\MerchantPropertiesHotel::getServicesList());
        //初始化绑定商家查询语句
        if( session('?org_merchant_id') ) {
            $merchantId = session('org_merchant_id');
            $this->merchantWhere = "(merchant.id = ".$merchantId." or merchant.pid = ".$merchantId.")";
        }
        //初始化选中的商家
        $mid = session('?merchant') ? session('merchant')['id'] : 0;
        $this->assignconfig('merchant_id', $mid);

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
                    ->with(['merchantproperties','merchantpropertieshotel','pmerchant'])
                    ->where($where)
                    ->whereRaw($this->merchantWhere)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['merchantproperties','merchantpropertieshotel','pmerchant'])
                    ->where($where)
                    ->whereRaw($this->merchantWhere)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                $row->visible(['id','merchant_name','merchant_localname','merchant_type','status','createtime']);
                $row->visible(['merchantproperties']);
				$row->getRelation('merchantproperties')->visible(['url','tel','email']);
                $row->visible(['merchantpropertieshotel']);
                $row->getRelation('merchantpropertieshotel')->visible(['hotel_type']);
                $pmerchant = $row->getRelation('pmerchant');
                if($pmerchant) {
                    $row->visible(['pmerchant']);
                    $pmerchant->visible(['id','merchant_name']);
                }
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
                        //验证商户表,商户属性与商户酒店属性应包含在商户表验证器中
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $merchant_id = Sequence::instance()->getNewSequence($this->seqName);
                    $params['id'] = $merchant_id;
                    $params['merchant_card'] = $this->createMerchantCard($params);
                    $result = $this->model->allowField(true)->together(['MerchantProperties','MerchantPropertiesHotel'])->save($params);

                    //增加默认第三方设置
                    $merchantType = $params['merchant_type'];
                    $merchantThirdModel = model('third','model\\merchant');
                    $sysThirds = model('third')->where('product', '=', $merchantType)->select();
                    foreach ($sysThirds as $third) {
                        $merchantThird = $third->getdata();
                        $merchantThird['merchant_id'] = $merchant_id;
                        unset($merchantThird['id']);
                        unset($merchantThird['product']);
                        unset($merchantThird['createtime']);
                        unset($merchantThird['updatetime']);
                        $merchantThirdModel->allowField(true)->save($merchantThird);
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
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        if( $ids ) {
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

            $this->view->assign("row", $row);
        }

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
                    $params['merchant_card'] = $this->createMerchantCard($params);
                    $result = $row->allowField(true)->together(['MerchantProperties','MerchantPropertiesHotel'])->save($params);
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
                    if (session('?merchant') && session('merchant')['id']==$row->getAttr("id")) {
                        session('merchant', $row->toArray());
                        session_commit();
                    }
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
     * 回收站
     * @throws \think\Exception
     */
    public function recyclebin()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->onlyTrashed()
                ->where($where)
                ->whereRaw($this->merchantWhere)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->onlyTrashed()
                ->where($where)
                ->whereRaw($this->merchantWhere)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {
                $row->visible(['merchant_id','merchant_name','deletetime']);
            }

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 真实删除
     */
    public function destroy($ids = "")
    {
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        $merP = $this->model->merchantproperties();
        $merPH = $this->model->merchantpropertiesHotel();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        if ($ids) {
            $this->model->where($pk, 'in', $ids);
            $merP->where($merP->getPk(), 'in', $ids);
            $merPH->where($merPH->getPk(), 'in', $ids);
            $count = 0;
            Db::startTrans();
            try {
                $list = $this->model->onlyTrashed()->select();
                foreach ($list as $k => $v) {
                    $count += $v->delete(true);
                }
                $list2 = $merP->select();
                foreach ($list2 as $k => $v) {
                    $count += $v->delete(true);
                }
                $list3 = $merPH->select();
                foreach ($list3 as $k => $v) {
                    $count += $v->delete(true);
                }
                Db::commit();
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($count) {
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }


    /**
     * 获取店铺的微信小程序码
     * @throws \think\Exception
     */
    public function wxacode($ids = null) {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        //小程序设定取得
        $row = $this->model
            ->with('third')
            ->whereRaw('third.third_type=1')
            ->find($ids);

        $third = null;
        if ( $row ) {
            //小程序已设定
            $third = $row->getRelation('third');
        } else {
            //小程序未设定，取出父商家小程序
            $pid = $this->model->get($ids)->getAttr('pid');
            $pRow = $this->model
                ->with('third')
                ->whereRaw('third.third_type=1')
                ->find($pid);
            if( $pRow ) {
                //父商家小程序已设定
                $third = $pRow->getRelation('third');
            }
        }
        if (!$third ) {
            $this->view->assign('result', __("No_wechat"));
        } else {
            //小程序已设定，取出的小程序二维码
            $thirdId = $third->id;
            $thirdUptime = $third->getAttr('updatetime');
            $url = null;
            $row = $this->model->find($ids)
                ->merchantscancode()
                ->where('merchant_third_id', $thirdId)
                ->find();
            if( $row ) {
                $codeUptime = $row->getAttr('third_updatetime');
                if( $thirdUptime == $codeUptime ) {
                    $url = $row->getAttr('url');
                }
            }
            // 小程序二维码不存在或者小程序二维码的第三方设置更新时间与第三方应用的最后更新时间不一致的时候，通过微信API取得小程序码
            if( is_null($url) ) {
                $appId = $third->getAttr('appid');
                $appSecret = $third->getAttr('app_secret');
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
                $accessToken = json_decode($res)->access_token;
                $miniCodeUrl = 'https://api.weixin.qq.com/wxa/getwxacode?access_token=' . $accessToken;
                $postJson = '{"path" : "pages/index/index?merchantId=' . $ids . '", "width" : 1280}';
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
                $filename = md5($ids.'1'.$appId).'.jpg';
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
                    $this->error('Get scan-code failed');
                }

                // 小程序码图片路径存到DB
                $url = $filepath.$filename.'?'.time();
                $qcos_key = $filepath.$filename;
                \think\Hook::listen("upload_to_qcos", $qcos_key);
                $scanModel = model('merchant_scancode','model\\merchant');

                $params = [];
                $params['url'] = $url;
                $params['third_updatetime'] = $thirdUptime;

                if( $row ) {
                    $row->save($params);
                } else {
                    $params['merchant_id'] = $ids;
                    $params['merchant_third_id'] = $thirdId;
                    $scanModel->save($params);
                }
            }
            $this->view->assign('wxaCode', $url);
        }
        return $this->view->fetch();
    }

    /**
     * 选择需要设置的商家
     * @throws \think\Exception
     */
    public function selectmerchant($ids = null)
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        $row = $this->model->with(['merchantproperties','merchantpropertieshotel'])->find($ids);
        if (!$row) {
            $this->error($this->model->getError());
        }
        session('merchant', $row->toArray());
        session_commit();
        $this->success(__('Merchant %s is selected', $row->getAttr("merchant_name")));
    }
    /**
     * 选择需要设置的商家
     * @throws \think\Exception
     */
    public function unselectmerchant()
    {
        //设置过滤方法
        session('merchant', null);
        session_commit();
        $this->success(__('Merchant is unselected'));
    }

    private function createMerchantCard($merchant)
    {
        //创建画布
        $im = imagecreatetruecolor(1500, 800);

        //填充画布背景色
        $bgColor = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $bgColor);

        //绘制卡片背景
        list($l_w,$l_h) = getimagesize(__DIR__.'/res/card.png');
        $cardImg = @imagecreatefrompng(__DIR__.'/res/card.png');
        imagecopyresized($im, $cardImg, 0, 0, 0, 0, 1500, 800, $l_w, $l_h);

        //绘制商家logo
        //取得logo图片地址(根据本地地址还是cdn地址生成logo的全路径)
        $logourl = $merchant['MerchantProperties']['logoimage'];
        $cdnurl = $this->view->config['upload']['cdnurl'];
        $localFile = false;
        if( !preg_match("/^((?:[a-z]+:)?\\/\\/|data:image\\/)/i", $logourl) ) {
            if($cdnurl!=='') {
                $logourl = $cdnurl.$logourl;
            } else {
                $logourl = ROOT_PATH . '/public' . $merchant['MerchantProperties']['logoimage'];
                $localFile = true;
            }
        }
        //判断logo文件是够存在
        $fileExists = false;
        if( $localFile ) {
            $fileExists = file_exists($logourl);
        } else {
            $curl = curl_init($logourl);
            // 不取回数据
            curl_setopt($curl, CURLOPT_NOBODY, true);
            // 发送请求
            $result = curl_exec($curl);
            // 如果请求没有发送失败
            if ($result !== false) {
                $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($http_status == 200) {
                    $fileExists = true;
                }
            }
        }
        //读取图片并绘制
        $infoStartX = 140;
        if( $fileExists ) {
            //有logo，文字信息右移
            $infoStartX = 500;
            $fileExt = pathinfo($logourl, PATHINFO_EXTENSION);
            $imageLoadMethod = 'imagecreatefrom'.($fileExt==='jpg'?'jpeg':$fileExt);
            if( function_exists($imageLoadMethod) ) {
                $drawMax = 300; //长边300像素
                list($orgW, $orgH) = getimagesize($logourl);
                $drawW = $orgW>=$orgH?$drawMax:($orgW/($orgH/$drawMax));
                $drawH = $orgW>=$orgH?($orgH/($orgW/$drawMax)):$drawMax;
                $logoImg = null;
                eval('$logoImg = @'.$imageLoadMethod.'($logourl);');
                imagecopyresampled($im, $logoImg, 100, 320-$drawH/2, 0, 0, $drawW, $drawH, $orgW, $orgH);
            }
        }

        //绘制商家的信息
        //加载字体、设定文字色彩
        $jpFont = __DIR__.'/res/meiryo.ttc';
        $cnFont = __DIR__.'/res/msyh.ttc';
        $mainColor = ImageColorAllocate ($im, 230, 180, 34);
        $subColor = ImageColorAllocate ($im, 80, 80, 80);
        $infoColor = ImageColorAllocate ($im, 130, 130, 130);

        $fontMethod = 'imagefttext';
        if( IS_WIN ) {
            $fontMethod = 'imagettftext';
        }
        //当地语言的商家店名
        $mainFontSize = 50;
        if( strlen($merchant['merchant_localname']) >= 14 ) {
            $mainFontSize = 40;
        }
        eval($fontMethod.'($im, $mainFontSize, 0, $infoStartX, 200, $mainColor, $jpFont, $merchant[\'merchant_localname\']);');
        //中文商家店名
        eval($fontMethod.'($im, 40, 0, $infoStartX, 280, $subColor, $cnFont, $merchant[\'merchant_name\']);');
        if($merchant['MerchantProperties']['address4']!=='') {
            //邮编
            eval($fontMethod.'($im, 40, 0, $infoStartX, 460, $infoColor, $jpFont, \'〒\'.$merchant[\'MerchantProperties\'][\'postcode\']);');
            //地址
            eval($fontMethod.'($im, 40, 0, $infoStartX, 530, $infoColor, $jpFont, $merchant[\'MerchantProperties\'][\'address1\'].$merchant[\'MerchantProperties\'][\'address2\'].$merchant[\'MerchantProperties\'][\'address3\']);');
            //建筑名
            eval($fontMethod.'($im, 40, 0, $infoStartX, 600, $infoColor, $jpFont, $merchant[\'MerchantProperties\'][\'address4\']);');
            //电话
            eval($fontMethod.'($im, 40, 0, $infoStartX, 670, $infoColor, $jpFont, \'TEL : \'.$merchant[\'MerchantProperties\'][\'tel\']);');
        } else {
            //邮编
            eval($fontMethod.'($im, 40, 0, $infoStartX, 490, $infoColor, $jpFont, \'〒\'.$merchant[\'MerchantProperties\'][\'postcode\']);');
            //地址
            eval($fontMethod.'($im, 40, 0, $infoStartX, 560, $infoColor, $jpFont, $merchant[\'MerchantProperties\'][\'address1\'].$merchant[\'MerchantProperties\'][\'address2\'].$merchant[\'MerchantProperties\'][\'address3\']);');
            //电话
            eval($fontMethod.'($im, 40, 0, $infoStartX, 630, $infoColor, $jpFont, \'TEL : \'.$merchant[\'MerchantProperties\'][\'tel\']);');
        }

        //输出图片
        $uploadurl = $this->view->config['upload']['uploadurl'];
        $filename = md5($merchant['id']).'.png';
        $filepath = '/uploads/merchant_card/';
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
        imagesavealpha($im, true);
        imagepng ($im,$filefullpath.$filename);

//        $im90 = imagerotate($im, 270, null);
//        imagepng ($im90,$filefullpath.'90_'.$filename);

        //释放空间
        imagedestroy($im);
//        imagedestroy($im90);
        imagedestroy($cardImg);
        if( isset($logoImg) ) {
            imagedestroy($logoImg);
        }

        $qcos_key = $filepath.$filename;
        \think\Hook::listen("upload_to_qcos", $qcos_key);
        return $filepath.$filename.'?'.time();
    }

}
