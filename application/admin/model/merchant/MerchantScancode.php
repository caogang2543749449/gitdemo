<?php

namespace app\admin\model\merchant;

use think\Model;

class MerchantScancode extends Model
{

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'merchant_scancode';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
    ];

    public function merchant()
    {
        return $this->belongsTo('app\admin\model\Merchant', 'id', 'merchant_id', [], 'LEFT')->setEagerlyType(0);
    }

    public function merchantthird()
    {
        return $this->belongsTo('app\admin\model\merchant\Third', 'id', 'merchant_third_id', [], 'LEFT')->setEagerlyType(0);
    }

}
