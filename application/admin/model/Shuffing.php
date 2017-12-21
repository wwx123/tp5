<?php
namespace app\admin\model;

use think\Model;

class Shuffing extends Model {
	// 设置查询的表
	protected $table = "tp_shuffing";

    public function getEnabledAttr($value,$data)
    {
        $status = [0=>'禁用',1=>'启用'];
        return $status[$value];
    }

    public function getStateAttr($value,$data)
    {
        $status = [0=>'商城',1=>'课程'];
        return $status[$value];
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