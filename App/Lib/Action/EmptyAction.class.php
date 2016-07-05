<?php
/**
 * 
 * @author uclnn
 *
 */
class EmptyAction extends HomeAction {

	function _initialize() {
		parent::_initialize ();
	}
	
	public function index() {	//不存在模块
		$obj = cateByAlias(MODULE_NAME);
		$other = A("Home.Other");
		if($obj['module']){
			if($obj['module'] == 'News'){
				$other->news();
			}else if($obj['module'] == 'About'){
				$other->about();
			}else{
				echo "<script>window.location.href='".__APP__."';</script>";
			}
		}
		else{
			if($obj['tpl_one'] == 'list'){
				$other->news();
			}else if($obj['tpl_one'] == 'one'){
				$other->about();
			}else{
				echo "<script>window.location.href='".__APP__."';</script>";
			}
		}
	}
	public function _empty() {	//不存在方法
		$other = A("Home.Other");
		if(ACTION_NAME == 'show'){
			$other->show();
		}else{
			echo "<script>window.location.href='".__APP__."';</script>";
		}
	}

}
?>