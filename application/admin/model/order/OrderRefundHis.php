<?php

namespace app\admin\model\order;

use think\Model;

class OrderRefundHis extends Model
{

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'order_refund_his';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';


}
