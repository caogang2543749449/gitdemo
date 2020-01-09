<?php

namespace app\admin\model;

use think\Log;
use think\Model;

class Sequence extends Model
{

    protected static $instance;

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'sequence';


    /**
     * 初始化
     * @access public
     * @param array $options 参数
     * @return Sequence
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }


    /**
     * 获取序列号
     * @author baiyouwen
     */
    public function getNewSequence($seqName)
    {
        $res = self::find($seqName);
        Log::write($res);
        $newSeq = $res->getData('seq_value') + $res->getData('seq_step');
        $res->save(['seq_value'=>$newSeq]);
        $res->commit();
        return $newSeq;
    }

}
