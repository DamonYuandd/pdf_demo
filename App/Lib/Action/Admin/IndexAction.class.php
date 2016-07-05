<?php

/**
 * 
 * 后台主框架控制器
 * @author uclnn
 *
 */
class IndexAction extends AdminAction {

	
	public function index() {
		
		$this->_assignModuleList();
		
		$categoryDao = M('Category');
		$langList = $categoryDao->where(array('alias'=>array('in',$this->custom['langs'])))->select();//选择语言
		$this->assign('langList', $langList);
		$this->display ( 'Layout:admin' );
	}

	//系统首页
	public function main() {
		$newsDao = M('News');
		$goodsDao = M('Goods');
		$guestbookDao = M('Guestbook');
		$advertDao = M('Advert');
		$linkDao = M('Link');
		$downloadDao = M('Download');
		$jobDao = M('Job');
		$jobrDao = M('JobResume');
		$memberDao = M('Member');
// 		$customDao = M('Custom');
		
		$this->assign('newsCount', $newsDao->count());
		$this->assign('goodsCount', $goodsDao->count());
		$this->assign('guestbookCount', $guestbookDao->count());
		$this->assign('guestbookCountRead0', $guestbookDao->where(array('read'=>0))->count());
		$this->assign('advertCount', $advertDao->count());
		$this->assign('linkCount', $linkDao->count());
		$this->assign('downloadCount', $downloadDao->count());
		$this->assign('jobCount', $jobDao->count());
		$this->assign('jobrCount', $jobrDao->count());
		$this->assign('memberCount', $memberDao->count());
		$this->assign('todayMemberCount', $memberDao->where("(FROM_UNIXTIME(create_time,'%Y-%m-%d')='" . date ( 'Y-m-d', time () ) . "')")->count());
		
		$this->_assignModuleList();
		
		$this->display ();
	}
	
	public function mainMenu() {
		exit;
	}

	public function category() {
		$this->assign('c_root', $_GET['c_root']);
		$this->display ();
	}

	//左边菜单
	public function sidemenu() {
		$pid = $_GET['pid'];
		if( !empty( $pid ) ) {
			$categoryDao = M('Category');
			$langin = $this->getSqlInLangs();
			$where['_string'] = "(is_publish=1 AND title<>'' AND hardware='".$_SESSION['hardware']."' AND lang in(".$langin.")) OR is_fixed=1";
			$dataList = $categoryDao->where( $where )->order('lang desc,ordernum desc')->select();
			Load('extend');
			$dataList = list_to_tree($dataList, 'id', 'pid','_child',$pid);
			$this->assign('module', $categoryDao->getField('module',array('id'=>$pid)));
			$this->assign('alias', $categoryDao->getField('alias',array('id'=>$pid)));
			$this->assign('dataList', $dataList);
		} else {
			$this->assign('module','System');
		}
		$this->display ();
	}
	
	//获取网站多语言SQL查询IN()参数
	protected function getSqlInLangs() {
		$categoryDao = M('Category');
		if( $_SESSION['hardware']=='pc' ) {
			$lang_pid = 3;
		} elseif( $_SESSION['hardware']=='mobile' ) {
			$lang_pid = 4;
		}
		$alias = $categoryDao->where(array('pid'=>$lang_pid,'is_publish'=>1))->field('alias')->select();
		$alias_count = count($alias);
		$langin = '';
		for ($i = 0; $i < $alias_count; $i++) {
			$langin .= "'".$alias[$i]['alias']."'";
			if( $alias_count-1 > $i ) {
				$langin .= ',';
			}
		}
		return $langin;
	}
	
	//改变当前语 言
	public function checkedLang() {
		$lang = $_GET['lang'];
		$_SESSION[C('USER_AUTH_KEY')]['lang'] = $lang;
	}
	
	//获取单个分类信息
	public function getCategory() {
		$id = $_REQUEST['id'];
		if( !empty($id) ) {
			$categoryDao = M('Category');
			$category = $categoryDao->find($id);
			if ( $this->isAjax() ) {
				exit(json_encode($category));
			} else {
				$this->assign('obj',$category);
			}
		}
	}
	
	//分类查找
	function selectCategoryByPid() {
		if( !empty($_GET['lang']) ) {
			$where['lang'] = $_GET['lang'];
		} else {
			$where['lang'] = array('in',getSqlInLangs());
		}
		if( !empty($_GET['hwe']) ) {
			$where['hardware'] = $_GET['hwe'];
		}
		if( !empty($_GET['pid']) ) {
			$where['pid'] = $_GET['pid'];
			$where['is_publish'] = 1;
			$categoryDao = M ( "Category" );
			$categoryList = $categoryDao->where ( $where )->order('hardware desc,lang desc,ordernum desc')->select ();
			$count = count($categoryList);
			for ($i = 0; $i < $count; $i++) {
				$hardware = $categoryList[$i]['hardware'];
				if($hardware=='pc') {
					$hardware_text = '电脑>';
				} elseif($hardware=='mobile') {
					$hardware_text = '手机>';
				}
				$title = $categoryDao->where(array('hardware'=>$hardware,'alias'=>$categoryList[$i]['lang']))->getField('title');
				$categoryList[$i]['title'] = '['.$hardware_text.substr($title, 0, 3).'] '.$categoryList[$i]['title'];
			}
			if( $this->isAjax() ) {
				$json ['list'] = $categoryList;
				exit(json_encode ( $json ));
			} else {
				$this->assign('categoryList', $categoryList);
			}
		}
	}

