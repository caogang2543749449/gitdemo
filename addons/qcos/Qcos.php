<?php

namespace addons\qcos;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Qcos extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        
        return true;
    }
    /**
     * 实现钩子方法
     * @return mixed
     */
    public function uploadAfter($attachment){
        $client = CosClient::getinstance();
        $client->uploadAttachment($attachment->url);
    }

    public function uploadToQcos($url) {
        $client = CosClient::getinstance();
        $client->upload($url);
    }
}
