<?php

namespace app\admin\model;

use think\Model;
use think\Db;
use think\Exception;
use traits\model\SoftDelete;



class MailContent extends Model
{
    use SoftDelete;
    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'mail_content';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'type_text'
    ];
    

    
    public function getTypeList()
    {
        return ['message_system_receiving' => __('Message_system_receiving'), 'message_normal' => __('Message_normal'), '' => __('')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public static function dataInitialize($merchant_id) {
        if($merchant_id == 0 ){
            return;
        }
        $model = new MailContent;
        $total = $model
            ->withTrashed()
            ->where("merchant_id", $merchant_id)
            ->count();
        if($total != 0) {
            return;
        }
        $list = $model->withTrashed()->where("merchant_id", 0)->select();

        Db::startTrans();
        try {
            $data = [];
            foreach($list as $row) {
                $row = $row->toArray();
                $data[] = array(
                    'type' => $row["type"],
                    'title' => $row["title"],
                    'content' => $row["content"],
                    'content_trans' => $row["content_trans"],
                    'merchant_id' => $merchant_id,
                );
            }
            $model->saveAll($data);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
        }
    }

}
