<?php
namespace app\admin\model;

use think\Model;

class News extends Model {
	// 设置查询的表
	protected $table = "tp_news";

    public function getStatusAttr($value,$data)
    {
        $status = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--'; 
    }
}