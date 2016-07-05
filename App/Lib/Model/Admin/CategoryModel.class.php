<?php
class CategoryModel extends Model {
	
	//通过ID获取别名
	public function getAlias( $cid ) {
		return $this->getField('alias', array('id'=>$cid));
	}
	
	//通过别名获取ID
	public function getId( $alias, $lang ) {
		return $this->getField('id', array('alias'=>$alias, 'lang'=>$lang));
	}
	
	public function getTitle( $id ) {
		return $this->where ( array('id'=>$id) )->getField('title');
	}
	
	//获取向上正确的层次
	public function getUpLevels( $id ) {
		$levels = $this->_findUpLevels($id);
		return substr($levels, 1);
	}
	
	//向上递归分类层次ID
	private function _findUpLevels($id) {
		$pid = $this->getField('pid', array('id'=>$id));
		if( $pid != 0 ) {
			return $levelsStr .= $this->_findUpLevels($pid).'|'.$pid;
		}
	}
	
	
	private $levelsDownStr = '';//记录向下层次所有ID
	
	//获取向下正确的层次
	function getDownLevels( $cid ) {
		$this->_findDownLevels($cid);
		$levelsDownStr = $this->levelsDownStr;
		$this->levelsDownStr = '';
		return $levelsDownStr.$cid;
		//return substr($this->levelsDownStr, 1);
	}

	//向下递归分类层次ID
	private function _findDownLevels( $pid ) {
		$list = $this->where(array('pid'=>$pid))->select();
		foreach ($list as $key => $value) {
			$this->levelsDownStr .= $value['id'].',';
			$this->_findDownLevels($value['id']);
		}
		
	}
}