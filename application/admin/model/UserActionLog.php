<?php

namespace app\admin\model;

use think\Model;

class UserActionLog extends Model
{

    // 表名
    protected $name = 'user_action_log';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        'action_type_text',
    ];

    public function getActionTypeList()
    {
        return ['scan_open' => __('Action_type scan_open'), 'normal_open' => __('Action_type normal_open'), 'button_click' => __('Action_type button_click'), 'close' => __('Action_type close'), 'share' => __('Action_type share')];
    }

    public function getActionTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['action_type']) ? $data['action_type'] : '');
        $list = $this->getActionTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


}
