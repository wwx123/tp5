<?php
namespace app\admin\model;

use think\Model;

class Distrilog extends Model {
	// 设置查询的表
	protected $table = "tp_distrilog";

    public function getStatusAttr($value,$data)
    {
        $status = [1=>'一级返利',2=>'二级返利'];
        return $status[$value];
    }

    public function getStateAttr($value,$data)
    {
        $status = [1=>'购买商品',2=>'订阅课程'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }
}