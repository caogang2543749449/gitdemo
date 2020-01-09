<?php

namespace app\admin\controller\merchant;

use app\common\controller\Backend;
use fast\Http;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 商家管理
 *
 * @icon fa fa-hotel
 */
class MerchantProp extends Backend
{
    
    /**
     * Merchant模型对象
     * @var \app\admin\model\Merchant
     */
    protected $model = null;

    /**
     * 绑定商家查询语句
     */
    protected $id = 0;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Merchant;
        $this->view->assign("merchantTypeList", \app\admin\model\Third::getProductList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("servicesList", \app\admin\model\merchant\MerchantPropertiesHotel::getServicesList());
        //初始化绑定商家
        if( session('?merchant') ) {
            $this->id = session('merchant')['id'];
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
        $row = $this->model->with(['merchantproperties','merchantpropertieshotel'])->find($this->id);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }

        //设置基本信息
        $this->view->assign("row", $row);
        //简介图片转换为数组
        $descImages = explode(',', $row->getRelation('merchantproperties')->getAttr('descimages'));
        $this->view->assign("descImages", $descImages);
        //提供服务转换为数组
        $hotelServices =explode( ',', $row->getRelation('merchantpropertieshotel')->getAttr('services'));
        $this->view->assign("hotelServices", $hotelServices);
        //设置小程序信息
        $pid = $row->getAttr('pid');
        $search_id = $pid==0? session('merchant')['id']:$pid;
        $wxapp = model('Third', 'model\merchant')
            ->alias('third')
            ->join('MerchantScancode scan', 'scan.merchant_third_id = third.id')
            ->where([
                'third.merchant_id' => $search_id,
                'scan.merchant_id' => session('merchant')['id'],
                'third_type' => 1
            ])
            ->field('scan.url as url')
            ->find();
        if( $wxapp ) {
            $this->view->assign('scancode', $wxapp->url);
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
                    $this->success('', 'merchant/merchant_prop/index');
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
     * 生成酒店信息卡
     * @param $merchant
     * @return string
     */
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
