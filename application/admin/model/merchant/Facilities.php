<?php

namespace app\admin\model\merchant;

use think\Model;
use traits\model\SoftDelete;

class Facilities extends Model
{

    use SoftDelete;

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'merchant_facilities';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $row->save(['weigh' => $row['id']]);
        });
    }

    // 追加属性
    protected $append = [
        'service_time_text'
    ];
    
    public function getServiceTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['service_time']) ? $data['service_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setServiceTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    
    public static function getTagList()
    {
        return ['room' => __('Room'), 'private' => __('Private'), '24H' => __('24H'), 'free' => __('Free'), 'childcare' => __('Childcare'), 'chinese' => __('Chinese')];
    }

    public function merchant()
    {
        return $this->belongsTo('app\admin\model\Merchant', 'merchant_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
