<?php

namespace app\admin\validate\merchant;

use think\Validate;

class Merchant_module extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'module_id' => 'require|unique:merchant_module,merchant_id^module_id'
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
            'module_id' => __('Module')
        ];
        parent::__construct($rules, $message, $field);
    }
    
}
