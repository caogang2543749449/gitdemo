<?php

namespace app\common\library;

use app\admin\model\Third;
use fast\Http;
use think\Log;

class LakalaUtil
{
    const STATUS_SUCCESS = 'SUCCESS';

    protected static $instance = null;

    protected $timeout = 10;
    protected $baseUrl = 'https://payjp.lakala.com/api/v1.0/gateway/partners/';
    protected $refundUri = '%s/orders/%s/refunds/%s';
    protected $checkUri = '%s/orders/%s';
    protected $curlOptions = [];

    public function __construct()
    {
        $this->curlOptions = [
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => array('Accept: application/json', 'Content-Type: application/json'),
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
     * 获取毫秒级别的时间戳
     */
    private function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode(" ", microtime());
		$millisecond = "000".($time[0] * 1000);
		$millisecond2 = explode(".", $millisecond);
		$millisecond = substr($millisecond2[0],-3);
        $time = $time[1] . $millisecond;
        return $time;
    }
    /**
     *
     * 产生随机字符串，不长于30位
     * @param int $length
     * @return $str 产生的随机字符串
     */
    private function getNonceStr($length = 30)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 生成带签名的 Params
     * @return 签名
     */
    public function makeParams($partnerCode, $credentialCode)
    {
        //签名步骤一：构造签名参数
        $time = $this->getMillisecond();
        $nonce_str = $this->getNonceStr();
        $params = $partnerCode . '&' . $time . '&' . $nonce_str . "&" . $credentialCode;;
        //签名步骤三：SHA256加密
        $string = hash('sha256', utf8_encode($params));
        //签名步骤四：所有字符转为小写
        $result = 'time=' . $time . '&nonce_str=' . $nonce_str . '&sign='.strtolower($string);
        return $result;
    }

    public function getMerchantThird($order) {
        $model = new \app\admin\model\merchant\Third;
        $merchantThird = $model
        ->where('merchant_id', $order->merchant_id)
        ->where('third_type', 1)
        ->where('payment', 'LakalaPay')
        ->find();
        return $merchantThird;
    }
    /**
     * 查询NihaoPay指定订单号的交易状态
     * @param $nihaoTransSn
     * @return array
     */
    public function checkPaidStatus($order)
    {
        $merchantThird = $this->getMerchantThird($order);
        if($merchantThird == null) {
            return [false, __('No_payment')];
        }

        $params = $this->makeParams($merchantThird->partner_code, $merchantThird->credential_code);
        $url = $this->baseUrl.sprintf($this->checkUri, $merchantThird->partner_code, $order->order_sn).'?'.$params;
        $res = Http::get($url, [], $this->curlOptions);
        Log::info(array(
            "checkPaidStatus",
            $res
        ));
        if(!$res) {
            Log::error(__('Network error'));
            return [false, __('Network error')];
        }
        $result = json_decode($res);
        if(!$result) {
            Log::error(__('Unknown data format'));
            return [false, __('Unknown data format')];
        }
        if(!isset($result->return_code)){
            $errmsg = 'Lakala Error : '.$result;
            Log::error($errmsg);
            return [false, 'Lakala Error'];
        }
        if(self::STATUS_SUCCESS !== $result->return_code){
            return [false, __('Order Not Found')];
        }
        if('PAY_SUCCESS' === $result->result_code){
            return [$result->order_id, $result->create_time];
        }
        return [false, __($result->result_code)];
    }
    /**
     * 发起退款
     * @param $paidId string 交易成功时返回的流水号
     * @param $amount int 退款金额，必须小于等于订单支付金额
     * @param $reason string 退款理由
     * @return bool|mixed
     */
    public function orderRefund($order, $amount, $reason)
    {
        // $order->paid_id = "K8NJY9-20191126143254175-FXWYTZF";
        $merchantThird = $this->getMerchantThird($order);
        if($merchantThird == null) {
            return [false, __('No_payment')];
        }

        $params = $this->makeParams($merchantThird->partner_code, $merchantThird->credential_code);
        $url = $this->baseUrl.sprintf($this->refundUri, $merchantThird->partner_code, $order->order_sn, $order->paid_id).'?'.$params;
        $postData = json_encode([
            "fee" => $amount
        ]);
        $res = Http::sendRequest($url, $postData, 'PUT', $options = $this->curlOptions);
        $response = $res['ret'] ? $res['msg'] : '';
        
        Log::info(array(
            "orderRefund",
            $response
        ));
        if(!$response) {
            Log::error(__('Network error'));
            return [false, __('Network error')];
        }
        $result = json_decode($response);
        if(!$result) {
            Log::error(__('Unknown data format'));
            return [false, __('Unknown data format')];
        }
        if(self::STATUS_SUCCESS === $result->return_code){
            return [true, $result->refund_id];
        }
        return [false, __('Refund failure')];
    }
}
