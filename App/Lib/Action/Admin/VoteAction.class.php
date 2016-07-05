<?php
/**
 * 
 * 介绍内容控制器
 * @author uclnn
 *
 */
class VoteAction extends AdminAction {
	
	function _initialize() {
		parent::_initialize ();
		//$this->setModel('News');
	}
	
	
	//作品列表
	public function index() {
		/*分页*/
		$order = 'id desc';
		import ( "ORG.Util.Page" );
		
		//筛选条件
		
		$where = '';
		$count = M('vote_option')->where ( $where )->count ();
		$page = new Page ( $count, 20 );
		$data = M('vote_option')->where( $where )->order($order)->select();
		
		
		$this->assign('pageBar',$page->show());
		$this->assign('data',$data);
		$this->display ();
	}
	
	
	//删除作品
	public function delete(){
		if(empty($_GET['id'])){
			$this->error ( '错误' );
		}
		$obj = M('vote_option')->where(array('id' => $_GET['id']))->delete();
		if($obj){
			$this->success ( '删除成功！' );
		}else{
			$this->error ( '异常' );
		}
	}
	
	//查看详情
	public function edit(){
		if(empty($_GET['id'])){
			$this->error ( '错误' );
		}
		$obj = M('vote_option')->where(array('id' => $_GET['id']))->find();
		if(!$obj){
			$this->error ( '异常' );
		} 
		
		$this->assign('obj',$obj);
		$this->display ();
	}
	
	//导出exl
	public function export(){
		$this->display();
	}
	
	//更新
	public function update(){
		if(empty($_POST['isFinalist'])){
			$data['isFinalist'] = 0;
		}else{
			$data['isFinalist'] = 1;	
		}
		
		if(empty($_POST['isAwards'])){
			$data['isAwards'] = 0;
		}else{
			$data['isAwards'] = 1;
		}
		$where = array('id' => $_POST['id']);
		M('vote_option')->where($where)->save($data);
		$this->success ( '修改成功！' );
	}
	
}
?>