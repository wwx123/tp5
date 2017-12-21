<?php
namespace app\admin\model;

use think\Model;

class Audit extends Model {
	// 设置查询的表
	protected $table = "tp_audit";

    public function getStatusAttr($value,$data)
    {
        $status = [1=>'新消息',2=>'已查看',3=>'已处理'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }
}