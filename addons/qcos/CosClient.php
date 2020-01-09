<?php

namespace addons\qcos;

use Qcloud\Cos\Client;
use think\Addons;
use think\Config;

class CosClient
{
    private static $instance = null;
    
    public static function getInstance() {
        if(self::$instance == null) {
            self::$instance = new CosClient();
        }
        return self::$instance;
    }

    private $client = null;
    private $bucket = '';
    private $appId  = '';
    private $path  = '';

    private function __construct() {

        $configs = get_addon_config('qcos');
        $secretId = $configs['SecretId'];
        $secretKey = $configs['SecretKey'];
        $region = $configs['region'];
        $this->appId = $configs['appId'];
        $this->path = $configs['path'];
        $this->bucket = $configs['bucket'] . '-' . $this->appId;
        $this->client = new \Qcloud\Cos\Client(
            array(
                'region' => $region,
                'schema' => 'https',
                'credentials'=> array(
                    'appId'  => $this->appId,
                    'secretId'  => $secretId,
                    'secretKey' => $secretKey)));
    }

    public function upload($filepath) {
        $localPath = ROOT_PATH.'public'.$filepath;
//        ImageHelper::getInstance()->resize_image($localPath, 414, 288);

        $key = str_replace("//", "/", DS . $this->path . DS . $filepath);
        $body = fopen($localPath, 'rb');
        // exit;
        $this->createBucketIfNeeded();
        try {
            return $this->client->upload($this->bucket, $key, $body);
        } catch (\Exception $e) {
            // 请求失败
        }
        return null;
    }

    public function uploadAttachment($attachmentUrl) {
        $result = $this->upload($attachmentUrl);
        if(!$result || !isset($result['RequestId'])){
            return;
        }
        $params = array(
            'storage'     => 'qcos',
        );
        $model = model("attachment");
        $attachment = $model->where('url', $attachmentUrl)->find();
        if (!$attachment) {
            return;
        }
        $attachment->allowField(true)->save($params);
    }

    private function createBucketIfNeeded() {
        try {
            //请求成功
            $result = $this->client->doesBucketExist($this->bucket);
            if($result) {
                return;
            }
            $this->client->createBucket(array('Bucket' => $this->bucket, 'ACL' => 'public-read'));
        } catch (\Exception $e) {
        }
    }

}
