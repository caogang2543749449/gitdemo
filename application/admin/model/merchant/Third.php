<?php

namespace app\admin\model\merchant;

use think\Model;


class Third extends Model
{

    

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'merchant_third';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'third_type_text'
    ];
    

    
    public function getThirdTypeList()
    {
        return \app\admin\model\Third::getThirdTypeList();
    }

    public function getPymentList()
    {
        return ['Nihaopay' => __('Nihaopay'), 'Lakala' => __('Lakala')];
    }


    public function getThirdTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['third_type']) ? $data['third_type'] : '');
        $list = $this->getThirdTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function merchant()
    {
        return $this->belongsTo('app\admin\model\Merchant', 'merchant_id', 'id')->setEagerlyType(0);
    }

}
