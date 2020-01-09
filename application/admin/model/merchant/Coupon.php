<?php

namespace app\admin\model\merchant;

use think\Model;
use traits\model\SoftDelete;

class Coupon extends Model
{

    use SoftDelete;

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'merchant_coupon';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'get_rule_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    public function getGetRuleList()
    {
        return ['manual' => __('Manual'), 'scan' => __('Scan'), 'share' => __('Share')];
    }

    public function getUseRuleList()
    {
        return ['scan' => __('Scan'), 'click' => __('Click')];
    }


    public function getGetRuleTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['get_rule']) ? $data['get_rule'] : '');
        $valueArr = explode(',', $value);
        $list = $this->getGetRuleList();
        return implode(',', array_intersect_key($list, array_flip($valueArr)));
    }

    protected function setGetRuleAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    public function getValidSwitchList()
    {
        return ['0' => __('Period'), '1' => __('Always'), '2' => __('Days')];
    }


    public function merchant()
    {
        return $this->belongsTo('app\admin\model\Merchant', 'merchant_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    







}
