<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Invite extends Model {
	// 设置查询的表
	protected $table = "tp_invite";

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--'; 
    }

    public function getStyleAttr ($value,$data)
    {
        $status = [1=>'返优惠券',2=>'返利'];
        return $status[$data['style']];
    }
}