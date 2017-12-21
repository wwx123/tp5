<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Address extends Model {
	// 设置查询的表
	protected $table = "tp_address";

    public function getIconAttr($value,$data)
    {
        $status = [0=>'家',1=>'公司',2=>'其它'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }

    public function getAdd1Attr($value,$data)
    {
        $str = $data['country'].','.$data['province'].','.$data['city'].','.$data['district'];
        $res = DB::table('tp_region') -> where('region_id','in','('.$str.')') -> order('parent_id','asc') -> select();
        $add1 = '';
        foreach($res as $v){
            $add1 .= $v['region_name'].'/';
        }
        $add1 = trim($add1,'/');
        return $add1;
    }
}