<?php
namespace app\admin\model;

use think\Model;

class Withdraw extends Model {
	// 设置查询的表
	protected $table = "tp_withdraw";

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }

    public function getUpdatetimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }
}