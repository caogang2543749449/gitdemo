<?php

namespace app\admin\validate\merchant;

use think\Validate;

class Facilities extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'facility_name'  =>  'require|max:20',
        'tel'  =>  'number|max:11',
        'address'  =>  'max:200',
        'inner_tel'  =>  'max:6',
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
            'facility_name' => __('facility_name'),
            'tel' => __('tel'),
            'address' => __('address'),
            'inner_tel' => __('inner_tel'),
        ];
        parent::__construct($rules, $message, $field);
    }
    
}