	//手机分类输出
	function selectMobileCategoryByPid() {
		if( !empty($_GET['lang']) ) {
			$where['lang'] = $_GET['lang'];
		} else {
			$where['lang'] = array('in',getSqlInLangs());
		}
		if( !empty($_GET['hwe']) ) {
			$where['hardware'] = $_GET['hwe'];
		}
		if( !empty($_GET['pid']) ) {
			$where['pid'] = $_GET['pid'];
			$where['is_publish'] = 1;
			$categoryDao = M ( "Category" );
			$categoryList = $categoryDao->where ( $where )->order('hardware desc,lang desc,ordernum desc')->select ();
			$count = count($categoryList);
			for ($i = 0; $i < $count; $i++) {
				$hardware = $categoryList[$i]['hardware'];
				$title = $categoryDao->where(array('hardware'=>$hardware,'alias'=>$categoryList[$i]['lang']))->getField('title');
				$categoryList[$i]['title'] = '['.substr($title, 0, 3).'] '.$categoryList[$i]['title'];
			}
			if( $this->isAjax() ) {
				$json ['list'] = $categoryList;
				exit(json_encode ( $json ));
			} else {
				$this->assign('categoryList', $categoryList);
			}
		}
	}
	
	//导出excel
	public function excelPort(){
		if (empty($_GET['do'])){
			$this->error('错误');
		}
		if ($_GET['do'] == 'phone'){
			$db = M('vote');
		}else if ($_GET['do'] == 'option'){
			$db = M('vote_option');
		}else{
			$this->error('错误');
		}
		
		
		$result = $db->select();
 
		import("ORG.Util.PHPExcel");
		
		$objPHPExcel = new PHPExcel();
	
		/*以下是一些设置 ，什么作者  标题啊之类的*/
		$objPHPExcel->getProperties()->setCreator("转弯的阳光")
		->setLastModifiedBy("转弯的阳光")
		->setTitle("数据EXCEL导出")
		->setSubject("数据EXCEL导出")
		->setDescription("备份数据")
		->setKeywords("excel")
		->setCategory("result file");
	
 
		if($_GET['do'] == 'phone'){	//获取投票者手机号码 
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '联系电话')
			->setCellValue('B1', '记录时间')
			;
			foreach($result as $key => $v){
				$num=$key+2;
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$num, $v['phone'])
				->setCellValue('B'.$num, date('Y-m-d',$v['addTime']))
				;
			}
		}

		if($_GET['do'] == 'option'){//获取参选作品信息
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '编号')
			->setCellValue('B1', '组别')
			->setCellValue('C1', '类型')
			->setCellValue('D1', '作品名称')
			->setCellValue('E1', '作者名称')
			->setCellValue('F1', '作者年龄')
			->setCellValue('G1', '地区')
			->setCellValue('H1', '指导老师')
			->setCellValue('I1', '参赛单位名称')
			->setCellValue('J1', '作者监护人')
			->setCellValue('K1', '与作者关系')
			->setCellValue('L1', '电话')
			->setCellValue('M1', '地址')
			->setCellValue('N1', '邮箱')
			->setCellValue('O1', '作者头像')
			->setCellValue('P1', '作品1')
			->setCellValue('Q1', '作品1尺寸')
			->setCellValue('R1', '作品2')
			->setCellValue('S1', '作品2尺寸')
			->setCellValue('T1', '投票数量')
			->setCellValue('U1', '是否入围')
			->setCellValue('V1', '是否获奖')
			->setCellValue('W1', '添加时间')
			;
			foreach($result as $key => $v){
				$num=$key+2;
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$num, $v['id'])
				->setCellValue('B'.$num, echoGroup($v['group'],'group'))
				->setCellValue('C'.$num, echoGroup($v['type'],'type'))
				->setCellValue('D'.$num, $v['title'])
				->setCellValue('E'.$num, $v['name'])
				->setCellValue('F'.$num, $v['age'])
				->setCellValue('G'.$num, echoGroup($v['city'],'city'))
				->setCellValue('H'.$num, $v['teacher'])
				->setCellValue('I'.$num, $v['entry_mame'])
				->setCellValue('J'.$num, $v['guardian'])
				->setCellValue('K'.$num, $v['relation'])
				->setCellValue('L'.$num, $v['phone'])
				->setCellValue('M'.$num, $v['address'])
				->setCellValue('N'.$num, $v['email'])
				->setCellValue('O'.$num, $v['author_avatar'])
				->setCellValue('P'.$num, $v['works_1'])
				->setCellValue('Q'.$num, $v['works_1_w'].'x'.$v['works_1_h'])
				->setCellValue('R'.$num, $v['works_2'])
				->setCellValue('S'.$num, $v['works_2_w'].'x'.$v['works_2_h'])
				->setCellValue('T'.$num, $v['vote_num'])
				->setCellValue('U'.$num, $v['isFinalist'])
				->setCellValue('V'.$num, $v['isAwards'])
				->setCellValue('W'.$num, date('Y-m-d',$v['addTime']))
				;
			}
		}
			
		
			
			
			
			
		$objPHPExcel->getActiveSheet()->setTitle('User');
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.time().$_GET['do'].'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
	
}
?>