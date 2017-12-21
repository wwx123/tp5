<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Bbs extends Controller
{
	// 论坛首页
    public function index()
    {
    	$newtop = new \app\index\model\Topic;
    	$review = new \app\index\model\Review;
    	$topic = $newtop::where(['status'=>1])->order(['sort'=>'desc','id'=>'desc'])->select();
    	$review = $review::where(['status'=>1,'path'=>0])->order(['addtime'=>'desc','id'=>'desc'])->limit(0,8)->select();

    	$this->assign(['topic'=>$topic,'review'=>$review]);
        return view();
	}

	// 关注页
	public function focus()
	{
		$sessionid = 1;
    	$review = new \app\index\model\Review;

    	$topicid = DB::table('tp_focus')->where('usersid',$sessionid)->field('topicid')->select();
    	$topicid = $this->ReArray($topicid,'topicid');
    	if ($topicid!=0) {
    		$review = $review::where(['status'=>1,'path'=>0,'topicid'=>['in',$topicid]])->order(['addtime'=>'desc','id'=>'desc'])->limit(0,8)->select();

    		$this->assign(['review'=>$review]);
    	}

    	$this->assign(['topid'=>$topicid]);
		return view();
	}

	// 发帖页
	public function posting()
	{
		// 上传到服务器上再验证功能
		return view();
	}

	// 我的圈子
	public function myinfo()
	{
		// 暂时没有功能
		return view();
	}

	// 话题详情
	public function topic_detail()
	{
		if (isset($_GET['id'])&&$_GET['id']>0) {
			$newtop = new \app\index\model\Topic;
			$newrev = new \app\index\model\Review;

			$topic = $newtop::where(['id'=>$_GET['id']])->field('picture,title,attention,reviews,desc')->find();
    		$review = $newrev::where(['status'=>1,'path'=>0,'topicid'=>['in',$_GET['id']]])->order(['addtime'=>'desc','id'=>'desc'])->limit(0,8)->select();

			$this->assign(['topic'=>$topic,'review'=>$review,'topicid'=>$_GET['id']]);
			return view();
		} else {
			return redirect('index');
		}
	}

	// 帖子详情
	public function detail()
	{
		if (isset($_GET['id'])&&$_GET['id']>0) {
			$newrev = new \app\index\model\Review;

    		$review = $newrev::where(['status'=>1,'path'=>0,'id'=>$_GET['id']])->find();

    		$chil = $review::where(['status'=>1,'path'=>$_GET['id']]) -> order(['id'=>'desc']) -> select();

			$this->assign(['review'=>$review,'chil'=>$chil]);
			return view();
		} else {
			return redirect('index');
		}
	}

	// 帖子展示1
	public function Reviewdata()
	{
		if (isset($_POST['page'])&&$_POST['page']>=0) {
     		$review = new \app\index\model\Review;

			if (!empty($_POST['sera'])&&$_POST['sera']!='') {
    			$review = $review::where(['status'=>1,'path'=>0])->where('descri','like','%'.$_POST['sera'].'%')->order(['id'=>'desc'])->limit($_POST['page']*8,8)->select();
			} else {
    			$review = $review::where(['status'=>1,'path'=>0])->order(['addtime'=>'desc','id'=>'desc'])->limit($_POST['page']*8,8)->select();
			}
    		return $this->ReviewShow($review);
		}
	}

	// 帖子展示2
	public function Reviewdata2()
	{
		if (isset($_POST['page'])&&$_POST['page']>=0) {
     		$review = new \app\index\model\Review;

    		if (isset($_POST['topid'])&&$_POST['topid']!=0) {
    			$topicid = $_POST['topid'];
    		} else {
				$sessionid = 1;
	    		$topicid = DB::table('tp_focus')->where('usersid',$sessionid)->field('topicid')->select();
	    		$topicid = $this->ReArray($topicid,'topicid');    			
    		}

			if (!empty($_POST['sera'])&&$_POST['sera']!='') {
    			$review = $review::where(['status'=>1,'path'=>0,'topicid'=>['in',$topicid]])->where('descri','like','%'.$_POST['sera'].'%')->order(['id'=>'desc'])->limit($_POST['page']*8,8)->select();
			} else {
    			$review = $review::where(['status'=>1,'path'=>0,'topicid'=>['in',$topicid]])->order(['addtime'=>'desc','id'=>'desc'])->limit($_POST['page']*8,8)->select();
			}

    		return $this->ReviewShow($review);
		}
	}

	// 评论展示
	// $review Array 评论内容
	// 返回 String
	private function ReviewShow($review)
	{
		$str = '';
		foreach ($review as $rv) {
			$picture = isset($rv['usersid']['picture'])?$rv['usersid']['picture']:'/bbs/images/logo.png';
			$username = $rv['usersid']['username']?$rv['usersid']['username']:'花加';
			$rv['ans'] = $rv['ans']==0?'评论':$rv['ans'];
            $str .= '<div class="bgWhite topicList"><a href="detail.html?id='.$rv['id'].'"><div class="authorRow clearfix"><div class="headImg fl"><img src="'.$picture.'"></div><div class="authorInfo"><div class="author">'.$username.'</div><div class="publishTime f10 color_9b">'.$rv['addtime'].'</div></div></div><div class="toppicCont">'.$rv['descri'].'</div><div class="toppicPic flex"><div class="inline imgList">'.$rv['picture'].'</div></div></a><div class="corp_row flex"><div class="btn commitBtn"><span class="icon icon_commit"></span><span class="num inline">'.$rv['ans'].'</span></div><div class="btn supportBtn"><span class="icon icon_support"></span><span class="num inline">'.$rv['likes'].'</span></div></div></div></a>';
		}

		return $str;
	}

	// 合并数组中的某一项
	// 参数 	描述			类型
	// $arr 	需要合并的数组  Array
	// $field 	需要合并的键 	String
	// 输出 String
	private function ReArray ($arr,$field,$str=',')
	{
		$n = count($arr);
		$new = '';
		if ($n>0) {
			foreach ($arr as $v) {
				$new .= $str.$v[$field];
			}
			$new = trim($new,$str);
			return $new;
		}
	}

	// public function _empty ()
	// {
	// 	$this -> redirect('index');
	// }
}
