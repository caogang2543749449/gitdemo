<?php

namespace app\admin\model\merchant;

use think\Model;


class MerchantModule extends Model
{

    

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'merchant_module';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
    ];


    public function merchant() {
        return $this->belongsTo('app\admin\model\Merchant', 'merchant_id', 'id')->setEagerlyType(0);
    }


}
