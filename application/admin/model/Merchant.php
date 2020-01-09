<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Merchant extends Model
{

    use SoftDelete;

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'merchant';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];


    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1'), '2' => __('Status 2')];
    }



    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function merchantproperties()
    {
        return $this->hasOne('app\admin\model\merchant\MerchantProperties', 'merchant_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function merchantpropertieshotel()
    {
        return $this->hasOne('app\admin\model\merchant\MerchantPropertiesHotel', 'merchant_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function pmerchant()
    {
        return $this->belongsTo('app\admin\model\Merchant', 'pid', 'id', [], 'LEFT')->setEagerlyType(0)->selfRelation();
    }

    public function third()
    {
        return $this->hasOne('app\admin\model\merchant\Third', 'merchant_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function merchantscancode()
    {
        return $this->hasMany('app\admin\model\merchant\MerchantScancode', 'merchant_id', 'id');
    }

}
