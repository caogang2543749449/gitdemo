<?php

namespace app\admin\validate\general;

use think\Validate;

class Regions extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'code'  =>  'require|max:10',
        'prefecture'  =>  'max:10',
        'city'  =>  'max:15',
        'ward'  =>  'max:20',
        'gis_coord'  =>  'max:50',
        'level'  =>  'max:1',
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
            'code'  => __('Code'),
            'prefecture'  => __('Prefecture'),
            'city'  => __('City'),
            'ward'  => __('Ward'),
            'gis_coord'  => __('Gis_coord'),
            'level'  => __('Level'),
        ];
        parent::__construct($rules, $message, $field);
    }
    
}
