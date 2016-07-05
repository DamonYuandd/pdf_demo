<?php
// 后台用户模块
class UserAction extends AdminAction {
	
	function _initialize() {
		parent::_initialize ();
		$this->setModel('Admin');
	}
	
	public function index() {
		$rowpage = $_REQUEST['rowpage'];
		$rowpage = empty($rowpage)?10:$rowpage;
		$this->assign('dataList', $this->page($where, $rowpage, 'create_time desc'));
		$this->assign('rowpage', $rowpage);
		$this->display ('User/index');
	}

	public function edit() {
		$id = $_GET['id'];
		if( !empty($id) ) {
			$user = $this->modelDao->find($id);
			$this->assign('obj', $user);
		}
		$this->display();
	}
	
	//授权
	public function accredit() {
		$accessDao = M('Access');
		if( $this->isAjax() ) {
			//权限设置
			$action = $_GET['admin_id'].'-'.$_GET['nodes'];
			if( isset($_GET['checked']) ) {
				$count = $accessDao->where(array('nodes'=>$action))->count();
				if( $count==0 ) {
					$accessDao->add(array('nodes'=>$action));
				}
			} else {
				$accessDao->where(array('nodes'=>$action))->delete();
			}
		} else {//授权页面
			$this->_assignModuleList();
			$nodesList = $accessDao->where(array('nodes'=>array('like','%'.$_GET['id'].'-%')))->select();
			$user = $this->modelDao->find($_GET['id']);
			$this->assign('obj', $user);
			$this->assign('nodesList', $nodesList);
			$this->display();
		}
	}

	
	public function add(){
		$data = $_POST;
		$data['password'] = md5($data['password']);
		$this->_add2($data);
	}

	public function update(){
		$data = $_POST;
		$password = trim($data['password']);
		if( empty($password) ) {
			unset($data['password']);
		} else {
			$data['password'] = md5($password);
		}
		$result = $this->modelDao->save ( $data );
		if(false !== $result) {
			$this->success ( '修改成功！');
		} else {
			$this->error ( '修改失败！');
		}
	}


	//重置密码
	public function password() {
		if( $this->isPost() ) {
			$admin_id = $_SESSION['admin']['id'];
			$oldpassword = $_POST['oldpassword'];
			$count = $this->modelDao->where(array('id'=>$admin_id, 'password'=>md5($oldpassword)))->count();
			if( $count == 0 ) {
				$this->error('旧的密码不一致！');
			}
			
			$password = $_POST['password'];
			$repassword = $_POST['repassword'];
			if( $repassword != $password ) {
				$this->error('两次输入密码不一致！');
			}
			
			$data['password'] = md5($password);
			$data['id'] = $admin_id;
			$result	= $this->modelDao->save($data);
			if(false !== $result) {
				$this->success("密码修改为 $password");
			}else {
				$this->error('重置密码失败！');
			}
		} else {
			$this->display('');
		}
	}
	
	public function isPublish() {
		$this->_updateField('is_publish');
	}
	
	public function deleteById() {
		$this->_deleteById();
	}
	function register(){
		$db = M('member');
		import('ORG.Net.IpLocation');
		$ip=new Iplocation();
		$check = $db->where('username = \''.$_POST['username'].'\'')->find();
		if(!$_POST['username'] || !$_POST['realname'] || !$_POST['password'])
		{
			$this->error ( '必须填写所有内容' );
		}
		if($check)
		{
			$this->error ( '用户名重复' );
		}
		if($_POST['password'] != $_POST['re_password'])
		{
			$this->error ( '两个密码不一致' );
		}
		$data = $_POST;
		$data['is_publish'] = 1;
		$data['ordernum'] = 999;
		$data['category_id'] = 336;
		$data['last_login_ip'] = $ip->get_client_ip();
		$data['update_time'] = time();
		$data['create_time'] = time();
		$data['last_login_time'] = time();
		$data['password'] = md5($_POST['password']);
		if($db->add($data))
		{
			$this->success ( '注册成功！' );
		}else
		{
			$this->error ( '网络原因导致注册失败，请重试' );
		}
	}
	
}
?>