<?php
namespace app\admin\model;

use think\Model;

class Users extends Model {
	// 设置查询的表
	protected $table = "tp_users"; 

    public function getStatusAttr($value,$data)
    {
        $status = [0=>'普通会员',1=>'推广员',2=>'经销商'];
        return $status[$value];
    }

    public function getStateAttr($value,$data)
    {
        $status = [0=>'微信用户',1=>'手机用户',2=>'其他用户'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--'; 
    }

}