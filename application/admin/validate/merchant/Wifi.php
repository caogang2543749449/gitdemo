<?php

namespace app\admin\validate\merchant;

use think\Validate;

class Wifi extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'ssid' => 'require|max:40|unique:merchant_wifi,merchant_id^ssid',
        'security_key' => 'max:60',
        'description' => 'max:200',
        'verify_url' => 'max:400',
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
            'ssid' => __('Ssid'),
            'security_key' => __('security_key'),
            'description' => __('description'),
            'verify_url' => __('verify_url'),
        ];
        parent::__construct($rules, $message, $field);
    }
    
}
