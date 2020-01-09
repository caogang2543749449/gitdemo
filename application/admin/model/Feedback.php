<?php

namespace app\admin\model;

use think\Model;


class Feedback extends Model
{

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'feedback';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
    ];
    

    
    public static function getScoreDesignList()
    {
        return ['' => '', '-1' => __('DesignUnlike'), '0' => __('DesignNormal'), '1' => __('DesignLike')];
    }
    

    public function merchant()
    {
        return $this->belongsTo('app\admin\model\Merchant', 'merchant_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    







}
