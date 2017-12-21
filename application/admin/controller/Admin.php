<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class Admin extends Controller
{
	protected $beforeActionList = [
		'isadmin' => ['except'=>'index']
	];

	// 登录判断
	public function _initialize ()
	{
        if (!(null!=session('adminid')&&!empty(session('adminid')))) {
            echo "<script>alert('请先登录');location.href='/login'</script>";
        }
	}

	public function Isadmin ()
	{
		if (session('admindata')['level']==1) {
			// echo "<script>alert('管理员权限不足');location.href='index'</script>";
		}
	}

	public function Index ()
	{
		return view();
	}

	public function Top ()
	{
		return view();
	}

	public function Left ()
	{
		return view();
	}

	public function Main ()
	{
		return view();
	}

	public function Manager ()
	{
		$new = new \app\admin\model\Manager;
		$page = isset($_GET['page'])?$_GET['page']:1;
		$len = 10;
		$start = ($page-1)*$len;

		$data = $new::where(['state'=>'0','level'=>'1'])
				->order('id','desc')
				->limit($start,$len)
				->field('id,username,level,state,lasttime,lastip,addtime')
				->select();
		$n = $new::where(['state'=>'0','level'=>'1']) -> count();
		$strpage = $this -> PageList($n,$page,$len,'');

		$this->assign(['page'=>$strpage,'data'=>$data]);
		return view('manager/manager');
	}

	public function Users ()
	{
		$news = new \app\admin\model\Users;
		$page = isset($_GET['page'])?$_GET['page']:1;
		$len = 5;
		$start = ($page-1)*$len;
		$search = isset($_GET['search'])?$_GET['search']:'';
		
		if ($search == '') {
			$data = $news::limit($start,$len)
					->order('id','desc')
					->select();
			$n = $news->count();
		} else {
			$data = $news::where('id',$search)
					->whereOr('username','like','%'.$search.'%')
					->limit($start,$len)
					->order('id','desc')
					->select();
			$n = $news::where('id',$search)
				 ->whereOr('username','like','%'.$search.'%')
				 ->count();
		}
		$page = $this->PageList($n,$page,$len,['search'=>$search]);

		$this->assign(['data'=>$data,'page'=>$page]);
		return view('users/users');
	}

	public function Useredit ()
	{
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$data = DB::table('tp_users') -> where('id',$id) -> find();

			$this->assign('data',$data);
			return view();
		}
	}

	public function Address ()
	{
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$news = new \app\admin\model\Address;
			$data = $news -> where('usersid',$id) -> select();

			$this->assign('data',$data);
			return view('users/address');
		}
	}

	public function Invite ()
	{
		$news = new \app\admin\model\Invite;
		$page = 1;
		$len = 10;

		$data = $news::join('tp_users','tp_invite.usersid=tp_users.id','left')
				->join('tp_users users2','tp_invite.inviteuser=users2.id','left')
				->order('tp_invite.id','desc')
				->field('tp_invite.*,tp_users.username,users2.username iusername')
				->select();

		$n = $news->count();
		$page = $this->PageList($n,$page,$len,'');

		$this->assign(['data'=>$data,'page'=>$page]);
		return view('invite/invite');
	}

	public function Coupon ()
	{
		$news = new \app\admin\model\Coupon;
		$page = 1;
		$len = 10;

		$data = $news->where('status','>','-1')->order('id','desc')->select();
		$n = $news->where('status','>','-1')->count();
		$page = $this->PageList($n,$page,$len,'');

		$this->assign(['data'=>$data,'page'=>$page]);
		return view('coupon/coupon');
	}

	public function Couponedit ()
	{
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$data = DB::table('tp_coupon') -> where('id',$id) -> find();

			$this->assign(['data'=>$data]);
			return view('coupon/couponedit');
		} else {
			return $this->redirect('coupon');
		}
	}

	public function Couponadd ()
	{
		return view('coupon/couponadd');
	}

	public function Index_list ()
	{
		$new = new \app\admin\model\IndexList;

		$data = $new -> order(['sort'=>'desc','id'=>'desc']) -> select();

		$this->assign(['data'=>$data]);
		return view('index/indexlist');
	}

	public function Index_listadd ()
	{
		$type = DB::table('tp_goods_type') -> where(['status'=>1,'path'=>0]) -> field('id,typename') -> select();

		$goods = DB::table('tp_goods') -> where(['status'=>1]) -> field('id,goods') -> select();

		$this->assign(['type'=>$type,'goods'=>$goods]);
		return view('index/up');
	}

	public function Index_listdesc ()
	{
		if (isset($_GET['i'])&&!empty($_GET['i'])) {
			$id = $_GET['i'];
			$data = DB::table('tp_index_list') -> where('id',$id) -> find();
			$type = DB::table('tp_goods_type') -> where(['status'=>1,'path'=>0]) -> field('id,typename') -> select();
			$goods = DB::table('tp_goods') -> where(['status'=>1]) -> field('id,goods') -> select();

			$this->assign(['data'=>$data,'type'=>$type,'action'=>'update','goods'=>$goods]);
			return view('index/up');
		} else {
			return $this->redirect('index_list');
		}
	}

	public function Usersay ()
	{
		$news = new \app\admin\model\Usersay;

		$data = $news::where(['status'=>1])->order(['sort'=>'desc','id'=>'desc'])->select();

		$this->assign(['data'=>$data]);
		return view('index/usersay');
	}

	public function Usersayadd ()
	{
		return view('index/sayup');
	}

	public function Usersaydesc ()
	{
		if (isset($_GET['i'])&&!empty($_GET['i'])) {
			$id = $_GET['i'];
			$data = DB::table('tp_usersay') -> where('id',$id) -> find();

			$this->assign(['data'=>$data,'action'=>'update']);
			return view('index/sayup');
		} else {
			return $this->redirect('usersay');
		}
	}

	public function About ()
	{
		$page = isset($_GET['page'])?$_GET['page']:1;
		$len = 10;
		$start = ($page-1)*$len;

		$data = DB::table('tp_about') -> where(['status'=>1]) -> order(['sort'=>'desc','id'=>'desc']) -> select();
		$n = DB::table('tp_about') -> where(['status'=>1]) -> count();
		$page = $this->PageList($n,$page,$len,'');

		$this->assign(['data'=>$data,'page'=>$page]);
		return view('index/about');
	}

	public function Aboutadd ()
	{
		$this->assign(['action'=>'insert']);
		return view('index/aboutup');
	}

	public function Aboutedit ()
	{
		if (isset($_GET['id'])) {
			$id = $_GET['id'];

			$data = DB::table('tp_about') -> where(['id'=>$id]) -> find();

			$this->assign(['data'=>$data,'action'=>'update']);
			return view('index/aboutup');
		} else {
			return redirect('about');
		}
	}

	// 轮播图列表
	public function Shuffing ()
	{
		$news = new \app\admin\model\Shuffing;

		$data = $news->order(['sort'=>'desc','id'=>'desc'])->select();

		$this->assign(['data'=>$data]);
		return view('shuffing/shuffing');
	}

	// 轮播图详情
	public function Shufdesc ()
	{
		if (isset($_GET['i'])&&!empty($_GET['i'])) {
			$id = $_GET['i'];
			$data = DB::table('tp_shuffing') -> where('id',$id) -> find();

			$this->assign(['data'=>$data,'action'=>'update']);
			return view('shuffing/shufup');
		} else {
			return $this->redirect('shuffing');
		}
	}

	// 轮播图添加
	public function Shufadd ()
	{
		$this->assign(['action'=>'insert']);
		return view('shuffing/shufup');
	}

	// 商品列表
	public function Goods ()
	{
		echo '商品列表';

		// return view('goods/goods');
	}

	// 课程列表
	public function Classes ()
	{
		$page = isset($_GET['page'])?$_GET['page']:1;
		$len = 5;
		$start = ($page-1)*$len;

		$new = new \app\admin\model\Classes;

		$data = $new::where(['status'=>1])->limit($start,$len)->order(['sort'=>'desc','id'=>'desc'])->select();
		$n = $new::where(['status'=>1])->count();

		$page = $this->PageList($n,$page,$len,'');

		$this->assign(['data'=>$data,'page'=>$page]);
		return view('classes/classes');
	}

	public function Classadd ()
	{
		$type = DB::table('tp_classes_type')->where(['status'=>1])->field('id,typename')->select();

		$this->assign(['type'=>$type]);
		return view('classes\classesadd');
	}

	public function Classedit ()
	{
		if (isset($_GET['id'])) {
			$id = $_GET['id'];

			$data = DB::table('tp_classes') -> where(['id'=>$id]) -> find();
			$type = DB::table('tp_classes_type')->where(['status'=>1])->field('id,typename')->select();

			$this->assign(['data'=>$data,'type'=>$type]);
			return view('classes\classesedit');
		} else {
			return redirect('classes');
		}

	}

	public function Classlist ()
	{
		if (isset($_GET['id'])) {
			$new = new \app\admin\model\ClassesDetail;
			$id = $_GET['id'];
			$page = 1;
			$len = 5;
			$start = ($page-1)*$len;

			$class = $new::where(['status'=>1,'id'=>$id])->find();
			$data = $new::where(['status'=>1,'classid'=>$id])->order(['id'=>'desc'])->select();
			$n = $new::where(['status'=>1,'classid'=>$id])->count();

			$page = $this->PageList($n,$page,$len,'');

			$this->assign(['data'=>$data,'page'=>$page,'class'=>$class,'id'=>$id]);
			return view('classes/list');
		} else {
			return redirect('classes');
		}
	}

	public function Listadd ()
	{
		return view('classes/listadd',['id'=>$_GET['id']]);
	}

	public function Listinsert (Request $request)
	{
		$data = $request->except('d1');
		$data['addtime'] = time();
		if ($data['state']==0) {
			$data['content'] = $_POST['d1'];
		} else {
			$newfile = request()->file('d2');
			if ($newfile) {
            	$info2 = $newfile->move(ROOT_PATH . 'public' . DS . 'video');
				$newname2 = $info2->getSaveName();
			} else {
                echo "<script>alert('上传失败，请确认文件是否正确');location.href=history.back(-1);</script>";
			}
			$data['url'] = $newname2;
		}
		$file = request()->file('file');
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'images/classes_detail');
            if($info){
                $newname = $info->getSaveName();
            }else{
                echo "<script>alert('上传失败，请确认图片格式正确');location.href=history.back(-1);</script>";
            }
        	$data['picture'] = $newname;
        }

        $res = DB::table('tp_classes_detail')->insert($data);
        
        if ($res) {
        	echo '<script>alert("添加成功");location.href="classlist.html?id='.$data['classid'].'"</script>';
        } else {
        	echo '<script>alert("操作失败，请联系管理员");location.href=history.back(-1)</script>';
        }
	}

	public function Classdel ()
	{
		if (isset($_GET['id'])) {
			$old = DB::table('tp_classes') -> where(['id'=>$_GET['id']]) -> find();
			$detail = DB::table('tp_classes_detail')
					  -> where(['classid'=>$_GET['id']])
					  -> field('id,picture,url')
					  -> select();
			foreach ($detail as $dv) {
                $picurl = ROOT_PATH . 'public' . DS . 'images/classes_detail/'.$dv['picture'];
                $picurl2 = ROOT_PATH . 'public' . DS . 'video/'.$dv['url'];
                $picurl3 = ROOT_PATH . 'public' . DS . 'mp3/'.$dv['url'];
                if (is_file($picurl)) { unlink($picurl); }
                if (is_file($picurl2)) { unlink($picurl2); }
                if (is_file($picurl3)) { unlink($picurl3); }
			}
			$oldpic = ROOT_PATH . 'public' . DS . 'images/classes/'.$old['picture'];
            if (is_file($oldpic)) { unlink($oldpic); }

            $res = DB::table('tp_classes_detail')->where(['classid'=>$_GET['id']])->delete();
            if ($res) {
            	$oldres = DB::table('tp_classes')->where(['id'=>$_GET['id']])->delete();
            	if ($oldres) {
            		echo '<script>alert("删除成功");location.href="classes.html"</script>';
            	} else {
            		echo '<script>alert("删除失败，请联系管理员");location.href=history.back(-1)</script>';
            	}
            } else {
           		echo '<script>alert("删除失败，请联系管理员");location.href=history.back(-1)</script>';
            }
		} else {
			return redirect('classes');
		}
	}

	public function Listedit ()
	{
		if ($_GET['id']) {
			$id = $_GET['id'];
			$data = DB::table('tp_classes_detail') -> where(['id'=>$id]) -> find();

			return view('classes/listedit',['id'=>$id,'data'=>$data]);
		} else {
			return redirect('classes');
		}
	}

	public function Listupdate (Request $request)
	{
		if (isset($_POST['id'])) {
			$id = $_POST['id'];
			$data = $request->except('d1');
			$data['updatetime'] = time();
			$old = DB::table('tp_classes_detail') -> where(['id'=>$id]) -> find();
			if ($data['state']==0) {
				$data['content'] = $_POST['d1'];
			} else {
				$statefile = $data['state']==1?'video':'mp3';
				$newfile = request()->file('d2');
				if ($newfile) {
	                $picurl2 = ROOT_PATH . 'public' . DS . 'video/'.$old['url'];
	                $picurl3 = ROOT_PATH . 'public' . DS . 'mp3/'.$old['url'];
	                if (is_file($picurl2)) {
	                    unlink($picurl2);
	                }
	                if (is_file($picurl3)) {
	                    unlink($picurl3);
	                }
	            	$info2 = $newfile->move(ROOT_PATH . 'public' . DS . $statefile);
					$newname2 = $info2->getSaveName();
					$data['url'] = $newname2;
				}
			}
			$file = request()->file('file');
	        if($file){
	            $info = $file->move(ROOT_PATH . 'public' . DS . 'images/classes_detail');
	            if($info){
	                $picurl = ROOT_PATH . 'public' . DS . 'images/classes_detail/'.$old['picture'];
	                if (is_file($picurl)) {
	                    unlink($picurl);
	                }
	                $newname = $info->getSaveName();
	        		$data['picture'] = $newname;
	            }
	        }
			$res = DB::table('tp_classes_detail')->update($data);
        
	        if ($res) {
	        	// echo '<script>alert("操作成功");location.href="classlist.html?id='.$data['classid'].'"</script>';
	        	echo '<script>alert("操作成功");location.href=history.back(-1)</script>';
	        } else {
	        	echo '<script>alert("操作失败，请联系管理员");location.href=history.back(-1)</script>';
	        }
		} else {
			return redirect('classes');
		}
	}

	public function Listdel ()
	{
		if ($_GET['id']) {
			$id = $_GET['id'];

			$res2 = DB::table('tp_classes_detail') -> where('id',$id) -> find();
			if ($res2) {
                $picurl = ROOT_PATH . 'public' . DS . 'images/classes_detail/'.$res2['picture'];
                $picurl2 = ROOT_PATH . 'public' . DS . 'video/'.$res2['url'];
                $picurl3 = ROOT_PATH . 'public' . DS . 'mp3/'.$res2['url'];
                if (is_file($picurl)) {
                    unlink($picurl);
                }
                if (is_file($picurl2)) {
                    unlink($picurl2);
                }
                if (is_file($picurl3)) {
                    unlink($picurl3);
                }
                $path = DB::table('tp_classes') -> where('id',$res2['classid']) -> setDec('num');
				echo "<script>alert('删除成功');location.href='classlist.html?id=".$res2['classid']."';</script>";
			} else {
				echo "<script>alert('删除失败，未知错误！请联系管理员');location.href=history.back(-1);</script>";
			}
			$res = DB::table('tp_classes_detail') -> where('id',$id) -> delete();
		} else {
			return redirect('classes');
		}
	}

	public function Classtype ()
	{
		$data = DB::table('tp_classes_type')->where(['status'=>1])->select();

		$this->assign(['data'=>$data]);
		return view('classes/classtype');
	}

	public function Orders ()
	{
		$news = new \app\admin\model\Orders;
		$page = isset($_GET['page'])?$_GET['page']:'1';
		$len = 10;
		$str = ($page-1)*$len;
		$state = '';

		if (!isset($_GET['state'])) {
			$data = $news::where(['status'=>1])
					-> limit($str,$len)
					-> order('id','desc')
					-> select();
			$n = $news
				 ->where(['status'=>1])
				 ->count();
		} else {
			$state = ['state'=>$_GET['state']];
			$data = $news::where(['status'=>1,'state'=>$state['state']])
					-> limit($str,$len)
					-> order('id','desc')
					-> select();
			$n = $news
				 ->where(['status'=>1,'state'=>$state['state']])
				 ->count();
		}
		$page = $this->PageList($n,$page,$len,$state);

		$this->assign(['data'=>$data,'page'=>$page]);
		return view('orders/orders');
	}

	public function Orderedit ()
	{
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			
			$new = new \app\admin\model\Orders;
			$data = $new::join('tp_users','tp_orders.usersid=tp_users.id','left')
					->join('tp_goods','tp_orders.goodsid=tp_goods.id','left')
					->where('tp_orders.id',$id)
					->field('tp_orders.*,tp_users.username,tp_goods.goods')
					->find();

			$this->assign(['data'=>$data]);
			return view('orders/orderedit');
		}
	}

	public function Fhup ()
	{
		if (isset($_POST['id'])&&$_POST['id']>0) {
			$id = $_POST['id'];
			$res = DB::table('tp_orders') -> where('id',$id) -> find();
			if ($res['state'] == 1) {
				$res = DB::table('tp_orders') -> where('id',$id) -> update(['state'=>2,'order_sn'=>$_POST['order_sn'],'sendtime'=>time(),'updatetime'=>time()]);
				if ($res) {
					echo "<script>alert('修改成功，刷新页面生效');location.href=history.back(-1)</script>";
				} else {
					echo "<script>alert('修改失败，请联系管理员');location.href=history.back(-1)</script>";
				}
			} else {
				echo '<script>location.href=history.back(-1)</script>';
			}
		}
	}

	public function Review ()
	{
		$news = new \app\admin\model\Review;
		$page = isset($_GET['page'])?$_GET['page']:1;
		$len = 10;
		$start = ($page-1)*$len;
		$top = isset($_GET['topic'])?['status'=>1,'topicid'=>$_GET['topic']]:'';
		$search = isset($_GET['topic'])?['topic'=>$_GET['topic']]:'';

		$data = $news::where($top)->limit($start,$len)->order(['id'=>'desc'])->select();
		$n = $news::where($top)->count();
		$page = $this->PageList($n,$page,$len,$search);

		$this->assign(['data'=>$data,'page'=>$page]);		
		return view('topic/review');
	}

	public function Reviewadd ()
	{
		$topic = DB::table('tp_topic') -> where(['status'=>1]) -> field('id,title') -> select();

		return view('topic/reviewadd',['topic'=>$topic]);
	}

	public function Reviewinsert (Request $request)
	{
		$data = $request->except(['file']);
		$data['addtime'] = time();
        $files = request()->file('file');
        $newname = '';
        foreach($files as $file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'images/review');
	        if($info){
	            $newname .= ','.$info->getSaveName();
	        }else{
                echo "<script>alert('上传失败，请确认图片格式正确');location.href=history.back(-1);</script>";
	        }
        }
        $data['picture'] = trim($newname,',');
        $res = DB::table('tp_review') -> insert($data);
        if ($res) {
        	echo '<script>alert("添加成功");location.href=history.back(-1)</script>';
        } else {
        	echo '<script>alert("添加失败，请联系管理员");location.href=history.back(-1)</script>';
        }
	}

	public function Reviewedit ()
	{
		if (isset($_GET['id'])) {
			$news = new \app\admin\model\Review;
			$data = $news -> where(['id'=>$_GET['id']]) -> find();

			$this->assign(['data'=>$data]);
			return view('topic/edit');
		} else {
			return redirect('review');
		}
	}

	public function Reviewdel ()
	{
		if ($_GET['id']) {
			$old = DB::table('tp_review') -> where(['id'=>$_GET['id']]) -> find();
			$pics = explode(',',$old['picture']);
			if (count($pics)>0) {
				foreach($pics as $pv){
	                $picurl = ROOT_PATH . 'public' . DS . 'images/review/'.$pv;
	                if (is_file($picurl)) {
	                    unlink($picurl);
	                }
				}
			}
			$res = DB::table('tp_review') -> where(['id'=>$_GET['id']]) -> delete();
			if ($res) {
				if ($old['topicid']>0) {
					$newres = DB::table('tp_topic') -> where(['id'=>$old['topicid']]) -> setDec('reviews');
				}
				return redirect('review');
			} else {
				echo '<script>alert("操作失败，请联系管理员");location.href=history.bakc(-1);</script>';
			}
		} else {
			return redirect('review');
		}
	}

	public function Reviewup ()
	{
		if (isset($_GET['sta'])&&isset($_GET['id'])) {
			$res = DB::table('tp_review') -> where(['id'=>$_GET['id']]) -> update(['state'=>$_GET['sta']]);
			if ($res) {
				echo '<script>alert("操作成功");location.href="review.html"</script>';
			} else {
				echo '<script>location.href=history.back(-1)</script>';
			}
		} else {
			return redirect('review');
		}
	}

	public function Topic ()
	{
		$news = new \app\admin\model\Topic;
		$page = isset($_GET['page'])?$_GET['page']:1;
		$len = 10;
		$start = ($page-1)*$len;

		$data = $news::where(['status'=>1])->limit($start,$len)->order(['sort'=>'desc','id'=>'desc'])->select();
		$n = $news::where(['status'=>1])->count();
		$page = $this->PageList($n,$page,$len,'');

		$this->assign(['data'=>$data,'page'=>$page]);
		return view('topic/topic');
	}

	public function Topicadd ()
	{
		return view('topic/up');
	}

	public function Topicedit ()
	{
		if (isset($_GET['id'])) {
			$data = DB::table('tp_topic')->where(['id'=>$_GET['id']])->find();

			$this->assign(['data'=>$data,'action'=>'update']);
			return view('topic/up');
		} else {
			return redirect('topic');
		}
	}

	public function Topicdel ()
	{
		if (isset($_GET['id'])) {
			$old = DB::table('tp_topic') -> where(['id'=>$_GET['id']]) -> field('id,picture') -> find();
            $review = DB::table(['tp_review']) -> where(['topicid'=>$old['id'],'picture'=>['<>','NULL']]) -> field('picture') -> select();
			foreach ($review as $rv) {
				$rvp = explode(',',$rv['picture']);
				foreach($rvp as $pv){
	                $picurl2 = ROOT_PATH . 'public' . DS . 'images/review/'.$pv;
	                if (is_file($picurl2)) {
	                    unlink($picurl2);
	                }
				}
			}

            $revres = DB::table(['tp_review']) -> where(['topicid'=>$old['id']]) -> delete();
            if ($revres) {
				$picurl = ROOT_PATH . 'public' . DS . 'images/topic/'.$old['picture'];
	            if (is_file($picurl)) { unlink($picurl); }
	            $res = DB::table('tp_topic') -> where(['id'=>$old['id']]) -> delete();
	            if ($res) {
	            	echo '<script>alert("操作成功");location.href="topic.html"</script>';
	            } else {
	            	echo '<script>alert("删除失败，请联系管理员");location.href=history.back(-1)</script>';
	            }
            } else {
            	echo '<script>alert("删除失败，部分子课程无法删除");location.href=history.back(-1)</script>';
            }
		} else {
			return redirect('topic');
		}
	}

	public function Subscribe ()
	{
		$news = new \app\admin\model\Subscribe;
		$page = isset($_GET['page'])?$_GET['page']:'1';
		$len = 10;
		$str = ($page-1)*$len;
		$state = '';

		if (!isset($_GET['state'])) {
			$data = $news->join('tp_users','tp_subscribe.usersid=tp_users.id')
					->where(['tp_subscribe.status'=>1])
					->limit($str,$len)
					->order('tp_subscribe.id','desc')
					->field('tp_subscribe.*,tp_users.username')
					->select();
			$n = $news->join('tp_users','tp_subscribe.usersid=tp_users.id')
				 ->where(['tp_subscribe.status'=>1])
				 ->count();
		} else {
			$state = ['state'=>$_GET['state']];
			$data = $news->join('tp_users','tp_subscribe.usersid=tp_users.id')
					->where(['tp_subscribe.status'=>1,'tp_subscribe.state'=>$state['state']])
					->limit($str,$len)
					->order('tp_subscribe.id','desc')
					->field('tp_subscribe.*,tp_users.username')
					->select();
			$n = $news->where(['status'=>1,'state'=>$state['state']])->count();
		}
		$page = $this->PageList($n,$page,$len,$state);

		$this->assign(['data'=>$data,'page'=>$page]);
		return view('subscribe/subscribe');
	}

	public function Subedit ()
	{
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$state = [-1=>'已失效',0=>'未支付',1=>'已支付',2=>'已取消'];
			$sql = 'select tp_subscribe.*,tp_users.username,tp_classes.classname from tp_subscribe 
				left join tp_users on tp_subscribe.usersid=tp_users.id
				left join tp_classes on tp_subscribe.classid=tp_classes.id
				where tp_subscribe.id='.$id;

			$data = DB::query($sql);

			$data = $data[0];
			
			$data['state'] = $state[$data['state']];
			$data['addtime'] = date('m-d h:i',$data['addtime']);
			$data['paytime'] = $data['paytime']>0?date('Y-m-d h:i',$data['paytime']):'未支付';
			$data['is_coupon'] = $data['is_coupon']==0?'否':'是';
			$data['is_rebate'] = $data['is_rebate']==0?'不返利':$data['is_rebate']==1?'返优惠券':'返利';

			$this->assign(['data'=>$data]);
			return view('subscribe/subedit');
		}
	}

	// 文章列表
	public function News ()
	{
		$news = new \app\admin\model\News;
		$page = 1;
		$len = 10;

		$data = $news->where(['status'=>['>','-1']])->order('id','desc')->select();
		$n = $news->where(['status'=>['>','-1']])->count();
		$page = $this->PageList($n,$page,$len,'');

		$this->assign(['data'=>$data,'page'=>$page]);
		return view('news/news');
	}

	public function Newsadd ()
	{
		$parth = DB::table('tp_news_type') -> order('id','desc') -> select();
		$parth = $this->Classification($parth,'parth');

		$this->assign(['parth'=>$parth]);
		return view('news/newsadd');
	}

	// 文章详情
	public function Newsedit (Request $request)
	{
		if (isset($_GET['id'])) {
			$id = $_GET['id'];

			$data = DB::table('tp_news') -> where('id',$id) -> find();
			$parth = DB::table('tp_news_type') -> order('id','desc') -> select();
			$parth = $this->Classification($parth,'parth');

			$this->assign(['data'=>$data,'parth'=>$parth]);
			return view('news/newsedit');
		} else {
			return $this->redirect('news');
		}
	}

	// 文章分类
	public function Newstype ()
	{
		$news = new \app\admin\model\NewsType;

		$data = $news->order('id','desc')->select();
		$data = $this->Classification($data,'parth');

		$this->assign(['data'=>$data]);
		return view('news/newstype');
	}

	public function Newstypeadd ()
	{
		$parth = DB::table('tp_news_type') -> where(['parth'=>0]) -> select();

		$this->assign(['parth'=>$parth]);
		return view('news/newstypeadd');
	}

	public function Newstypeedit ()
	{
		$id = $_GET['id'];
		$data = DB::table('tp_news_type') -> where('id',$id) -> find();
		$parth = DB::table('tp_news_type') -> where(['parth'=>0,'id'=>['<>',$id]]) -> select();

		$this->assign(['data'=>$data,'parth'=>$parth]);
		return view('news/newstypeedit');
	}

	public function Newstypedel ()
	{
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$data = DB::table('tp_news') -> where('typeid',$id) -> find();
			if (!$data) {
				$type = DB::table('tp_news_type') -> where('parth',$id) -> find();
				if (!$type) {
					$res2 = DB::table('tp_news_type') -> where('id',$id) -> find();
					$res = DB::table('tp_news_type') -> where('id',$id) -> delete();
					if ($res) {
		                $picurl = ROOT_PATH . 'public' . DS . 'images/news_type/'.$res2['picture'];
		                if (is_file($picurl)) {
		                    unlink($picurl);
		                }
						return $this->redirect('newstype');
					} else {
						echo "<script>alert('删除失败，未知错误！请联系管理员');location.href=history.back(-1);</script>";
					}
				} else {
					echo "<script>alert('删除失败，该分类下含有子分类');location.href=history.back(-1);</script>";
				}
			} else {
				echo "<script>alert('删除失败，该分类下内容不为空');location.href=history.back(-1);</script>";
			}
		} else {
			return $this->redirect('newstype');
		}
	}

	// 网站公告
	public function Notice ()
	{
		$data = DB::table('tp_notice') -> order(['sort'=>'desc','id'=>'desc']) -> select();

		$this->assign(['data'=>$data]);
		return view('notice/notice');
	}

	// 商务合作
	public function Audit ()
	{
		$news = new \app\admin\model\Audit;
		$page = isset($_GET['page'])&&$_GET['page']>0?$_GET['page']:1;
		$len = 10;
		$start = ($page-1)*$len;
		$status = isset($_GET['sta'])&&$_GET['sta']>0?['=',$_GET['sta']]:['>','-1'];
		$data = $news->where(['status'=>$status])->limit($start,$len)->order('id','desc')->select();
		$n = $news->where(['status'=>$status])->count();

		$page = $this->PageList($n,$page,$len,isset($_GET['sta'])&&$_GET['sta']>0?['sta'=>$_GET['sta']]:'');

		$this->assign(['data'=>$data,'page'=>$page]);
		return view('audit/audit');
	}

	public function Auditedit ()
	{
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$data = DB::table('tp_audit') -> where('id',$id) -> find();
			if ($data['status']==1) {
				$data = DB::table('tp_audit') -> where('id',$id) -> update(['status'=>'2']);
			}

			$this->assign(['data'=>$data]);
			return view('audit/auditedit');
		} else {
			return $this->redirect('audit');
		}
	}

	// 提现申请
	public function Withdraw ()
	{
		$news = new \app\admin\model\Withdraw;
		$page = isset($_GET['page'])&&$_GET['page']>0?$_GET['page']:1;
		$len = 10;
		$start = ($page-1)*$len;
		$status = isset($_GET['sta'])&&$_GET['sta']>=0?['=',$_GET['sta']]:['>','-1'];
		$data = $news->where(['status'=>$status])->limit($start,$len)->order('id','desc')->select();
		$n = $news->where(['status'=>$status])->count();

		$page = $this->PageList($n,$page,$len,isset($_GET['sta'])&&$_GET['sta']>0?['sta'=>$_GET['sta']]:'');

		$this->assign(['data'=>$data,'page'=>$page]);
		return view('withdraw/withdraw');
	}

	public function Apply ()
	{
		$news = new \app\admin\model\Apply;
		$page = isset($_GET['page'])?$_GET['page']:'1';
		$len = 10;
		$start = ($page-1)*$len;
		$status = isset($_GET['sta'])&&$_GET['sta']>=0?['=',$_GET['sta']]:['>','-1'];

		$data = $news::where(['status'=>1,'state'=>$status])->order(['id'=>'desc'])->limit($start,$len)->select();
		$n = $news::where(['status'=>1,'state'=>$status])->count();
		$page = $this->PageList($n,$page,$len,isset($_GET['sta'])&&$_GET['sta']>0?['sta'=>$_GET['sta']]:'');

		$this->assign(['data'=>$data,'page'=>$page]);
		return view('apply/apply');
	}

	public function Distrilog ()
	{
		$news = new \app\admin\model\Distrilog;
		$page = isset($_GET['page'])&&$_GET['page']>0?$_GET['page']:1;
		$len = 10;
		$start = ($page-1)*$len;
		$status = isset($_GET['sta'])&&$_GET['sta']>0?['=',$_GET['sta']]:['>','-1'];
		$data = $news->where(['status'=>$status])->limit($start,$len)->order('id','desc')->select();
		$n = $news->where(['status'=>$status])->count();

		$page = $this->PageList($n,$page,$len,isset($_GET['sta'])&&$_GET['sta']>0?['sta'=>$_GET['sta']]:'');
			$yj = DB::table('tp_distrilog') -> where('status',1) -> sum('money');
			$ej = DB::table('tp_distrilog') -> where('status',2)->sum('money');
		$this->assign(['data'=>$data,'page'=>$page,'yj'=>$yj,'ej'=>$ej]);
		return view('distri/distrilog');
	}

	public function Distriedit ()
	{
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$data = DB::table('tp_distrilog')-> where('id',$id) -> find();
			$this->assign(['data'=>$data]);
			return view('distri/distriedit');
		} else {
			return $this->redirect('distrilog');
		}
	}

	// 添加页
	public function Add ()
	{
		if (isset($_GET['db'])&&!empty($_GET['db'])) {
			$db = $_GET['db'];
			return view($db.'/add'.$db);
		} else {
			return redirect('/');
		}
	}

	// 添加操作
	public function Insert (Request $request)
	{
		if (isset($_POST['db'])&&!empty($_POST['db'])) {
			$db = $_POST['db'];
			if ($db == 'manager') {
				$data = $request->except(['db','newpass']);
				if (isset($_POST['password'])&&!empty($_POST['password'])) {
					if ($_POST['password']!=$_POST['newpass']) {
						echo "<script>alert('两次密码不一致');location.href=history.back(-1);</script>";
					} else {
						$old = DB::table('tp_'.$db)->where('username',$_POST['username'])->find();
						if ($old) {
							echo "<script>alert('用户名已存在');location.href=history.back(-1);</script>";
						} else {
							$data['password'] = $this->Encrypt($data['password']);
							$data['addtime'] = time();
						}
					}
				} else {
					echo "<script>alert('请输入密码');location.href=history.back(-1);</script>";
				}
			} else if ($db == 'index_list') {
				if ($_POST['state']==1) {
					$data = $request -> except(['db','file','url','price']);
					$data['goodsid'] = implode(',',$_POST['goodsid']);
				} else {
					$data = $request -> except(['db','file','goodsid']);
					$file = request()->file('file');
	                if($file){
	                    $info = $file->move(ROOT_PATH . 'public' . DS . 'images/'.$db);
	                    if($info){
	                        $newname = $info->getSaveName();
	                    }else{
	                        echo "<script>alert('上传失败，请确认图片格式正确');location.href=history.back(-1);</script>";
	                    }
	                	$data['picture'] = $newname;
	                }
				}
				$data['addtime'] = time();
			} else if ($db == 'shuffing' || $db == 'news_type' || $db == 'news' || $db == 'classes' || $db == 'usersay' || $db == 'topic') {
				$data = $request->except(['db','file']);
				if ($db == 'shuffing') {
					$data['starttime'] = strtotime($data['starttime']);
					$data['endtime'] = strtotime($data['endtime']);	
				} else {
					$data['addtime'] = time();
				}
                $file = request()->file('file');
                if($file){
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'images/'.$db);
                    if($info){
                        $newname = $info->getSaveName();
                    }else{
                        echo "<script>alert('上传失败，请确认图片格式正确');location.href=history.back(-1);</script>";
                    }
                	$data['picture'] = $newname;
                }
			} else {
				$data = $request->except(['db','newpass']);
			}
			$res = DB::table('tp_'.$db)->insert($data);
			if ($res) {
				echo "<script>alert('添加成功');location.href=history.back(-1);</script>";
			}
		} else {
			echo "<script>location.href=history.back(-1);</script>";
		}
	}

	// ///////////////////////////////////////////////
	// 数据添加
	// 参数 		类型 		意义
	// $db 			String 		添加表
	// $arr 		Array 		添加数据
	// 返回值 		Boolen
	// ///////////////////////////////////////////////
	private function InsertData ($db,$arr)
	{
		$res = DB::table("tp_".$db)
			   ->insert($arr);
		if ($res) {
			return 1;
		} else {
			return 0;
		}
	}

	// 修改页
	public function Edit ()
	{
		if (isset($_GET['db'])&&!empty($_GET['db'])&&isset($_GET['id'])&&!empty($_GET['id'])) {
			$db = $_GET['db'];
			$id = $_GET['id'];
			$data = DB::table('tp_'.$db) -> where('id',$id) -> find();
			if ($data) {
				$this->assign("data",$data);
				return view($db.'/edit'.$db);
			} else {
				return redirect('/');
			}
		} else {
			return redirect('/');
		}
	}

	public function Update (Request $request)
	{
		if (isset($_POST['db'])&&!empty($_POST['db'])) {
			$db = $_POST['db'];
			$id = $_POST['id'];
			if ($db == 'manager') {
				$data = $request->except(['db','newpass','id','password','goodsid']);
				if (isset($_POST['password'])&&!empty($_POST['password'])) {
					if ($_POST['password']!=$_POST['newpass']) {
						echo "<script>alert('两次密码不一致');location.href=history.back(-1);</script>";
					} else {
						$data['password'] = $this->Encrypt($_POST['password']);
					}
				}
				$old = DB::table('tp_'.$db)->where('username',$_POST['username'])->find();
				if ($old) {
					if ($old['id']!=$id)
						echo "<script>alert('用户名已存在');location.href=history.back(-1);</script>";
				} 
				$data['updatetime'] = time();
			} else if ($db == 'index_list') {
				if ($_POST['state']==1) {
					$data = $request -> except(['db','file','url','price','oldpic']);
					if (isset($_POST['goodsid'])) {
						$data['goodsid'] = implode(',',$_POST['goodsid']);
					}
				} else {
					$data = $request -> except(['db','file','goodsid','oldpic']);
					$file = request()->file('file');
	                if($file){
		                $picurl = ROOT_PATH . 'public' . DS . 'images/'.$db.'/'.$_POST['oldpic'];
		                if (is_file($picurl)) {
		                    unlink($picurl);
		                }
	                    $info = $file->move(ROOT_PATH . 'public' . DS . 'images/'.$db);
	                    if($info){
	                        $newname = $info->getSaveName();
	                    }else{
	                        echo "<script>alert('上传失败，请确认图片格式正确');location.href=history.back(-1);</script>";
	                    }
	                	$data['picture'] = $newname;
	                }
				}
				$data['updatetime'] = time();
			} else if ($db == 'shuffing' || $db == 'news_type' || $db == 'news' || $db == 'classes' || $db == 'usersay' || $db == 'topic') {
				$data = $request->except(['db','file','oldpic']);
				if ($db == 'shuffing') {
					$data['starttime'] = strtotime($data['starttime']);
					$data['endtime'] = strtotime($data['endtime']);	
				} else if ($db != 'news_type') {
					$data['updatetime'] = time();
				}
				$file = request()->file('file');
                if($file){
	                $picurl = ROOT_PATH . 'public' . DS . 'images/'.$db.'/'.$_POST['oldpic'];
	                if (is_file($picurl)) {
	                    unlink($picurl);
	                }
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'images/'.$db);
                    if($info){
                        $newname = $info->getSaveName();
                    }else{
                        echo "<script>alert('上传失败，请确认图片格式正确');location.href=history.back(-1);</script>";
                    }
                	$data['picture'] = $newname;
                }
			} else if ($db == 'coupon') {
				$data = $request -> except('db');
				$data['starttime'] = strtotime($data['starttime']);
				$data['endtime'] = strtotime($data['endtime']);
				$data['updatetime'] = time();
			} else {
				$data = $request->except('db');
				$data['updatetime'] = time();
			}
			$res = DB::table('tp_'.$db)->where('id',$id)->update($data);
			if ($res) {
				echo "<script>alert('修改成功');location.href=history.back(-1);</script>";
			} else {
				echo "<script>alert('修改失败，请联系管理员');location.href=history.back(-1);</script>";
			}
		} else {
			echo "<script>location.href=history.back(-1);</script>";
		}
	}

	// 状态修改
	public function upState (Request $request)
	{
		$db = $_POST['db'];
		$id = $_POST['id'];
		$state = 1-$_POST['state'];

		$res = DB::table('tp_'.$db)
			   ->where('id',$id)
			   ->update(['state'=>$state]);
		if ($res) { 
			return $state;
		} else { 
			return -1; 
		}
	}

	// 审核操作
	public function Checks ()
	{
		$db = isset($_POST['db'])?$_POST['db']:'withdraw';
		$id = $_POST['id'];
		$s = $_POST['s'];
		if ($db == 'withdraw') {
			$res = DB::table('tp_withdraw') -> where(['id'=>$id,'status'=>0]) -> update(['status'=>$s]);
		} else {
			$res = DB::table('tp_'.$db) -> where(['id'=>$id]) -> update(['state'=>$s]);
		}
		
		if ($res) {
			return '1';
		} else {
			return '0';
		}
	}

	// 删除操作
	public function Del ()
	{
		$db = $_GET['db'];
		$id = $_GET['id'];
		if ($db == 'shuffing' || $db == 'index_list' || $db == 'usersay') {
			$res = DB::table('tp_'.$db)->where('id',$id)->find();
			$picsarr = [$res['picture']];
			$this->PicsRemove($db,$picsarr);
		} else if ($db == 'topic') {
			$res = DB::table('tp_'.$db)->where('id',$id)->find();
			$picsarr = [$res['picture']];
			$this->PicsRemove($db,$picsarr);
		} else if ($db == 'distrilog' || $db == 'withdraw') {
			echo "<script>location.href=history.back(-1);</script>";
		}
		$res = DB::table('tp_'.$db)->where('id',$id)->delete();
		if ($res) {
			echo "<script>alert('删除成功');location.href='".url($db)."'</script>";
		} else {
			echo "<script>alert('删除失败，请联系管理员');location.href=history.back(-1);</script>";
		}
	}

	// 软删除
	public function Dels ()
	{
		$db = $_GET['db'];
		$id = $_GET['id'];
		$res = DB::table('tp_'.$db)->where('id',$id)->update(['status'=>-1]);
		if ($res) {
			echo "<script>alert('删除成功');location.href='".url($db)."'</script>";
		} else {
			echo "<script>alert('删除失败，请联系管理员');location.href=history.back(-1);</script>";
		}
	}

	// 批量删除操作
	public function Checkdel ()
	{
		if (isset($_POST['check'])&&!empty($_POST['check'])) {
			$db = $_POST['db'];
			$where = implode(',',$_POST['check']);
			$res = DB::table('tp_'.$db) -> where('id','in',$where) -> delete();
			if ($res) {
				echo "<script>alert('操作成功');location.href='".$db."';</script>";
			} else {
				echo "<script>alert('删除失败，请联系管理员');location.href=history.back(-1);</script>";
			}
		} else {
			echo "<script>alert('请选择要删除内容');location.href=history.back(-1);</script>";
		}
	}

	// 空操作
	public function _empty ()
	{
		// echo "<script>parent.location.href='".url('/admin/admin')."'</script>";
	}

	// ////////////////////////////////////////////////
	// 分页
	// 参数 		类型		意义
	// $total 		int 		数据总数
	// $page 		int 		当前页
	// $len 		int 		每页个数
	// $search 		String 		搜索
	// 返回字符串   String
	// ////////////////////////////////////////////////
	private function PageList ($total,$page,$len,$search)
	{
		$get = '';
		if (!empty($search)) {
			foreach($search as $sk=>$sv){
				$get .= '&'.$sk.'='.$sv;
			}
		}
		$pagestr = '<div class="message">共<i class="blue">'.$total.'</i>条记录，当前显示第&nbsp;<i class="blue">'.$page.'&nbsp;</i>页</div><ul class="paginList">';
	    $pagestr .=	'<li class="paginItem" title="首页"><a href="?page=1'.$get.'"><span class="pagepre"></span></a></li>';
	    $pn = ceil($total/$len);
	    for ($i = 1;$i <= $pn;$i++) {
	    	if ($page == $i) {
	    		$pagestr .= '<li class="paginItem current"><a href="?page='.$i.$get.'">'.$i.'</a></li>';
	    	} else {
	    		$pagestr .= '<li class="paginItem"><a href="?page='.$i.$get.'">'.$i.'</a></li>';
	    	}
	    }

        $pagestr .= '<li class="paginItem" title="末页"><a href="?page='.$pn.$get.'"><span class="pagenxt"></span></a></li></ul>';

		return $pagestr;
	}

	// ////////////////////////////////////////////////
	// 图片删除
	// 参数			类型		意义
	// filename 	String 		文件名
	// $array 		Array 		图片数组
	// 返回 		布尔型
	// ////////////////////////////////////////////////
	private function PicsRemove ($filename,$array)
	{
		foreach ($array as $v) {
			$pic = ROOT_PATH . 'public' . DS . 'images/'.$filename.'/'.$v;
            if (is_file($pic)) {
                unlink($pic);
            }
		}
	}

	// ////////////////////////////////////////////////
	// 数据分级classification
	// $array 		Array 		数组
	// $path 		String 		分级字段
	// 返回 		Array
	// ////////////////////////////////////////////////
	private function Classification ($array,$parth)
	{
		$newarr = array();
		$n = count($array);
		if ($n > 0) {
			foreach ($array as $v) {
				$newarr[$v[$parth]][] = $v;
			}
		}
		return $newarr;
	}

	// ////////////////////////////////////////////////
	// 删除关联表数据
	// 参数			类型		意义
	// $dbarr 		Array 		关联数据库
	// $field 		String 		匹配字段
	// 返回 		Boolen
	// ////////////////////////////////////////////////

	// 加密
	private function Encrypt ($password)
	{
		return md5(md5($password));
	}
}