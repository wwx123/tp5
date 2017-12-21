<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Usersay extends Model {
	// 设置查询的表
	protected $table = "tp_usersay";

    public function getStatusAttr($value,$data)
    {
        $status = [0=>'禁用',1=>'正常'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }
}