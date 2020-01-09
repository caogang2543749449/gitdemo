<?php

namespace app\admin\model\service;

use think\Model;
use traits\model\SoftDelete;

class Goods extends Model
{

    use SoftDelete;

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    







    public function category()
    {
        return $this->belongsTo('app\admin\model\service\Category', 'category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function merchant()
    {
        return $this->belongsTo('app\admin\model\Merchant', 'merchant_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
