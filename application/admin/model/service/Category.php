<?php

namespace app\admin\model\service;

use think\Model;
use think\Db;
use think\Exception;
use traits\model\SoftDelete;

class Category extends Model
{

    use SoftDelete;

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'goods_category';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }


    public function merchant()
    {
        return $this->belongsTo('app\admin\model\Merchant', 'merchant_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public static function dataInitialize($merchant_id) {
        if($merchant_id == 0 ){
            return;
        }
        $model = new Category;
        $total = $model
            ->withTrashed()
            ->where("merchant_id", $merchant_id)
            ->count();
        if($total != 0) {
            return;
        }
        $commonCategory = model('app\common\model\Category');
        $categorys = $commonCategory->getCategoryArray('service');

        Db::startTrans();
        try {
            $data = [];
            foreach($categorys as $category) {
                $row = [];
                $row['name'] = $category['name'];
                $row['nickname'] = $category['nickname'];
                $row['description'] = $category['description'];
                $row["merchant_id"] = $merchant_id;
                $data[] = $row;
            }
            $model->saveAll($data);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
        }
    }
}
