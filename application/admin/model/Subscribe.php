<?php
namespace app\admin\model;

use think\Model;

class Subscribe extends Model {
	// 设置查询的表
	protected $table = "tp_subscribe";

    public function getState2Attr($value,$data)
    {
        $status = [-1=>'订单失效',0=>'未支付',1=>'已付款',2=>'取消订单'];
        return $status[$data['state']];
    }

    public function getIsRebateAttr($value,$data)
    {
        $status = [0=>'无',1=>'一级返利',2=>'二级返利'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';  
    }
}