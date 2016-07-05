<?php
/**
 * 
 * 介绍内容控制器
 * @author uclnn
 *
 */
class AboutAction extends AdminAction {
	
	function _initialize() {
		parent::_initialize ();
		$this->setModel('News');
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
	
	public function isHomeList($categoryDao) {
		$where['is_home'] = 1;
		$this->_dataPage( $categoryDao, $this->c_root, $where );
		$this->display ('index');exit;
	}
	
	public function isTopList($categoryDao) {
		$where['is_top'] = 1;
		$this->_dataPage( $categoryDao, $this->c_root, $where );
		$this->display ('index');exit;
	}
	
	public function isPublish1List($categoryDao) {
		$where['is_publish'] = 1;
		$this->_dataPage( $categoryDao, $this->c_root, $where );
		$this->display ('index');exit;
	}
	
	public function isPublish0List($categoryDao) {
		$where['is_publish'] = 0;
		$this->_dataPage( $categoryDao, $this->c_root, $where );
		$this->display ('index');exit;
	}
	
	
	//单页添加
	public function saveOne() {
		$this->setJumpUrl( 'About/index/cid/'.$_SESSION['sidemenu_cid'].'/lang/'.$_SESSION['sidemenu_lang'] );
		$image = $this->_img_upload('news');
		if( !empty($image) ) {
			$_POST['image'] = $image;
		}
		$this->_saveOne($_POST);
	}
	
	//删除封面
	public function deleteImage() {
		exit($this->_deleteImage($this->upload_root_path.'images/news/'));
	}
	
	public function delete(){
		if( isset( $_GET['id'] ) ) {
			$this->_deleteById();
		} else {
			$this->_delete();
		}
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