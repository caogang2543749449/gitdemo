<?php

namespace app\common\library;

use app\admin\model\Third;
use fast\Http;
use think\Log;

class NihaoPayUtil
{
    const STATUS_SUCCESS = 'success';

    protected static $instance = null;

    protected $timeout = 10;
    protected $baseUrl = '';
    protected $refundUri = '/%s/refund';
    protected $checkUri = '/merchant/%s';
    protected $curlOptions = [];

    public function __construct($merchantId)
    {
        $third = null;
        if($merchantId===0) {
            $third = model('third')
                ->where('product', '=', Third::PRODUCT_COMMON)
                ->where('third_type', '=', Third::THIRD_NIHAO_PAY)
                ->find();
        } else {
            $third = model('third', 'model\\merchant')
                ->where('merchant_id', '=', $merchantId)
                ->where('third_type', '=', Third::THIRD_NIHAO_PAY)
                ->find();
            if (!$third) {
                $third = model('third')
                    ->where('product', '=', Third::PRODUCT_COMMON)
                    ->where('third_type', '=', Third::THIRD_NIHAO_PAY)
                    ->find();
            }
        }
        $this->baseUrl = $third->getAttr('appurl');
        $this->curlOptions = [
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer '.$third->getAttr('app_secret'), 'Content-Type：application/x-www-form-urlencoded']
        ];
    }

    /**
     *
     * @param array $options 参数
     * @return NihaoPayUtil
     */
    public static function instance($merchantId = 0)
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($merchantId);
        }

        return self::$instance;
    }

    /**
     * 查询NihaoPay指定订单号的交易状态
     * @param $nihaoTransSn
     * @return array
     */
    public function checkPaidStatus($orderSn)
    {
        $url = $this->baseUrl.sprintf($this->checkUri, $orderSn);
        $res = Http::get($url, [], $this->curlOptions);
        if(!$res) {
            Log::error(__('Network error'));
            return [false, __('Network error')];
        }
        $result = json_decode($res);
        if(!$result) {
            Log::error(__('Unknown data format'));
            return [false, __('Unknown data format')];
        }
        if(isset($result->code) && $result->code!='200'){
            $errmsg = 'Nihaopay Error : '.$result->label.' '.$result->message;
            Log::error($errmsg);
            return [false, $errmsg];
        }
        if(self::STATUS_SUCCESS === $result->status){
            return [$result->id, $result->time];
        }
        return [false, __($result->status)];
    }

    /**
     * 发起退款
     * @param $paidId string 交易成功时返回的流水号
     * @param $amount int 退款金额，必须小于等于订单支付金额
     * @param $reason string 退款理由
     * @return bool|mixed
     */
    public function orderRefund($paidId, $amount, $reason)
    {
        $url = $this->baseUrl.sprintf($this->refundUri, $paidId);
        $postJson = '{"currency" : "JPY", "amount" : '.$amount.', "reason" : "'.$reason.'"}';
        $postData = [
            "currency" => "JPY",
            "amount" => $amount,
            "reason" => $reason
        ];

        $res = Http::post($url, $postData, $this->curlOptions);
        if(!$res) {
            Log::error(__('Network error'));
            return [false, __('Network error')];
        }
        $result = json_decode($res);
        if(!$result) {
            Log::error(__('Unknown data format'));
            return [false, __('Unknown data format')];
        }
        if(self::STATUS_SUCCESS === $result->status){
            return [true, $result->id];
        }
        return [false, __('Refund failure')];
    }
}
