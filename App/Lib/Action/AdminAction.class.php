<?php

class AdminAction extends CommonAction {

	protected $lang;//当前语言

	protected $c_root;//当前模块分类根ID

	protected $custom;//定制配置

	protected $admin; //管理员信息
	
	protected $upload_root_path; //上传根目录

	function _initialize() {
		parent::_initialize ();

		//登录与权限检查
		if (!$_SESSION [C('USER_AUTH_KEY')]) {
			echo "<script language=\"javascript\">window.top.location.href='".__APP__."/Admin/Public/login';</script>";
		} else {
			$this->admin = $_SESSION[C('USER_AUTH_KEY')];
		}
		
		//定制配置
		$this->custom = getAccountDiy();
		$_SESSION['lang'] = $this->getDefaultLang('pc');

		//当前终端
		$hardware = $_GET['hardware'];
		if( empty($hardware) && empty($_SESSION['hardware']) ) {//第一次进入设置全部语言
			$_SESSION['hardware'] = 'pc';
			$_SESSION['lang'] = $this->getDefaultLang('pc');
		} else if( !empty($hardware) ) {
			$_SESSION['hardware'] = $hardware;
			if( $hardware=='pc' ) {
				$_SESSION['lang'] = $this->getDefaultLang('pc');
			} elseif( $hardware=='mobile' ) {
				$_SESSION['lang'] = $this->getDefaultLang('mobile');
			}
		}
		
		//当前导航ID,使能读出下级分类
		if( !empty($_GET['c_root']) ) {
			$_SESSION['c_root'] = $_GET['c_root'];
		}
		
		//用会话记录左边菜单ID和语言,常用于操作返回
		$cid = $_GET['cid'];$lang = $_GET['lang'];
		if( !empty($cid) && !empty($lang) ) {
			$_SESSION['sidemenu_cid'] = $_GET['cid'];
			$_SESSION['sidemenu_lang'] = $_GET['lang'];
		}
		
		//$this->upload_root_path = $_SERVER['DOCUMENT_ROOT'].$this->custom['contract_number'].'/'.C('UPLOAD_FILE_RULE');
		//$this->upload_root_path = C('APP_ROOT_PATH').C('UPLOAD_FILE_RULE').'/'.$this->custom['contract_number'].'/';
		//$this->upload_root_path = C('APP_ROOT_PATH').__ROOT__.'/Public/';
		$this->upload_root_path = C('APP_ROOT_PATH');
		$_SESSION['ueditor_upload_Path'] = $this->upload_root_path;
		$this->assign('upload_root_path', C('IMG_URL').'/'.$this->custom['contract_number'].'/');
		$this->assign('custom', $this->custom);
		$this->assign('actionName', MODULE_NAME);
	}
	
	private function getDefaultLang($hardware) {
		$categoryDao = M('Category');
		if( $hardware=='pc' ) {
			return $categoryDao->where(array('pid'=>3,'is_default'=>1))->getField('alias');
		} elseif( $hardware=='mobile' ) {
			return $categoryDao->where(array('pid'=>4,'is_default'=>1))->getField('alias');
		}
	}

	// SWF文件上传
	protected function _swf_upload($uploaddir='', $field='flash') {
		import("ORG.Util.UploadFile");
		$upload = new UploadFile();
		$upload->maxSize = 3292200;
		$upload->allowExts = explode(',', 'swf,');
		$upload->savePath = $uploaddir;
		$upload->saveRule = uniqid;
		if (!$upload->upload()) {
			$this->error($upload->getErrorMsg());
		} else {
			$uploadList = $upload->getUploadFileInfo();
			$_POST[$field] = $uploadList[0]['savename'];
		}

	}

	// 图片上传
	protected function _img_uploads($folder='',$thumb=true,$width=300,$height=300) {
		$upload_dir = $this->upload_root_path.'images/'.$folder.'/';
		import("ORG.Util.UploadFile");
		$upload = new UploadFile();
		$upload->maxSize = 3292200;
		$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
		$upload->savePath = $upload_dir;
		if( $thumb==true ) {
			$upload->thumb = true;
			$upload->imageClassPath = 'ORG.Util.Image';
			$upload->thumbPrefix = 'm_,s_';
			$upload->thumbMaxWidth = '2200,'.$width;
			$upload->thumbMaxHeight = '2200,'.$height;
			$upload->thumbRemoveOrigin = true;
		}
		$upload->saveRule = uniqid;
		if (!$upload->upload()) {
			$this->error($upload->getErrorMsg());
		} else {
			return $upload->getUploadFileInfo();
		}
		
	}
	
