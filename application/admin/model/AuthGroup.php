<?php

namespace app\admin\model;

use think\Model;

class AuthGroup extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    const GROUP_TYPE_PLATFORM = 1;
    const GROUP_TYPE_MERCHANT = 2;

    public function getNameAttr($value, $data)
    {
        return __($value);
    }

    public function getGroupTypeList()
    {
        return ['1' => __('Platform Type'), '2' => __('Merchant Type')];
    }


}
