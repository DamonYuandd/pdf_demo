<?php
/**
 * 
 * 广告管理控制器
 * @author uclnn
 *
 */
class AdvertAction extends AdminAction {
	
	function _initialize() {
		parent::_initialize ();
		$this->setModel('Advert');
	}
	
	public function index() {
		$cid = $_REQUEST['cid'];
		if( !empty( $cid ) ) {
			
			$categoryDao = D('Admin.Category');
			$category = $categoryDao->field('alias,tpl_one')->where(array('id'=>$cid))->find();
			$alias = $category['alias'];
			$tpl_one = $category['tpl_one'];
			
			if( $tpl_one=='auto' ) { //设置呈现样式为“自动”会自动选择下一级的第一个分类
				$this->goToCategoryFirst( $cid );
			} elseif( $tpl_one=='one' ) {
				$this->_oneContent($cid); //单页显示方式
			}
			
			$this->_dataPage($categoryDao, $cid, $where);
			
			$this->display ();
		}
	}
	
	public function edit(){
		
		$this->_edit();
		
		$categoryDao = D('Admin.Category');
		$alias = $categoryDao->getAlias( $_GET['cid'] );
		if( $alias=='advert_home_switchover' ) {//跳转到首页图片切换独立编辑页面
			$this->display('edit_switchover');
		} else {
			$this->display ();
		}
	}
	
	public function add(){
		$this->_uploads();
		$this->_add2($_POST);
	}

	public function update(){
		$this->_uploads();
		$this->_update2($_POST);
	}
	
	//添加、更新上传通用
	private function _uploads() {
		$this->setJumpUrl( 'Advert/index/cid/'.$_SESSION['sidemenu_cid'].'/lang/'.$_SESSION['sidemenu_lang'] );
		$image = $this->_img_upload('advert');
		if( !empty($image) ) {
			$_POST['image'] = $image;
		}
	}
	
	public function delete(){
		$this->_delete();
	}
	
	public function deleteById(){
		$this->_deleteById();
	}
	
	//删除封面
	public function deleteImage() {
		exit($this->_deleteImage($this->upload_root_path.'images/advert/'));
	}
	
	public function ordernum(){
		$this->_ordernum();
	}
	
	public function move(){
		$this->_move();
	}
	
	public function copy() {
		$this->_copy();
	}
	
	public function isPublish() {
		$this->_updateField('is_publish');
	}
	
	public function isHome() {
		$this->_updateField('is_home');
	}
	
	public function isTop() {
		$this->_updateField('is_top');
	}
	
}
?>