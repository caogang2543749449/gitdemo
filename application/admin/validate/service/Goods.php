<?php

namespace app\admin\validate\service;

use think\Validate;

class Goods extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'attributes' => 'checkNullAttributes',
        'name' => 'max:100|unique:goods,merchant_id^name',
        'local_name' => 'max:200|unique:goods,merchant_id^local_name',
        'goods_sn' => 'max:64|unique:goods,merchant_id^goods_sn',
        'brief' => 'max:500',
        'unit' => 'max:50',
        'labels' => 'max:255',
        'notice' => 'max:1000',
    ];
    /**
     * 提示消息
     */
    protected $message = [
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => [],
        'edit' => [],
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        //验证字段描述
        $this->field = [
            'name' => __('Name'),
            'goods_sn' => __('Goods_sn'),
            'local_name' => __('Local_name'),
            'brief' => __('brief'),
            'unit' => __('unit'),
            'labels' => __('labels'),
            'notice' => __('notice'),
        ];
        parent::__construct($rules, $message, $field);
    }

    // 验证商品属性是否存在空值
    protected function checkNullAttributes($value,$rule,$data)
    {
        if(empty($value)) {
            return true;
        }
        $attributeErr = __('Attribute name and value can not be empty.');
        $attributes = json_decode($value);
        if(is_array($attributes)) {
            foreach ($attributes as $attribute) {
                if(empty($attribute->name) || empty($attribute->value)) {
                    return $attributeErr;
                }
            }
            return true;
        }
        return $attributeErr;
    }
}
