<?php

namespace app\admin\model\merchant;

use think\Model;


class MessageMail extends Model
{

    

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'merchant_msg_mail';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







}
