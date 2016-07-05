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
	
	private $levelsUpStr = '';//记录向上层次所有ID
	
	//获取向上正确的层次
	public function getUpLevels( $id, $lang ) {
		$levels = $this->_findUpLevels($id, $lang);
		return substr($levels, 1);
	}
	
	//向上递归分类层次ID
	private function _findUpLevels($id, $lang) {
		$pid = $this->getField('pid', array('id'=>$id,'lang'=>$lang));
		if( $pid != 0 ) {
			return $levelsStr .= $this->_findUpLevels($pid).'|'.$pid;
		}
	}
	
	
	private $levelsDownStr = '';//记录向下层次所有ID
	
	//获取向下正确的层次
	/*function getDownLevels( $cid, $lang  ) {
		$this->_findDownLevels($cid, $lang );
		$levelsDownStr = $this->levelsDownStr;
		$this->levelsDownStr = '';
		return $levelsDownStr.$cid;
		//return substr($this->levelsDownStr, 1);
	}*/
	function getDownLevels( $cid, $lang,$hardware ) {
		$this->_findDownLevels($cid, $lang ,$hardware);
		$levelsDownStr = $this->levelsDownStr;
		$this->levelsDownStr = '';
		return $levelsDownStr.$cid;
		//return substr($this->levelsDownStr, 1);
	}

	//向下递归分类层次ID
	private function _findDownLevels( $pid, $lang , $hardware = 'pc') {
		$list = $this->where(array('pid'=>$pid,'hardware' => $hardware,'lang'=>$lang))->order('ordernum desc')->select();
		foreach ($list as $key => $value) {
			$this->levelsDownStr .= $value['id'].',';
			$this->_findDownLevels($value['id'], $lang);
		}
	}
}