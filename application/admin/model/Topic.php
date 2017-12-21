<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Topic extends Model {
	// 设置查询的表
	protected $table = "tp_topic";

    public function getStateAttr ($value,$data)
    {
        $status = [0=>'正在讨论',1=>'已过期'];
        return $status[$data['state']];
    }

    public function getStatusAttr ($value,$data)
    {
        $status = [0=>'已禁用',1=>'正常'];
        return $status[$data['status']];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }

    public function getUpdatetimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }

    public function getStarttimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }

    public function getEndtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }
}