	// 图片单张上传
	protected function _img_upload($folder='',$field='image',$thumb=true,$width=300,$height=300) {
		if( empty($_FILES[$field]['name'])) {
			return '';
		}
		$upload_dir = $this->upload_root_path.'images/'.$folder.'/';
		import("ORG.Util.UploadFile");
		$upload = new UploadFile();
		$upload->maxSize = 3292200;
		$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
		$upload->savePath = $upload_dir;
		if( $thumb==true ) {
			$upload->thumb = true;
			$upload->imageClassPath = 'ORG.Util.Image';
			$upload->thumbPrefix = 'm_,s_';
			$upload->thumbMaxWidth = '2200,'.$width;
			$upload->thumbMaxHeight = '2200,'.$height;
			$upload->thumbRemoveOrigin = true;
		}
		$upload->saveRule = uniqid;
		$result = $upload->uploadOne($_FILES[$field]);
		if (!$result) {
			$this->error($upload->getErrorMsg());
		} else {
			return  $result[0]['savename'];
		}
	
	}

	// SWF或图片文件上传
	protected function _swf_img_upload($uploaddir='', $field='flash_img') {
		import("ORG.Util.UploadFile");
		$upload = new UploadFile();
		$upload->maxSize = 3292200;
		$upload->allowExts = explode(',', 'swf,jpg,gif,png,jpeg');
		$upload->savePath = $uploaddir;
		$upload->saveRule = uniqid;
		if (!$upload->upload()) {
			$this->error($upload->getErrorMsg());
		} else {
			$uploadList = $upload->getUploadFileInfo();
			$i=1;
			foreach ($uploadList as $key => $value) {
				$_POST[$field.$i] = $value['savename'];
				$i++;
			}
		}

	}

	


	//条件列表分页
	protected function _dataPage($categoryDao, $cid, $where) {
		if( !empty($cid) ) {
			$levels = $categoryDao->getDownLevels($cid);
			if( empty($levels) ) {
				$levels = $cid;
			}
			$where['category_id'] = array('in', $levels);
		}
		$where['hardware'] = $_SESSION['hardware'];
		$rowpage = $_REQUEST['rowpage'];
		$searchKey = $_REQUEST['searchKey'];
		if( !empty($searchKey) && $searchKey!='请输入关键字' ) {
			$where['_string'] = "title like '%$searchKey%' OR tag like '%$searchKey%'";
		}
		$rowpage = empty($rowpage)?12:$rowpage;
		$this->assign('dataList', $this->page($where, $rowpage));
		$this->assign('rowpage', $rowpage);
		$this->assign('searchKey', empty($searchKey)?'请输入关键字':$searchKey);
	}

	//获取子分类的第一个分类ID并重定向
	public function goToCategoryFirst( $pid ) {
		$data['pid'] = $pid;
		$categoryDao = M('Category','AdvModel');
		$obj = $categoryDao->where( $data )->order('ordernum desc')->first();
		if( empty($obj['url']) ) {
			$this->redirect(MODULE_NAME.'/index', array('cid'=>$obj['id'],'lang'=>$_GET['lang']));
		} else {
			$this->redirect($obj['url'], array('cid'=>$obj['id'],'lang'=>$_GET['lang']));
		}
	}

	//转到单个内容页面
	public function _oneContent($cid) {
		$lang = $_GET['lang'];//切换语言
		$newsDao = M('News');
		$news = $newsDao->where(array('category_id'=>$cid,'hardware'=>$_SESSION['hardware'],'lang'=>$lang))->find();
		$this->assign('obj', $news);
		$this->display ('Index/edit-one');
		exit;
	}

