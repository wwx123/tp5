<?php
namespace app\admin\model;

use think\Model;

class NewsType extends Model {
	// 设置查询的表
	protected $table = "tp_news_type";

    public function getStatusAttr($value,$data)
    {
        $status = [-1=>'删除',0=>'禁用',1=>'正常'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }
}