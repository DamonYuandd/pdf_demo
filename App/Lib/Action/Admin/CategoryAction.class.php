<?php
/**
 * 
 * 分类管理
 * @author uclnn
 *
 */
class CategoryAction extends AdminAction {
	
	function _initialize() {
		parent::_initialize ();
		$this->setModel('Category');
	}
	
	public function index() {
	}
	
	//编辑分类
	public function edit() {
		if( !empty($_GET['id']) ) {
			$categoryDao = M ( "Category" );
			$category = $categoryDao->find($_GET['id']);
			$this->assign('obj', $category);
			$this->assign('levels', $category['levels']);
		}
		$this->assign('nav_site', '网站后台 > '.$this->_getDefLangTitle2());
		$this->display();
	}
	
	//编辑添加分类
	public function editAdd() {
		$categoryDao = M ( "Category" );
		$category = $categoryDao->where(array('id'=>$_GET['id']))->field('lang')->find();
		$this->assign('obj', array('pid'=>$_GET['id'],'lang'=>$category['lang']));
		$this->assign('nav_site', '网站后台 > '.$this->_getDefLangTitle2());
		$this->display('edit');
	}
	
	//添加子分类页面
	public function addSubCategory() {
		$categoryDao = D ( "Admin.Category" );
		$category = $categoryDao->find($_GET['id']);
		$categoryList = $categoryDao->where ( array('pid'=>$_GET['pid'], 'is_fixed'=>0, 'lang'=>$lang,'is_publish'=>1) )->order('ordernum desc')->select ();
		$this->assign('categoryList', $categoryList);
	}

	//编辑留言板分类
	public function edit_guestbook() {
		$this->selectCategoryByPid();
		$this->getCategory();
		$this->display();
	}
	
	public function save() {

		$image = $this->_img_upload('category','image',false);
		if( !empty($image) ) {
			$_POST['image'] = $image;
		}
		$data = $_POST;

		if( empty($data['id']) ) {
			$pid = $data['pid'];
			$levels = $this->modelDao->getField('levels', array('id'=>$pid));
			$data['levels'] = $levels.'|'.$pid;
			$this->_add2($data);
		} else {
			$this->_update2($data);
		}
	}
	
	public function add() {
	    $this->_imgUploads('category');
		$data = $_POST;
		$pid = $data['pid'];
		$levels = $this->modelDao->getField('levels', array('id'=>$pid));
		$data['levels'] = $levels.'|'.$pid;
		$this->_add($data);
	}
	
	public function update(){
	    $this->_imgUploads('category');
		$this->_update( $_POST );
	}
	
	public function isPublish() {
		$this->_updateField('is_publish');
	}
	
	public function ordernum(){
		$this->_ordernum();
	}
	
	public function move(){
		$ids = $_POST ['ids'];
		if( $_POST['one_category_id']=='pc' || $_POST['one_category_id']=='mobile'){
			$data['hardware'] = $_POST['one_category_id'];
			$category_id = $_SESSION['c_root'];
		} else {
			$category_id = $this->_getLastSelectedCategoryId();
		}
		$category = $this->modelDao->where(array('id'=>$category_id))->find();
		if (! empty ( $ids )) {
			$count = count ( $ids );
			for($i = 0; $i < $count; $i ++) {
				$numAndId = explode ( ',', $ids [$i] );
				$obj = $this->modelDao->where ( 'id=' . $numAndId [1] )->find ();
				$obj ['hardware'] = !empty($data['hardware'])?$data['hardware']:$category['hardware'];
				$obj ['lang'] = empty($category['lang'])?$_SESSION['lang']:$category['lang'];
				$obj ['levels'] = $category['levels'].'|'.$obj ['pid'];
				$obj ['pid'] = $category_id;
				$result = $this->modelDao->save ( $obj );
			}
			$this->success ( '移动成功！' );
		} else {
			$this->error ( '移动失败！' );
		}
	}
	
