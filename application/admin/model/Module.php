<?php

namespace app\admin\model;

use think\Model;


class Module extends Model
{

    

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'module';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'target_type_text',
    ];

    public function getTargetTypeList()
    {
        return ['url' => __('Target_type url'), 'app' => __('Target_type app'), 'method' => __('Target_type method')];
    }


    public function getTargetTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['target_type']) ? $data['target_type'] : '');
        $list = $this->getTargetTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    

    public function merchant()
    {
        return $this->belongsTo('Merchant', 'merchant_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    







}
