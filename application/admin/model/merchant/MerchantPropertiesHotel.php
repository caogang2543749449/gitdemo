<?php

namespace app\admin\model\merchant;

use think\Model;


class MerchantPropertiesHotel extends Model
{

    

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'merchant_properties_hotel';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'services_text'
    ];


    public static function getServicesList()
    {
        return ['1' => __('Services 1'), '2' => __('Services 2'), '3' => __('Services 3'), '4' => __('Services 4'), '5' => __('Services 5'), '6' => __('Services 6'), '7' => __('Services 7'), '8' => __('Services 8')];
    }

    public static function getHotelTypeList()
    {
        return ['1' => __('Normal Hotel'), '2' => __('High-End Hotel')];
    }


    public function getServicesTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['services']) ? $data['services'] : '');
        $valueArr = explode(',', $value);
        $list = $this->getServicesList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }

    protected function setServicesAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }


}