	//单页添加与更新
	public function _saveOne($data) {
		$newsDao = M('News');
		//发布设置
		if (!isset($data['is_publish'])) {
			$data['is_publish'] = 0;
		}
		if( !empty($data['content']) ) {
			//HTML标签转实体
			if (get_magic_quotes_gpc ()) {
				$content = htmlspecialchars ( stripslashes ( $data ['content'] ) );
			} else {
				$content = htmlspecialchars ( $data ['content'] );
			}
			$data['content'] = $content;
		}
		
		if( empty($data['id']) ) { //添加
			$data['update_time'] = time();
			$data['create_time'] = time();
			if($newsDao->add($data)!==false) {
				if (isset($data['synch_mobile'])) {
					$insid = $newsDao->getLastInsID();
					$data['category_id'] = $this->_getModileCategoryId($_POST);
					$synch_msg = $this->_synchMobileOne($data);
				}
				$this->success ( '添加成功！'.$synch_msg);
			} else {
				$this->error('添加失败！');
			}
		} else { //更新
			$data['update_time'] = time();
			if($newsDao->save($data)!==false) {
				if (isset($data['synch_mobile'])) {
					$data['category_id'] = $this->_getModileCategoryId($_POST);//dump($data);exit;
					$synch_msg = $this->_synchMobileOne($data);
				}
				$this->success ( '更新成功！'.$synch_msg);
			} else {
				$this->error('更新失败！');
			}
		}
	}

	//编辑页面
	protected function _edit() {
		$cid = $_GET['cid'];
		$categoryDao = D('Admin.Category');
		$tpl_one = $categoryDao->where(array('id'=>$cid))->getField('tpl_one');
		if( $tpl_one=='one' ) {
			$this->_oneContent($cid); //单页显示方式
		}

		//获取编辑数据
		$id = $_GET['id'];

		$lang = $_GET['lang'];
		if( empty($lang) ) {
			$lang = $this->lang;
		}

		if( !empty($id) ) {
			$obj = $this->modelDao->where(array('id'=>$id))->find();
			$cid = $obj['category_id'];
		} else {
			$cid = $_GET['cid'];
		}

		//显示分类处理
		$levels = $categoryDao->getUpLevels($cid);

		if( !empty($cid) ) {
			$levelsArray = explode('|', $levels);
			foreach ($levelsArray as $key=>$value) {
				if($key == 0) {
					continue;
				}
				$categoryList = $categoryDao->where(array('pid'=>$value,'lang'=>$lang, 'is_fixed'=>0,'is_publish'=>1,'tpl_one'=>array('neq','one')))->order('ordernum desc')->select();				
				if($key == 1) {
					$this->assign('oneCategoryList', $categoryList);
				} else if($key == 2) {
					$this->assign('twoCategoryList', $categoryList);
				} else if($key == 3) {
					$this->assign('threeCategoryList', $categoryList);
				}
			}
		}

		$this->assign('obj',$obj);
		$this->assign('category_is_comment',$category['is_comment']);
		$this->assign('levels', $levels);
		$this->assign('m_levels', $m_levels);
	}

	//普通修改,不做数据处理(不提供手机同步)
	protected function _update($data) {
		try {
			if ($this->modelDao->save ( $data )!==false) {
				$this->success ( '修改成功！' );
			} else {
				$this->error ( '修改失败！' );
			}
		} catch ( Exception $e ) {
			$this->error ( '异常：' . $e->getMessage () );
		}
	}

