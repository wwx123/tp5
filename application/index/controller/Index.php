<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
    	$newnotice = '';
    	$newshuf = array();
    	$list1 = array();
    	$list2 = array();

    	$notice = DB::table('tp_notice')
    			  -> order(['sort'=>'desc'])
    			  -> field('title')
    			  -> select();

    	$shuf = DB::table('tp_shuffing')
    			-> where(['state'=>0,'status'=>1,'enabled'=>1])
    			-> order(['sort'=>'desc'])
    			-> field('title,picture,url,starttime,endtime')
    			-> select();

    	$list = DB::table('tp_index_list')
    			-> where(['status'=>1])
    			-> order(['sort'=>'desc','id'=>'desc'])
    			-> field('title,picture,price,desc,url,goodsid,state')
    			-> select();

    	$usersay = DB::table('tp_usersay')
    			   -> where(['status'=>1])
    			   -> order(['sort'=>1,'id'=>'desc'])
    			   -> field('desc,picture,url')
    			   -> select();

    	$goods = DB::table('tp_goods')
    			 -> where(['status'=>1])
    			 -> order(['sort'=>'desc'])
    			 -> field('id,goods,picture,price')
    			 -> select();

    	$about = DB::table('tp_about')
    			 -> where(['status'=>1])
    			 -> field('title,content')
    			 -> select();

    	foreach ($notice as $nv) {
    		$newnotice .= '&nbsp;&nbsp;|&nbsp;&nbsp;'.$nv['title'];
    	}
    	$newno = trim($newnotice,'&nbsp;&nbsp;|&nbsp;&nbsp;');

    	foreach ($shuf as $sv) {
    		if (!($sv['endtime']>0&&$sv['endtime']<time()&&$sv['starttime']>0&&$sv['starttime']>time())) {
    			$newshuf[] = $sv;
    		}
    	}
    	$sn = count($shuf);

    	foreach ($list as $lv) {
    		if ($lv['state'] == 0) {
    			$list1[] = $lv;
    		} else {
    			$lv['goods'] = DB::table('tp_goods') 
    						   -> where(['id'=>['in',$lv['goodsid']]])
    						   -> field('id,goods,picture,price') 
    						   -> select();
    			$list2[] = $lv;
    		}
    	}

    	$this -> assign(['notice'=>$newno,'shuf'=>$newshuf,'sn'=>$sn,'usersay'=>$usersay,'list1'=>$list1,'list2'=>$list2,'about'=>$about]);
        return view();
    }

	public function _empty ()
	{
		$this -> redirect('/');
	}
}
