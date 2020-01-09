<?php

namespace app\admin\validate\service;

use think\Validate;

class Category extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'name' => 'require|max:30',
        'nickname' => 'require|max:50',
        'description' => 'max:255',
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
            'nickname' => __('nickname'),
            'description' => __('description'),
        ];
        parent::__construct($rules, $message, $field);
    }
    
}
