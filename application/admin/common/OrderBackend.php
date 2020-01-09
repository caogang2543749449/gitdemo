<?php

namespace app\admin\common;

/**
 * 订单类共通后台基类
 * 订单状态:
 * 101=下单待支付,
 * 201=用户已支付,
 * 301=商家已配送, 302=用户不在房间
 * 401=用户确认收货, 402=系统自动收货,
 * 501=已退款
 * 601=用户取消, 602=超时系统自动取消,
 * 999=交易异常
 *
 * 订单表示状态：
 * 1XX：待支付
 * 2XX：待发货
 * 3XX：待收货
 * 4XX：已完成
 * 5XX：已退款
 * 6XX：已取消
 * 9XX：异常
 *
 * 操作权限：
 * 购物车下单后状态置为待支付，但如订单价格为0（免费服务）则状态自动置为已支付
 * 1XX：用户可取消订单、或者支付；发生支付故障时，商家可点击确认按钮从nihaopay刷新状态为已支付
 * 2XX：商家可更改状态为已发货，或执行退款
 * 3XX：用户可确认收货，商家更改状态为用户不在，或可执行退款
 * 4XX：用户可删除订单（用户端不显示），商家可执行退款，未来支持评论时用户可评价商品（限制评论数量为1）
 * 5XX，6XX：用户可删除订单（用户端不显示）
 *
 * 关于订单号（32位长）
 * 规则：生成订单瞬间的17位毫秒级字符串时间戳+10位商家ID+5位随机数
 * 格式：yyyyMMddhhmmssSSS0000000000XXXXX
 * 例：20190902102259234000000001682232
 * 说明：商家ID为16（左补0）的商家于2019年9月2日10点22分59秒234毫秒生成的订单，生成时附加随机数82232
 */
class OrderBackend extends MerchantBackend
{
    const STATUS_NEW = '101';
    const STATUS_PAID = '201';
    const STATUS_DELIVERED = '301';
    const STATUS_USER_MISSED = '302';
    const STATUS_RECEIVED = '401';
    const STATUS_RECEIVED_AUTO = '402';
    const STATUS_REFUNDED = '501';
    const STATUS_CANCELED = '601';
    const STATUS_CANCELED_AUTO = '602';
    const STATUS_ILLEGAL = '999';

    const ACTION_NEW = 'Action 101';
    const ACTION_PAID = 'Action 201';
    const ACTION_DELIVERED = 'Action 301';
    const ACTION_REDELIVER = 'Action 302';

    /**
     * 服务状态列表。
     * @return array
     */
    public static $statusList =[
        self::STATUS_NEW ,
        self::STATUS_PAID,
        self::STATUS_DELIVERED,
        self::STATUS_USER_MISSED,
        self::STATUS_RECEIVED,
        self::STATUS_RECEIVED_AUTO,
        self::STATUS_REFUNDED,
        self::STATUS_CANCELED,
        self::STATUS_CANCELED_AUTO,
        self::STATUS_ILLEGAL
    ];

    /**
     * 可取消服务状态列表。
     * @return array
     */
    public static $cancelableList =[
        self::STATUS_NEW,
    ];

    /**
     * 可退款服务状态列表。
     * @return array
     */
    public static $refundableList =[
        self::STATUS_PAID,
        self::STATUS_DELIVERED,
        self::STATUS_USER_MISSED,
        self::STATUS_RECEIVED,
        self::STATUS_RECEIVED_AUTO,
    ];

    public static $merchantRolloverList =[
        self::STATUS_NEW => [self::STATUS_PAID => self::ACTION_NEW],
        self::STATUS_PAID => [self::STATUS_DELIVERED => self::ACTION_PAID],
        self::STATUS_DELIVERED => [self::STATUS_USER_MISSED => self::ACTION_DELIVERED],
        self::STATUS_USER_MISSED => [self::STATUS_DELIVERED => self::ACTION_REDELIVER],
    ];

    public static $userRolloverList =[
        self::STATUS_NEW => self::STATUS_PAID,
        self::STATUS_PAID => self::STATUS_CANCELED,
        self::STATUS_DELIVERED => self::STATUS_RECEIVED,
        self::STATUS_USER_MISSED => self::STATUS_RECEIVED,
        self::STATUS_RECEIVED => self::STATUS_RECEIVED
    ];

    public static $autoRollList =[
        self::STATUS_NEW => self::STATUS_CANCELED_AUTO,
        self::STATUS_DELIVERED => self::STATUS_RECEIVED_AUTO,
    ];

    //退款原因
    const REFUND_WRONG_PRODUCT = '1';
    const REFUND_WRONG_QUANTITY = '2';
    const REFUND_DELIVERY_MISS = '3';
    const REFUND_EXPIRED = '4';
    const REFUND_DAMAGED = '5';
    const REFUND_OUT_OF_STACK = '6';
    const REFUND_PERSONAL_REASONS = '7';
    const REFUND_OTHER_REASONS = 'O'; //alphabet 'O', not zero

    /**
     * 退款原因列表。
     * @return array
     */
    public static $refundReasonList =[
        self::REFUND_WRONG_PRODUCT => 'Purchased wrong product',
        self::REFUND_WRONG_QUANTITY => 'Purchased wrong quantity',
        self::REFUND_DELIVERY_MISS => 'Delivery miss',
        self::REFUND_EXPIRED => 'Product expired',
        self::REFUND_DAMAGED => 'Product damaged',
        self::REFUND_OUT_OF_STACK => 'Personal reasons',
        self::REFUND_PERSONAL_REASONS => 'Out of stock',
        self::REFUND_OTHER_REASONS => 'Other reasons',
    ];

    /**
     * 取得下一步状态
     * @param $currentStep
     * @return array|bool
     */
    public function getMerchantNextStep($currentStep)
    {
        if(key_exists($currentStep, self::$merchantRolloverList)) {
            return self::$merchantRolloverList[$currentStep];
        }
        return false;
    }

    /**
     * 交易状态是否可交易取消
     * @param $currentStep
     * @return bool
     */
    public function canCancel($currentStep)
    {
        if( in_array($currentStep, self::$cancelableList) ) {
            return true;
        }
        return false;
    }

    /**
     * 交易状态是否可退款
     * @param $currentStep
     * @return bool
     */
    public function canRefund($currentStep)
    {
        if( in_array($currentStep, self::$refundableList) ) {
            return true;
        }
        return false;
    }

    /**
     * 交易状态是否有错
     * @param $currentStep
     * @return bool
     */
    public function isError($currentStep)
    {
        if( $currentStep==self::STATUS_ILLEGAL ) {
            return true;
        }
        return false;
    }

}
