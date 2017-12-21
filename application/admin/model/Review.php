<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Review extends Model {
	// 设置查询的表
	protected $table = "tp_review";

    public function getState2Attr ($value,$data)
    {
        $status = ['-1'=>'未通过',0=>'等待审核',1=>'已发表'];
        return $status[$data['state']];
    }

    public function getTypeAttr ($value,$data)
    {
        $status = [0=>'评价',1=>'话题',2=>'回复'];
        return $status[$data['type']];
    }

    public function getStatusAttr ($value,$data)
    {
        $status = [0=>'已禁用',1=>'正常'];
        return $status[$data['status']];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }

    public function getUpdatetimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }

    public function getStarttimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }

    public function getEndtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }

    public function getUsernameAttr($value,$data)
    {
        if ($data['usersid']==0) {
            return '管理员';
        } else {
            $res = DB::table('tp_users') -> where(['id'=>$data['usersid']]) -> field('username') -> find();
            return isset($res['username'])?$res['username']:'未知';
        }
    }

    public function getTopicnameAttr($value,$data)
    {
        if ($data['topicid']==0) {
            return '不参与话题';
        } else {
            $res = DB::table('tp_topic') -> where(['id'=>$data['topicid']]) -> field('title') -> find();
            return isset($res['title'])?$res['title']:'未知';
        }
    }

    public function getPicsAttr($value,$data)
    {
        return !empty($data['picture'])?explode(',',$data['picture']):'';
    }
}