	public function copy() {
		$ids = $_POST ['ids'];
		if( $_POST['one_category_id']=='pc' || $_POST['one_category_id']=='mobile'){
			$data['hardware'] = $_POST['one_category_id'];
			$category_id = $_SESSION['c_root'];
		} else {
			$category_id = $this->_getLastSelectedCategoryId();
		}
		$category = $this->modelDao->where(array('id'=>$category_id))->find();
		if (! empty ( $ids )) {
			$count = count ( $ids );
			for($i = 0; $i < $count; $i ++) {
				$numAndId = explode ( ',', $ids [$i] );
				$obj = $this->modelDao->where ( 'id=' . $numAndId [1] )->find ();
				unset($obj ['id']);
				$obj ['hardware'] = !empty($data['hardware'])?$data['hardware']:$category['hardware'];
				if( !empty($_POST['category_lang']) ) {
					$obj ['lang'] = $_POST['category_lang'];
				} else if(!empty($category['lang'])) {
					$obj ['lang'] = $category['lang'];
					$obj ['levels'] = $category['levels'].'|'.$obj ['pid'];
				}
				$obj ['pid'] = $category_id;
				$result = $this->modelDao->add ( $obj );
			}
			$this->success ( '复制成功！' );
		} else {
			$this->error ( '复制失败！' );
		}
		
	}
	
	/*表单下拉列表
	public function selectOption() {
		$pid = $_REQUEST['pid'];
		$categoryDao = M('Category');
		$dataList = $categoryDao->where(array('pid'=>$pid, 'lang'=>$this->lang))->select();
		$options = '<option value="0" selected="">请选择</option>';
		foreach ($dataList as $key => $value) {
			$options .= '<option value="'.$value['id'].'" '.$style.'>'.$value['title'].'</option>';
		}
		exit($options);
	}*/
	
	//获取单个分类信息
	public function getCategory() {
		$id = $_REQUEST['id'];
		$lang = $_REQUEST['lang'];
		if( !empty($id) ) {
			$categoryDao = M('Category');
			if( !empty($lang) ) {
				$where['lang'] = $lang;
			} else {
				$where['id'] = $id;
			}
			
			$category = $categoryDao->where($where)->find();
			if ( $this->isAjax() ) {
				exit(json_encode($category));
			} else {
				$this->assign('obj',$category);
			}
		}
	}
	
	//传入Pid找下级分类
	function selectCategoryByPid() {
		$pid = $_GET ['pid'];
		$hardware = $_SESSION['hardware'];
		$lang_in = getSqlInLangs();
		$category = M ( "Category" );
		$categoryList = $category->where ( array('pid'=>$pid, 'hardware'=>$hardware,'is_publish'=>1,'lang'=>array('in',$lang_in)) )->order('hardware desc,lang desc,ordernum desc')->select ();
		if( $this->isAjax() ) {
			$json ['list'] = $categoryList;
			exit(json_encode ( $json ));
		} else {
			$this->assign('categoryList', $categoryList);
		}
	}
	
	//删除封面
	public function deleteImage() {
		exit($this->_deleteImage($this->upload_root_path.'images/category/'));
	}
	
	//删除分类
	public function deleteCategory() {
		$id = $_GET ['id'];
		if (! empty ( $id )) {
			
			$this->isExistDataByCid( $id );
			
			$categoryDao = M ( 'Category' );
			$count = $categoryDao->where ( array('pid'=>$id) )->count ();
			if( $this->isAjax() ) {
				if ($count > 0) {
					exit('sub_exist');
				} else {
					$categoryDao->delete($id);
					exit('true');
				}
			} else {
				if ($count > 0) {
					$this->error('删除之前请确认没有子分类！');
				} else {
					$categoryDao->delete($id);
					$this->success('删除成功！');
				}
			}
		}
	}
	
	//检查分类是否存在关联的数据
	protected function isExistDataByCid( $cid ) {
		$goodsDao = M('Goods');
		$count = $goodsDao->where(array('category_id'=>$cid))->count();
		if( $count > 0 ) {
			$this->error('检查到这个分类下还有部分产品，请删除后再操作！');exit;
		}
		$marketDao = M('Market');
		$count = $marketDao->where(array('category_id'=>$cid))->count();
		if( $count > 0 ) {
			$this->error('检查到这个分类下还有部分网点，请删除后再操作！');exit;
		}
		$guestbookDao = M('Guestbook');
		$count = $guestbookDao->where(array('category_id'=>$cid))->count();
		if( $count > 0 ) {
			$this->error('检查到这个分类下还有部分留言，请删除后再操作！');exit;
		}
	}
	
	
}
?>