<?php

namespace app\admin\model;

use think\Model;


class MerchantMsg extends Model
{

    

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'merchant_msg';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'msg_type_text',
        'status_text'
    ];
    

    
    public function getMsgTypeList()
    {
        return ['msg' => __('Msg_type msg'), 'lost' => __('Msg_type lost')];
    }

    public function getStatusList()
    {
        return ['1' => __('Status 1'), '2' => __('Status 2'), '3' => __('Status 3'), '9' => __('Status 9')];
    }


    public function getMsgTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['msg_type']) ? $data['msg_type'] : '');
        $list = $this->getMsgTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
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
}
