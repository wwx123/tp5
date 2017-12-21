<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Review extends Model {

    public function getPictureAttr($value,$data)
    {
        $str = '';
        $val = $value==''?array():explode(',',$value);
        $pn = count($val);
        if ($pn == 1) {
            $class = '';
        } else if ($pn == 2 || $pn == 4) {
            $class = 'two';
        } else if ($pn == 3 || $pn > 4) {
            $class = 'three';
        }
        foreach ($val as $v) {
            $str .= '<div class="inline imgList '.$class.'"><img src="/images/review/'.$v.'"></div>';
        }
        return $str;
    }

    public function getUsersidAttr($value,$data)
    {
        $res = DB::table('tp_users')->where(['id'=>$value])->field('picture,username')->find();

        return $res;
    }

    public function getAddtimeAttr($value,$data)
    {
        $now = time()-$value;
        if ($now < 60) {
            $val = $now.'秒前';
        } else if ($now >= 60 && $now < 3600) {
            $val = ceil($now/60).'分钟前';
        } else if ($now >= 3600 && $now < 86400) {
            $val = ceil($now/3600).'小时前';
        } else if ($now >= 86400 && $now < 604800) {
            $val = ceil($now/86400).'天前';
        } else if ($now >= 604800 && $now < 2592000) {
            $val = ceil($now/604800).'星期前';
        } else if ($now >= 2592000 && $now < 28536000) {
            $val = ceil($now/259200).'月前';
        } else {
            $val = ceil($now/28536000).'年前';
        }

        return $val;
    }

    public function getDescriAttr($value,$data)
    {
        if ($data['topicid']!=0) {
            $new = DB::table('tp_topic') -> where('id',$data['topicid']) -> field('title') -> find();
            if (isset($new['title'])) {
                $value = '<span style="color:#F5A623;">#'.$new['title'].'#</span>&nbsp;'.$value;
            }
        }
        return $value;
    }

    public function getUsersAttr($value,$data)
    {
        $users = array();
        $sql = "select tp_users.picture from tp_users,tp_review_attention where tp_users.id=tp_review_attention.usersid and tp_review_attention.reviewid=".$data['id']." limit 8";
        $users = DB::query($sql);

        return $users;
    }

    public function getDescri2Attr($value,$data)
    {
        return $data['descri'];
    }
}