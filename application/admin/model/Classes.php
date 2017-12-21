<?php
namespace app\admin\model;

use think\Model;

class Classes extends Model {
	// 设置查询的表
	protected $table = "tp_classes";

    public function getIsFreeAttr($value)
    {
        $status = [1=>'免费',0=>'付费'];
        return $status[$value];
    }

    public function getIsIndexAttr($value)
    {
        $status = [0=>'否',1=>'是'];
        return $status[$value];
    }

    public function getStatusAttr($value)
    {
        $status = [0=>'禁用',1=>'正常'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }
}