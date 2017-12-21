<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Coupon extends Model {
	// 设置查询的表
	protected $table = "tp_coupon";

    public function getStateAttr ($value,$data)
    {
        $status = [0=>'满减/抵用券',1=>'折扣'];
        return $status[$data['state']];
    }

    public function getStatusAttr ($value,$data)
    {
        $status = [-1=>'已禁用',0=>'已停止',1=>'正常'];
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

    public function getNumberAttr ($value,$data)
    {
    	return $data['number']==-1?'无限量':$data['number'];
    }
}