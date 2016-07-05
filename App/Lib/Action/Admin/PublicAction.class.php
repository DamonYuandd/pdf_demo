<?php
//不需要权限验证
class PublicAction extends Action {

	// 用户登出
	public function logout()
	{
		if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			unset($_SESSION[C('USER_AUTH_KEY')]);
			$this->redirect('Public/login');
		}else {
			$this->redirect('Public/login');
		}
	}
	
	public function loginPage() {
		$this->assign("jumpUrl",__URL__.'/login/');
	}

	//验证码
	public function verify() {
		$type = isset ( $_GET ['type'] ) ? $_GET ['type'] : 'gif';
		import ( "ORG.Util.Image" );
		Image::buildImageVerify ( 4, 1, $type );
	}

	//请求登录
	//	public function reqLogin() {
	//		if ($_SESSION ["verify"] != md5 ( $_POST ["verify"] )) {
	//			$this->assign ( 'msg', '验证码错误' );
	//		} else {
	//			$condition ["username"] = $_POST ["username"];
	//			$condition ["passwd"] = md5 ( $_POST ["password"] );
	//			$condition ["admin"] = 2;
	//			$menberDao = M ( "Menber" );
	//			$menber = $menberDao->where ( $condition )->find ();
	//			if ($menber) {
	//				Load('extend');
	//				$menber ["client_ip"] = get_client_ip();
	//				$menber ["password"] = "";
	//				$_SESSION ["admin"] = $menber;
	//				$this->redirect ( 'Index/' );
	//			} else {
	//				$this->assign ( 'msg', '用户名或密码不正确' );
	//			}
	//		}
	//		$this->display ( 'login' );
	//	}

	// 检查用户是否登录
	protected function checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('jumpUrl',__APP__.'/Admin/Public/login');
			$this->error('没有登录');
		}
	}

	// 用户登录页面
	public function login() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->display('login');
		}else{
			$this->redirect('Index/index');
		}
	}

	// 登录检测
	public function checkLogin() {
		$domain = $_POST['domain'];
		$username = $_POST['username'];
		$password = $_POST['password'];
		$verify = $_POST['verify'];

		if(empty($username)) {
			$this->error('帐号错误！');
		} elseif (empty($password)){
			$this->error('密码必须！');
		} elseif (empty($verify)){
			$this->error('验证码必须！');
		} else if($_SESSION['verify'] != md5($_POST['verify'])) {
			$this->error('验证码错误！');
		}
		$adminDao = D('Admin');
		$where['username'] = trim($username);
		$where["is_publish"] = array('gt',0);
		$admin = $adminDao->where($where)->find();
		if(false === $admin) {
			$this->error('帐号不存在或已禁用！');
		}else {
			if($admin['password'] != md5($_POST['password'])) {
				$this->error('密码错误！');
			}
			if( $admin['username'] == 'admin' ) { //超级管理员没权限限制
				$admin['superadmin'] = true;
			}
			$admin['contract_number'] = $account['contract_number'];
			$_SESSION[C('USER_AUTH_KEY')]	=	$admin;

			//保存登录信息
			Load('extend');
			$data['id']	= $admin['id'];
			$data['last_login_time']	=	time();
			$data['login_count']	=	array('exp','login_count+1');
			$data['last_login_ip']	=	get_client_ip();
			$adminDao->save($data);
			$this->redirect('Index/index');
		}
	}

	// 更换密码
	public function changePwd()
	{
		$this->checkUser();
		//对表单提交处理进行处理或者增加非表单数据
		if(md5($_POST['verify'])	!= $_SESSION['verify']) {
			$this->error('验证码错误！');
		}
		$map	=	array();
		$map['password']= pwdHash($_POST['oldpassword']);
		if(isset($_POST['account'])) {
			$map['account']	 =	 $_POST['account'];
		}elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
			$map['id']		=	$_SESSION[C('USER_AUTH_KEY')];
		}
		//检查用户
		$User    =   M("User");
		if(!$User->where($map)->field('id')->find()) {
			$this->error('旧密码不符或者用户名错误！');
		}else {
			$User->password	=	pwdHash($_POST['password']);
			$User->save();
			$this->success('密码修改成功！');
		}
	}

	public function profile() {
		$this->checkUser();
		$User	 =	 M("User");
		$vo	=	$User->getById($_SESSION[C('USER_AUTH_KEY')]);
		$this->assign('vo',$vo);
		$this->display();
	}

	// 修改资料
	public function change() {
		$this->checkUser();
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}
		$result	=	$User->save();
		if(false !== $result) {
			$this->success('资料修改成功！');
		}else{
			$this->error('资料修改失败!');
		}
	}
}
?>