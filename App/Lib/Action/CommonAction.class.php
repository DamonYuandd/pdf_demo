<?php

class CommonAction extends Action {

	public $modelDao = null;

	function _initialize() {

	}

	/**
	 * 实例化模型 - 前提是需要调用操作数据库方法
	 *
	 * @param string $modelName 模型名称，如：Menber
	 */
	protected function setModel($modelName) {
		$this->modelDao = D ( $modelName );
	}

	/**
	 * 单个数据表查询分页
	 *
	 * @param array $where 查询条件
	 * @param string $order 排序
	 * @param int $rowpage 每页显示行数
	 */
	protected function page($where, $rowpage = 10, $sortBy = '', $asc = false) {

		//排序字段 默认为主键名
		if (isset($_REQUEST ['_order'])) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = !empty($sortBy) ? $sortBy : $this->modelDao->getPk();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset($_REQUEST ['_sort'])) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
			
		/*分页*/
		import ( "ORG.Util.Page" );
		$count = $this->modelDao->where ( $where )->count ();
		$page = new Page ( $count, $rowpage );
		$dataList = $this->modelDao->where ( $where )->order("`" . $order . "` " . $sort)->limit ( $page->firstRow . ',' . $page->listRows )->select ();

		/*在URL添加参数*/
		foreach ( $where as $key => $val ) {
			if (!is_array($val)) {
				$p->parameter .= "$key=" . urlencode($val) . "&";
			}
		}
		//$page->setConfig ( "theme", "%first% %upPage% %linkPage% %downPage% %end%" );
		$pageBar = $page->show ();
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		$this->assign ( 'pageBar', $pageBar );
		$this->assign ( 'sort', $sort);
		$this->assign ( 'totalRows', $count );
		$this->assign ( 'rowpage', $rowpage );
		return $dataList;
	}

}
?>