	//复杂修改操作,自动选择分类,HTML内容处理(提供手机同步)
	protected function _update2($data) {
		try {
			$data = $this->_processData($data);
			$before_cid = $this->modelDao->getField('category_id', array('id'=>$data['id']));
			$result = $this->modelDao->save($data);
			if (isset($data['synch_mobile'])) {
				$data = $this->modelDao->where(array('id'=>$data ['id']))->find();
				if( MODULE_NAME=='Category' ) {
					$data['pid'] = $this->_getModileCategoryId($_POST);
				} else {
					$data['category_id'] = $this->_getModileCategoryId($_POST);//dump($data);exit;
				}
				if( MODULE_NAME=='Advert' ) {
					$synch_msg = $this->_synchHardwareList($data);
				} else {
					$synch_msg = $this->_synchMobileList($data);
				}
				//产品多图片同步
				if( MODULE_NAME=='Goods' ) {
					$good_id = $this->modelDao->getLastInsID();
					$gpDao = M('GoodsPhoto');
					$gphotos = $gpDao->where(array('goods_id'=>$data['id']))->order('ordernum desc')->select();
					$gp_count = count($gphotos);
					if( $gp_count==0 ){
						continue;
					}
					for($i=0;$i<$gp_count;$i++){
						$gphoto = $gphotos[$i];
						unset($gphoto['id']);
						$gphoto['goods_id'] = $good_id;
						$gpDao->add($gphoto);
					}
				}
			}
			if( $result!==false ) {
				$this->success ( '修改成功！'.$synch_msg);
			} else {
				$this->success ( '修改失败！'.$synch_msg);
			}
		} catch ( Exception $e ) {
			$this->error ( '异常：' . $e->getMessage () );
		}
	}

	//普通添加,不做数据处理(不提供手机同步)
	protected function _add($data) {
		try {
			unset($data['id']);
			if ($this->modelDao->add ( $data )!==false) {
				$this->success ( '添加成功！' );
			} else {
				$this->error ( '添加失败！' );
			}
		} catch ( Exception $e ) {
			$this->error ( '异常：' . $e->getMessage () );
		}
	}

	//添加复杂操作，只要用在像发布新闻有分类,HTML内容处理
	protected function _add2($data) {
		try {
			$data = $this->_processData($data);
			if ($this->modelDao->add ( $data )) {
				
				if (isset($data['synch_mobile'])) {
					if( MODULE_NAME=='Category' ) {
						$data['pid'] = $this->_getModileCategoryId($_POST);
					} else {
						$data['category_id'] = $this->_getModileCategoryId($_POST);//dump($data);exit;
					}
					if( MODULE_NAME=='Advert' ) {
						$synch_msg = $this->_synchHardwareList($data);
					} else {
						$synch_msg = $this->_synchMobileList($data);
					}
				}
				$this->success ( '添加成功！'.$synch_msg );
			} else {
				$this->error ( '添加失败！' );
			}
		} catch ( Exception $e ) {
			$this->error ( '异常：' . $e->getMessage () );
		}
	}
	
	
	//选择最后一个分类ID保存
	protected function _getModileCategoryId($data) {
		if($data['one_mobile_category_id']=='mobile') {
			return $_SESSION['c_root'];
		}
		if($data['three_mobile_category_id']>0) {
			return $data['three_mobile_category_id'];
		} else if($data['two_mobile_category_id']>0) {
			return $data['two_mobile_category_id'];
		} else {
			return $data['one_mobile_category_id'];
		}
	}

	/**
	 * 添加/更新/删除/移动/复制有分类文章,都需要更新当前分类下列表数量
	 *
	 * @param int $current_cid 当前分类ID
	 * @param int $before_cid 修改之前分类ID, 只用在修改或移动时改变分类使用
	 * @param string $act 执行数据库动作
	 */
	protected function _updateCategoryListCount( $current_cid, $before_cid, $act='add' ) {
		$categoryDao = D('Admin.Category');
		$levels = $categoryDao->getUpLevels( $current_cid );
		$levels = explode('|', $levels);
		$levels[] = $current_cid;

		if( $act=='add' ) {//添加需要累加数量
			foreach ($levels as $key => $value) {
				$categoryDao->setInc("list_count","id=$value",1);
			}
		} else if( $act=='update' ) {//如更改分类需要减去和累加数量

			if( $before_cid!=$current_cid ) {//判断修改了分类
				//当前分类累加
				foreach ($levels as $key => $value) {
					$categoryDao->setInc("list_count","id=$value",1);
				}

				//之前分类减去
				$before_levels = $categoryDao->getUpLevels( $before_cid );
				$before_levels = explode('|', $before_levels);
				$before_levels[] = $before_cid;
				foreach ($before_levels as $key => $value) {
					$categoryDao->setDec("list_count","id=$value",1);
				}

			}
		} else if( $act=='delete' ) {//删除需要减去数量
			foreach ($levels as $key => $value) {
				$categoryDao->setDec("list_count","id=$value",1);
			}
		}

	}

