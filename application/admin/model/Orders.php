<?php
namespace app\admin\model;

use think\Model;

class Orders extends Model {
	// 设置查询的表
	protected $table = "tp_orders";

    public function getState2Attr($value,$data)
    {
        $status = [-1=>'订单失效',0=>'未支付',1=>'已付款',2=>'已发货',3=>'已签收',4=>'已完成',5=>'取消订单'];
        return $status[$data['state']];
    }

    public function getIsRebateAttr($value,$data)
    {
        $status = [0=>'无',1=>'一级返利',2=>'二级返利'];
        return $status[$value];
    }

    public function getIsCouponAttr($value,$data)
    {
        $status = [0=>'不返利',1=>'返优惠券',2=>'返利'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--'; 
    }

    public function getSendtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--'; 
    }
}