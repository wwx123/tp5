<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Topic extends Model {

    public function getUsersAttr($value,$data)
    {
    	$users = array();
    	$sql = "select tp_users.picture from tp_users,tp_attention where tp_users.id=tp_attention.usersid and tp_attention.topicid=".$data['id']." limit 4";
        $users = DB::query($sql);

        return $users;
    }
}