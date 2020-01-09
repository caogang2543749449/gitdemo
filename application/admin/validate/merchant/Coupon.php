<?php

namespace app\admin\validate\merchant;

use think\Validate;

class Coupon extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'title'  =>  'require|max:100',
        'store_name'  =>  'max:100',
        'store_logo_image'  =>  'max:255',
        'store_address'  =>  'max:255',
        'gis_coord'  =>  'max:50',
        'qr_image'  =>  'max:255',
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
        $this->field   = [
            'title'  => __('Title'),
            'store_name'  => __('Store_name'),
            'store_logo_image'  => __('Store_logo_image'),
            'store_address'  => __('Store_address'),
            'gis_coord'  => __('Gis_coord'),
            'qr_image'  => __('Qr_image'),
        ];
        parent::__construct($rules, $message, $field);
    }
}
