<?php
namespace app\admin\model;

use think\Model;

class IndexList extends Model {
	// 设置查询的表
	protected $table = "tp_index_list";

    public function getStatusAttr($value)
    {
        $status = [0=>'禁用',1=>'正常'];
        return $status[$value];
    }

    public function getStateAttr($value)
    {
        $status = [0=>'图片',1=>'商品'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }
}