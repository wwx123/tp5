<?php
namespace app\admin\model;

use think\Model;

class Manager extends Model {
    
    public function getStateAttr($value)
    {
        $status = [0=>'正常',1=>'禁用'];
        return $status[$value];
    }

    public function getLevelAttr($value)
    {
        $status = [0=>'超级管理员',1=>'普通管理员'];
        return $status[$value];
    }

    public function getAddtimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }

    public function getLasttimeAttr($value)
    {
        return $value>0?date('Y-m-d',$value):'--:--';
    }
}