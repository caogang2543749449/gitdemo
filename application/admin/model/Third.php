<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Third extends Model
{
    /**
     * 产品定义
     */
    const PRODUCT_COMMON = 0;
    const PRODUCT_QRHOTEL = 1;
    const PRODUCT_QRMINPAKU = 2;
    const PRODUCT_QRMALL = 3;
    const PRODUCT_QRSTORE = 4;
    const PRODUCT_QRCITY = 5;

    /**
     * 第三方服务定义
     */
    const THIRD_WX_APPLICATION = 1;
    const THIRD_WX_ACCOUNT = 2;
    const THIRD_ALI_APPLICATION = 3;
    const THIRD_BD_APPLICATION = 4;
    const THIRD_NIHAO_PAY = 5;
    const THIRD_OTHER = 9;


    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'third';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'product_text',
        'third_type_text'
    ];

    public static function getProductList()
    {
        return [
            '1' => __('QRHOTEL'),
            '2' => __('QRMINPAKU'),
            '3' => __('QRMALL'),
            '4' => __('QRSTORE'),
            '5' => __('QRCITY')
        ];
    }

    public static function getThirdTypeList()
    {
        return [
            '1' => __('WX APPLICATION'),
            '2' => __('WX ACCOUNT'),
            '3' => __('ALI APPLICATION'),
            '4' => __('BD APPLICATION'),
            '5' => __('NIHAO_PAY'),
            '9' => __('OTHER')
        ];
    }

    public function getProductTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['product']) ? $data['product'] : '');
        $list = $this->getProductList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getThirdTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['third_type']) ? $data['third_type'] : '');
        $list = $this->getThirdTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

}