	//加工处理分类,HTML内容处理等
	protected function _processData($data) {

		if($data['category_id']==-1) {
			$this->error('请选择分类！');
		}

		if( !empty($data['content']) ) {
			//HTML标签转实体
			if (get_magic_quotes_gpc ()) {
				$content = htmlspecialchars ( stripslashes ( $data ["content"] ) );
			} else {
				$content = htmlspecialchars ( $data ["content"] );
			}
			$data['content'] = $content;
		}

		//发布设置
		if (!isset($data['is_publish'])) {
			$data['is_publish'] = 0;
		}
		//语言设置
		if( empty($data['lang']) ) {
			$data['lang'] = $this->custom['def_lang'];
		}

		//时间设置
		if( !empty($data['update_time']) ) {
			$data['update_time'] = strtotime($data['update_time']);
		} else {
			$data['update_time'] = time();
		}
		if( !empty($data['create_time']) ) {
			$data['create_time'] = strtotime($data['create_time']);
		} else {
			$data['create_time'] = time();
		}
		if( !empty($data['begin_time']) ) {
			$data['begin_time'] = strtotime($data['begin_time']);
		} else {
			$data['begin_time'] = time();
		}
		if( !empty($data['end_time']) ) {
			$data['end_time'] = strtotime($data['end_time']);
		} else {
			$data['end_time'] = time();
		}

		return $data;
	}

	//分类管理
	protected function _category( $where ) {
		Load ( 'extend' );
		$categoryDao = M( 'Category' );
		$where['lang'] = array('in',getSqlInLangs());
		$where['hardware'] = $_SESSION['hardware'];
		$categoryList = $categoryDao->where($where)->order('lang desc,ordernum desc')->findAll ();
		$categoryList = list_to_tree ( $categoryList, 'id', 'pid', '_child', $_SESSION['c_root'] );
		$this->assign ( 'categoryList', $categoryList );
		$this->display ('Category/index');
	}

	//多选删除
	protected function _delete() {
		try {
			$ids = $_POST ["ids"];
			$count = count ( $ids );
			for($i = 0; $i < $count; $i ++) {
				$numAndId = explode ( ',', $ids [$i] );
				$cid = $this->modelDao->getField('category_id', array('id'=>$numAndId [1]));
				$this->modelDao->delete ( $numAndId [1] );
			}
			$this->success ( '删除成功！' );
		} catch ( Exception $e ) {
			$this->error ( '异常：' . $e->getMessage () );
		}
	}

	//单个删除
	protected function _deleteById() {
		try {
			$cid = $this->modelDao->getField('category_id', array('id'=>$_GET ['id']));
			$this->modelDao->delete ( $_GET ['id'] );
			$this->success ( '删除成功！' );
		} catch ( Exception $e ) {
			$this->error ( '异常：' . $e->getMessage () );
		}
	}

	//移动
	protected function _move($field='category_id') {
		try {
			$ids = $_POST ['ids'];
			$category_id = $this->_getLastSelectedCategoryId();
			$categoryDao = M('Category');
			$category = $categoryDao->where(array('id'=>$category_id))->find();
			if (! empty ( $ids )) {
				$count = count ( $ids );
				for($i = 0; $i < $count; $i ++) {
					$numAndId = explode ( ',', $ids [$i] );
					$this->modelDao->where(array('id'=>$numAndId [1]))->setField ( array($field,'hardware','lang'),array($category_id,$category['hardware'],$category['lang']) );
				}
				$this->success ( '移动成功！' );
			}
		} catch ( Exception $e ) {
			$this->error ( '异常：' . $e->getMessage () );
		}
	}

