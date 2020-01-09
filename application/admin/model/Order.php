<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Order extends Model
{

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'status_text',
        'paid_type_text',
        'deliver_type_text'
    ];


    public function getStatusList()
    {
        return ['1' => __('Status 1'), '2' => __('Status 2'), '3' => __('Status 3'), '4' => __('Status 4'), '5' => __('Status 5'), '6' => __('Status 6'), '7' => __('Status 7'), '8' => __('Status 8'), '9' => __('Status 9')];
    }

    public function getPaidTypeList()
    {
        return ['1' => __('Paid_type 1'), '2' => __('Paid_type 2'), '3' => __('Paid_type 3')];
    }

    public function getDeliverTypeList()
    {
        return ['1' => __('Deliver_type 1'), '2' => __('Deliver_type 2')];
    }



    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPaidTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['paid_type']) ? $data['paid_type'] : '');
        $list = $this->getPaidTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getDeliverTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['deliver_type']) ? $data['deliver_type'] : '');
        $list = $this->getDeliverTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function merchant()
    {
        return $this->belongsTo('Merchant', 'merchant_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function orderGoods()
    {
        return $this->hasMany('app\admin\model\order\OrderGoods', 'order_id', 'id');
    }

    public function orderRefundHis()
    {
        return $this->hasMany('app\admin\model\order\OrderRefundHis', 'order_id', 'id');
    }

}
