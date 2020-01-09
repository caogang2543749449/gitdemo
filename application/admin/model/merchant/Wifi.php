<?php

namespace app\admin\model\merchant;

use think\Model;
use traits\model\SoftDelete;

class Wifi extends Model
{

    use SoftDelete;

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'merchant_wifi';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'wifi_type_text',
        'verify_type_text'
    ];
    
    
    public function getWifiTypeList()
    {
        return ['normal' => __('Normal'), 'dynamic' => __('Dynamic')];
    }

    
    public function getVerifyTypeList()
    {
        return ['1' => __('Verify_type 1'), '2' => __('Verify_type 2'), '3' => __('Verify_type 3')];
    }


    public function getWifiTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['wifi_type']) ? $data['wifi_type'] : '');
        $list = $this->getWifiTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getVerifyTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['verify_type']) ? $data['verify_type'] : '');
        $list = $this->getVerifyTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function merchant()
    {
        return $this->belongsTo('app\admin\model\Merchant', 'merchant_id', 'id')->setEagerlyType(0);
    }


}