	//复制数据到指定分类
	protected function _copy() {
		try {
			$ids = $_POST ['ids'];
			$category_id = $this->_getLastSelectedCategoryId();
			$categoryDao = M('Category');
			$category = $categoryDao->where(array('id'=>$category_id))->find();
			if (! empty ( $ids )) {
				$count = count ( $ids );
				for($i = 0; $i < $count; $i ++) {
					$numAndId = explode ( ',', $ids [$i] );
					$obj = $this->modelDao->where ( 'id=' . $numAndId [1] )->find ();
					unset($obj ['id']);
					$obj ['hardware'] = $category['hardware'];
					$obj ['lang'] = $category['lang'];
					$obj ['category_id'] = $category_id;
					$result = $this->modelDao->add ( $obj );
					$result = true;
					if( $result!==false ) {
						//产品添加多图片
						if( MODULE_NAME=='Goods' ) {
							$good_id = $this->modelDao->getLastInsID();
							$gpDao = M('GoodsPhoto');
							$gphotos = $gpDao->where(array('goods_id'=>$numAndId [1]))->order('ordernum desc')->select();
							$gp_count = count($gphotos);
							if( $gp_count==0 ){continue;}
							for($j=0;$j<$gp_count;$j++){
								$gphoto = $gphotos[$j];
								unset($gphoto['id']);
								$gphoto['goods_id'] = $good_id;
								$gpDao->add($gphoto);
							}
						}
					}
				}
			}
			$this->success ( '复制成功！' );
		} catch ( Exception $e ) {
			$this->error ( '异常：' . $e->getMessage () );
		}
	}

	//选择最后一个分类ID保存
	protected function _getLastSelectedCategoryId() {

		if($_POST['three_category_id']>0) {
			$category_id = $_POST['three_category_id'];
		} else if($_POST['two_category_id']>0) {
			$category_id = $_POST['two_category_id'];
		} else if($_POST['one_category_id']>0) {
			$category_id = $_POST['one_category_id'];
		}
		return $category_id;
	}

	//多选更新排序
	protected function _ordernum() {
		try {
			$ordernums = $_POST ['ordernums'];
			$ids = $_POST ['ids'];
			if (! empty ( $ordernums ) && ! empty ( $ids )) {
				$count = count ( $ids );
				for($i = 0; $i < $count; $i ++) {
					$numAndId = explode ( ',', $ids [$i] );
					$bool = $this->modelDao->setField ( 'ordernum', $ordernums [$numAndId [0] - 1], 'id=' . $numAndId [1] );
				}
			}
			$this->success ( '更新成功！' );
		} catch ( Exception $e ) {
			$this->error ( '异常：' . $e->getMessage () );
		}
	}

	//更新单个字段
	protected function _updateField($filed){
		$fval = $_GET['fval'];
		if($fval=='true') {
			$fval = 1;
		} else {
			$fval = 0;
		}
		echo $this->modelDao->setField ( $filed, $fval, array('id'=>$_GET['id']) );
	}

	//可设置图片大小添加、更新上传通用
	protected function _imgUploads($folder) {
		$dir = $this->upload_root_path.'images/'.$folder.'/';
		$imgwidth = $_POST['imgwidth'];
		$imgheight = $_POST['imgheight'];
		if( !empty($_FILES['image']['name']) && !empty( $imgwidth ) && !empty( $imgheight ) ) {
			$this->_img_upload($dir,'image',true,$imgwidth,$imgheight);
		} elseif( !empty($_FILES['image']['name'] )) {
			$this->_img_upload($dir,'image');
		}
	}

	//删除图片
	protected function _deleteImage( $path, $field='image' ) {
		$where['id'] = $_GET['id'];
		$filename = $this->modelDao->where($where)->getField($field);
		unlink($path.$filename);
		unlink($path.'s_'.$filename);
		unlink($path.'m_'.$filename);
		$bool = $this->modelDao->where($where)->setField($field,'');
		if( $bool!==false ) {
			return true;
		} else {
			return false;
		}
	}

	//删除文件
	protected function _deleteFile( $path ) {
		$where['id'] = $_GET['id'];
		$filename = $this->modelDao->where($where)->getField('downfile');
		unlink($path.$filename);
		$bool = $this->modelDao->where($where)->setField('image','');
		if( $bool!==false ) {
			return true;
		} else {
			return false;
		}
	}

