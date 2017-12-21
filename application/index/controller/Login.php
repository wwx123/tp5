<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Login extends Controller
{
	// 登录页
	public function index ()
	{
		if (null!=cookie('username')) {
			$username = cookie('username');
			$password = md5(md5(cookie('password')));
			$sql = 'select id,username,password,lasttime,lastip,number,state,level from tp_manager where username="'.$username.'" and password="'.$password.'" limit 1';
			$res = DB::query($sql);
			if (!empty($res)) {
				$res = $res[0];
				if ($res['state']>=0) {
					$newres = Db::table('tp_manager') -> where('id',$res['id']) -> update(['lasttime'=>time(),'lastip'=>$_SERVER['REMOTE_ADDR'],'number'=>($res['number']+1)]);
					session('adminid', $res['id']);
					session('admindata', $res);
					$this->redirect('/admin.php');
				}
			}
		}

		cookie('username',null);
		cookie('password',null);
		return $this->fetch('index');
	}

	// 登录判断
	public function submitData ()
	{
		if (isset($_POST['username'])&&!empty($_POST['username'])) {
			$username = $_POST['username'];
			$password = md5(md5($_POST['password']));
			$sql = 'select id,username,password,lasttime,lastip,number,state,level from tp_manager where username="'.$username.'" and password="'.$password.'" limit 1';
			$res = Db::query($sql);
			if (!empty($res)) {
				$res = $res[0];
				if ($res['state']>=0) {
					$newres = Db::table('tp_manager') -> where('id',$res['id']) -> update(['lasttime'=>time(),'lastip'=>$_SERVER['REMOTE_ADDR'],'number'=>($res['number']+1)]);
					if (isset($_POST['remember'])&&!empty($_POST['remember'])) {
						cookie('username',$username);
						cookie('password',$_POST['password']);
					}
					session('adminid', $res['id']);
					session('admindata', $res);
					$this->redirect('/admin.php/admin/index');
				} else {
					echo "<script>alert('该账户已被禁用，请联系管理员');location.href=history.back(-1);</script>";
				}
			} else {
				echo "<script>alert('账号密码不正确');location.href=history.back(-1);</script>";
			}
		} else {
				echo "<script>alert('用户名不能为空');location.href=history.back(-1);</script>";
		}

	}

	// 退出登录
	public function loginout ()
	{
		session('adminid', null);
		session('admindata', null);
		cookie('adminid',null);
		cookie('admindata',null);
		cookie('username',null);
		cookie('password',null);

		$this->redirect('/login');
	}

	// 忘记密码
	public function forget ()
	{
		return $this->fetch('forget');
	}

	// 帮助
	public function help ()
	{
		return $this->fetch('help');
	}

	// 关于
	public function about ()
	{
		return $this->fetch('about');
	}

	public function geta()
	{
		echo '1';
	}

	public function _empty ()
	{
		$this -> redirect('/');
	}
}