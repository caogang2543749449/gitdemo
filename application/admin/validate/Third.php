<?php

namespace app\admin\validate;

use think\Validate;

class Third extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'third_type' => 'require|unique:third,product^third_type',
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
            'third_type' => __('third_type')
        ];
        parent::__construct($rules, $message, $field);
    }

}