	//已选择模块
	protected function _assignModuleList() {
		$lang_in = getSqlInLangs();
		$categoryDao = M('Category');
		$moduleList = $categoryDao->where(array('pid'=>12,'is_publish'=>1))->order('ordernum desc')->select();//选择模块
		$moduleCount = count($moduleList);
		$categoryDao = M('Category','AdvModel');
		for ($i = 0; $i < $moduleCount; $i++) {
			$obj = $categoryDao->where( array('pid'=>$moduleList[$i]['id'],'hardware'=>$_SESSION['hardware'],'lang'=>array('in',$lang_in)) )->order('ordernum desc')->first();
			if($obj['tpl_one'] == 'auto' || selectCategoryByPid($obj['id'])) {
				$obj = $categoryDao->where( array('pid'=>$obj['id']) )->order('ordernum desc')->first();
				if($obj['tpl_one'] == 'auto' ) {
					$obj = $categoryDao->where( array('pid'=>$obj['id']) )->order('ordernum desc')->first();
				}
			}
			$moduleList[$i]['first_cid'] = $obj['id'];
			$moduleList[$i]['lang'] = $obj['lang'];
		}
		$this->assign('moduleList', $moduleList);
	}
	
	protected function _getDefLangTitle2() {
		$categoryDao = D('Admin.Category');
		$title_langs = json_decode($categoryDao->getTitle($_SESSION['c_root']),true);
		if( is_array($title_langs) ) {
			return $title_langs[$_SESSION['lang']]['title'];
		}
		return '';
	}
	
	//单页手机同步
	protected function _synchMobileOne( $data ) {
		try {
			$mobileObj = $this->modelDao->where( array('category_id'=>$data['category_id']) )->find();
			
			if( empty($mobileObj) ) {
				unset($data['id']);
				$data["hardware"] = 'mobile';
				$data['create_time'] = time();
				$result = $this->modelDao->add ( $data );
			} else {
				$data = $this->modelDao->where( array('id'=>$data['id']) )->find();
				$data["id"] = $mobileObj['id'];
				$data["hardware"] = 'mobile';
				$data["lang"] = $mobileObj['lang'];
				$data["category_id"] = $mobileObj['category_id'];
				$result = $this->modelDao->save ( $data );
			}
			if( $result!==false ) {
				return '同步手机成功！';
			} else {
				return '同步手机失败！';
			}
		} catch ( Exception $e ) {
			$this->error ( '异常：' . $e->getMessage () );
		}
	}

	//列表同步
	protected function _synchMobileList( $data ) {
		try {
			if( !empty($data) ) {
				unset($data ['id']);
				$data ["hardware"] = 'mobile';
				$result = $this->modelDao->add ( $data );
				if( $result!==false ) {
					return '同步手机成功！';
				} else {
					return '同步手机失败！';
				}
			}
		} catch ( Exception $e ) {
			$this->error ( '异常：' . $e->getMessage () );
		}
	}
	
	//当前终端列表同步
	protected function _synchHardwareList( $data ) {
		try {
			if( !empty($data) ) {
				$categoryDao = M('Category');
				$category = $categoryDao->where( array('id'=>$data['category_id']) )->find();
				unset($data ['id']);
				$data ["hardware"] = $_SESSION['hardware'];
				$data["lang"] = $category['lang'];
				$result = $this->modelDao->add ( $data );
				if( $result!==false ) {
					return '同步语言分类成功！';
				} else {
					return '同步语言分类失败！';
				}
			}
		} catch ( Exception $e ) {
			$this->error ( '异常：' . $e->getMessage () );
		}
	}
	
	//设置操作提示后跳转的URL
	protected function setJumpUrl( $url ) {
		$this->assign('jumpUrl', __APP__.'/Admin/'.$url);
	}


	//没权限访问提示
	protected function _noAccess() {
		if( $this->isAjax() ) {
			exit('no-access');
		} else {
			$this->redirect('Public/no-access');
		}
	}
//可设置图片大小添加、更新上传通用
	protected function _imgUploads2($folder) {
		$dir = $this->upload_root_path.'images/'.$folder.'/';
		$imgwidth = $_POST['imgwidth'];
		$imgheight = $_POST['imgheight'];
		if( !empty($_FILES['image']['name']) && !empty( $imgwidth ) && !empty( $imgheight ) ) {
			return $this->_img_upload($dir,'image',true,$imgwidth,$imgheight);
		} elseif( !empty($_FILES['image']['name'] )) {
			return $this->_img_upload($dir,'image');
		}
	}
}
?>