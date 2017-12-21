<?php
namespace app\admin\model;

use think\Model;

class Apply extends Model {
	// 设置查询的表
	protected $table = "tp_apply";

    public function getState2Attr($value,$data)
    {
        $status = [0=>'待购买',1=>'待审核',2=>'已审核'];
        return $status[$data['state']];
    }

    public function getTypeAttr($value)
    {
        $status = [0=>'推广员',1=>'经销商'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }

    public function getUpdatetimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }
}