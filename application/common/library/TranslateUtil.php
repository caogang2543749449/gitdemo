<?php

namespace app\common\library;

use app\admin\model\Third;
use fast\Http;
use think\Log;
use think\Config;

class TranslateUtil
{
    const STATUS_SUCCESS = 'success';

    protected static $instance = null;

    protected $timeout = 10;
    protected $curlOptions = [];

    public function __construct()
    {
        $headers = [ 'Content-Type: application/json; charset=utf-8'];
        $this->curlOptions = [
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers 
        ];
    }

    /**
     *
     * @param array $options 参数
     * @return NihaoPayUtil
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     */
    public function translate($postData)
    {
        $url_tmt_wx_api = Config::get('url_tmt_wx_api');
        $url = $url_tmt_wx_api . "/internal/translate/text";
        if(!isset($postData["sourceText"]) || empty($postData["sourceText"]) ) {
            return null;
        }
        if(!isset($postData["source"]) || empty($postData["source"]) ) {
            $postData["source"] = 'jp';
        }
        if(!isset($postData["target"]) || empty($postData["target"]) ) {
            $postData["target"] = 'zh';
        }
        $postJson = json_encode($postData);

        $res = Http::post($url, $postJson, $this->curlOptions);
        if(!$res) {
            Log::error(__('Network error'));
            return [false, __('Network error')];
        }
        return json_decode($res, true);
    }
}
