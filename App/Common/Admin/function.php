<?php
//多语言表单元素radio,checkbox（系统定制使用,需要迁移）
function langInput( $type ) {
	$customDao = D('Custom');
	$categoryDao = D('Category');
	$def_lang = $customDao->getField('def_lang',array('id'=>1));
	$langList = $categoryDao->where(array('pid'=>11))->order('ordernum desc')->select();
	if( !empty($langList) ) {
		foreach ($langList as $key => $value) {
			if($value['alias'] == $def_lang ) {
				$style = 'style="color:red;"';
			} else {
				$style = '';
			}
			$input .= '<input type="'.$type.'" id="langs" name="langs" value="'.$value['alias'].'"> <span '.$style.'>'.$value['title'].'</span>&nbsp;&nbsp';
		}
	}
	echo $input;
}

function getAccountDiy() {
	return (array)json_decode(C('ACCOUNT_DIY'));
}

//是否使用多语言
function isLang() {
	$categoryDao = M('Category');
	if( $_SESSION['hardware']=='pc' ) {
		$pid = 3;
	} elseif( $_SESSION['hardware']=='mobile' ) {
		$pid = 4;
	}
	$langList = $categoryDao->where(array('pid'=>$pid,'is_publish'=>1))->order('is_default desc,ordernum desc')->select();
	if( count($langList)>1 ) {
		foreach ($langList as $key => $value) {
			if($value['alias'] == $_SESSION['lang'] ) {
				$style = 'style="color:red;"';
				$checked = 'checked';
			} else {
				$style = '';
				$checked = '';
			}
			$input .= '<input type="radio" id="lang" name="lang" value="'.$value['alias'].'" '.$checked.'> <span '.$style.'>'.$value['title'].'</span>&nbsp;&nbsp;';
		}
		$li = '<label>选择语言</label>'.$input;
	} else {
		$li .= '<input type="hidden" name="lang" value="'.$_SESSION['lang'].'">';
	}
	echo $li;
}

//能否评论
function isComment($is_comment) {
	if( $is_comment==0 ) {
		$style = 'style="display:none;"';
	}
	$li = '<li id="li_is_comment" '.$style.'><label>能否评论?</label><input type="radio" id="is_comment" name="is_comment" value="1"> 是&nbsp;&nbsp;<input type="radio" id="is_comment" name="is_comment" value="0" checked="checked"> 否</li>';
	echo $li;
}

//是否多语言
function isMultilingual($custom) {
	$langs = explode(',', str_replace('mobile,', '', $custom['langs']));
	if( count($langs) >= 2 ) {
		return true;
	} else {
		return false;
	}
}

//返回分类标题
function getCategoryTitle($id) {
	$categoryDao = M('Category');
	echo $categoryDao->getField('title', array('id'=>$id));
}

//显示状态
function getShowState($is_publish,$is_home,$is_top,$is_comment){
	if($is_publish==1) echo '发布 ';
	if($is_home==1) echo '首页 ';
	if($is_top==1) echo '置顶 ';
	if($is_comment==1) echo '评论 ';
}

//获取广告类型文字说明
function getAdvertTypeText( $type ) {
	if( $type==1 ) {
		echo '文字';
	} elseif ( $type==2 ) {
		echo '图片';
	} elseif ( $type==3 ) {
		echo 'flash';
	} elseif ( $type==4 ) {
		echo '代码';
	} elseif ( $type==5 ) {
		echo '对联';
	}
}

//输出语言标题
function getLangText( $hardware, $alias ) {
	if( empty($hardware) ) {
		$hardware = $_SESSION['hardware'];
	}
	$categoryDao = M('Category');
	return $categoryDao->where(array('hardware'=>$hardware,'alias'=>$alias))->getField('title');
}

//输出语言标题
function getLangTextTitle( $lang ) {
	$hardware = $_SESSION['hardware'];
	$title = getLangText($hardware,$lang);
	echo '<span style="color:#BB4141">['.substr($title, 0, 3).']</span>';
}

function getLangTextSidemenu($hardware,$alias) {
	$title = getLangText($hardware,$alias);
	echo '<span style="color:#BB4141">['.substr($title, 0, 3).']</span>';
}

//checkbox状态
function getCheckboxState($vo_id,$name,$state){
	if($state==1) {
		echo '<input type="checkbox" checked="checked" value="'.$vo_id.'" name="'.$name.'" id="'.$name.'" />';
	} else {
		echo '<input type="checkbox" value="'.$vo_id.'" name="'.$name.'" id="'.$name.'" />';
	}
}

//radio 状态
function getRadioState($vo_id,$name,$state)
	{
		if($state == 1)
		{
			echo '<input type="radio" value="'.$vo_id.'" name="'.$name.'" id="'.$name.'"  checked="checked"/>';
		}else
		{
			echo '<input type="radio" value="'.$vo_id.'" name="'.$name.'" id="'.$name.'" />';
		}
	}
//是否有分类
function isCategory( $oneC, $twoC, $threeC, $lable='分类', $lang='') {
	//一级分类
	if( empty($oneC) ) {
		$oneHide = 'display:none;';
	}
	if( $lang=='mobile' ) {
		$c_lang = 'mobile_';
	}
	$li = '<li id="li_'.$c_lang.'category" style="'.$oneHide.'"><label>'.$lable.'</label>';
	$li .= '<select id="one_'.$c_lang.'category_id" name="one_'.$c_lang.'category_id" style="width:200px;" onchange="changeCategory(this,\'two_'.$c_lang.'category_id\',\''.$lang.'\')">';
	$li .= '<option value="-1" selected="">请选择</option>';
	foreach ($oneC as $key => $value) {
		$li .= '<option value="'.$value['id'].'">'.$value['title'].'</option>';
	}
	$li .= '</select> ';

	//二级分类
	if( empty($twoC) ) {
		$twoHide = 'display:none;';
	}
	$li .= '<select id="two_'.$c_lang.'category_id" name="two_'.$c_lang.'category_id" style="width:200px;'.$twoHide.'" onchange="changeCategory(this,\'three_'.$c_lang.'category_id\',\''.$lang.'\')">';
	$li .= '<option value="-1" selected="">请选择</option>';
	foreach ($twoC as $key => $value) {
		$li .= '<option value="'.$value['id'].'">'.$value['title'].'</option>';
	}
	$li .= '</select> ';

	//三级分类
	if( empty($threeC) ) {
		$threeHide = 'display:none;';
	}
	$li .= '<select id="three_'.$c_lang.'category_id" name="three_'.$c_lang.'category_id" style="width:200px;'.$threeHide.'" onchange="changeCategory(this,\'\',\''.$lang.'\')">';
	$li .= '<option value="-1" selected="">请选择</option>';
	foreach ($threeC as $key => $value) {
		$li .= '<option value="'.$value['id'].'">'.$value['title'].'</option>';
	}
	$li .= '</select></li>';

	echo $li;
}

//分类组装下拉选择
function selectCateoryOptions($pid,$lang,$hardware='mobile') {
	$categoryDao = D('Category');
	$where['pid'] = $pid;
	if( $hardware!='all' ) {
		$where['hardware'] = $hardware;
	}
	if( $lang!='all' ) {
		$where['lang'] = $lang;
	} else {
		$where['lang'] = array('in', getSqlInLangs());
	}
	$cateoryList = $categoryDao->where ( $where )->order('lang desc,ordernum desc')->select ();
	foreach ( $cateoryList as $val ) {
		$hardware = $val['hardware'];
		$lang = $val['lang'];
		$title = $categoryDao->where(array('hardware'=>$hardware,'alias'=>$lang))->getField('title');
		$options .= '<option value="' . $val ['id'] . '">'.'['.substr($title, 0, 3).'] '.$val ['title'] . '</option>';
	}
	echo $options;
}

//分类组装下拉选择有终端显示
function selectCateoryOptions2($pid,$lang,$hardware='mobile') {
	$categoryDao = D('Category');
	$where['pid'] = $pid;
	if( $hardware!='all' ) {
		$where['hardware'] = $hardware;
	}
	if( $lang!='all' ) {
		$where['lang'] = $lang;
	} else {
		$where['lang'] = array('in', getSqlInLangs());
	}
	$cateoryList = $categoryDao->where ( $where )->order('hardware desc,lang desc,ordernum desc')->select ();
	foreach ( $cateoryList as $val ) {
		$hardware = $val['hardware'];
		if($hardware=='pc') {
			$hardware_text = '电脑>';
		} elseif($hardware=='mobile') {
			$hardware_text = '手机>';
		}
		$lang = $val['lang'];
		$title = $categoryDao->where(array('hardware'=>$hardware,'alias'=>$lang))->getField('title');
		$options .= '<option value="' . $val ['id'] . '">'.'['.$hardware_text.substr($title, 0, 3).'] '.$val ['title'] . '</option>';
	}
	echo $options;
}

//获取网站多语言SQL查询IN()参数
function getSqlInLangs() {
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
		$langin .= $alias[$i]['alias'];
		if( $alias_count-1 > $i ) {
			$langin .= ',';
		}
	}
	return $langin;
}

//通过别名获取分类ID
function getCategoryIdByAlias($alias) {
	$cateoryDao = M ( 'Category' );
	return $cateoryDao->getField('id', array('alias'=>$alias));
}

//获取当前招聘简历个数
function getJobResumeCount($job_id) {
	$jrDao = M('JobResume');
	echo $jrDao->where(array('job_id'=>$job_id))->count();
}

//招聘简历列表
function findJobResume($job_id,$file_path) {
	$jrDao = M('JobResume');
	$jrList = $jrDao->where(array('job_id'=>$job_id))->select();
	foreach ($jrList as $key => $value) {
		$tr .= '<tr>';
		$tr .= "<td>".$value['linkname']."</td>";
		$tr .= "<td>".$value['sex']."</td>";
		$tr .= "<td>".$value['age']."</td>";
		$tr .= "<td>".$value['phone']."</td>";
		$tr .= "<td>".$value['address']."</td>";
		$tr .= "<td>".$value['email']."</td>";
		$tr .= "<td>".$value['intro']."</td>";
		if( !empty($value['file']) ) {
			$tr .= '<td><a href="'.$file_path.'files/resume/'.$value['file'].'">查看简历文件</a></td>';
		} else {
			$tr .= "<td>没有上传</td>";
		}
		$tr .= "<td>".date('Y-m-d',$value['create_time'])."</td>";
		$tr .= '<td><a href="#" onclick="javascript:deleteData(\''.__APP__.'/Admin/Job/deleteResume/id/'.$value['id'].'\');">删除</a></td>';
		$tr .= '</tr>';
	}
	echo $tr;
}

//找出父级的子分类
function selectCategoryByPid( $pid ) {
	$categoryDao = M ( "Category" );
	return $categoryDao->where ( array('pid'=>$pid) )->order('ordernum desc')->select ();
}

function get_GoodsPhotos($goods_id){
	$jrDao = M('GoodsPhoto');
	return $jrDao->where(array('goods_id'=>$goods_id))->select();
}

function get_SurveyQues($survey_id){
	$jrDao = M('SurveyQuestion');

	return $jrDao->where(array("sort_id"=>$survey_id))->order('id asc')->select();
}

function get_surveyAnswer($qid){
	$jrDao = M('SurveyAnswer');
	return $jrDao->where(array('ques_id'=>$qid))->order('ordernum asc')->select();
}

function get_SureyResult($id,$antype){
	$jrDao = M('SurveyResult');
	if($antype!=3){
		return $jrDao->where(array('answer_id'=>$id))->order('id DESC')->count();
	}else{
		return $jrDao->where(array('ques_id'=>$id))->order('id DESC')->select();
	}
}

function get_QuesListEdit($sortid){

	$queslist = get_SurveyQues($sortid);
	if($queslist){
		$questr = '';
		foreach($queslist as $key=>$ques){
			$ques_id .= $ques['id'].',';
			$questr .= "<span id='questit".($key+1)."'>";
			$questr .= "<label></label><span>".($key+1).". <input id='ques_title".($key+1)."' name='ques_title[]' value='".$ques['ques_title']."' class='type-text'/></span><img src='__ADMIN__/Public/imgs/cross.png' title='删除问题' onclick='del_ques(".($key+1).",".$ques['id'].")'/><br>";
			$questr .= "<input type='hidden' id='ques_id".($key+1)."' name='ques_id[]' value='".$ques['id']."'/>";
			$radiochk1 = ($ques['answer_type'] ==1)?'checked':'';
			$radiochk2 = ($ques['answer_type'] ==2)?'checked':'';
			$radiochk3 = ($ques['answer_type'] ==3)?'checked':'';
			if($ques['answer_type']!=3){
				$questr .= "<label></label><input type='radio' id='answer_type1".($key+1)."' name='answer_type".($key+1)."' value='1' ".$radiochk1." >答案单选";
				$questr .= "<label></label><input type='radio' id='answer_type2".($key+1)."' name='answer_type".($key+1)."' value='2' ".$radiochk2." >答案多选";
				$questr .= "<label></label><input type='radio' id='answer_type3".($key+1)."' name='answer_type".($key+1)."' value='3' ".$radiochk3." >答案输入<br>";
				$answerlist = get_surveyAnswer($ques['id']);
				if($answerlist){
					foreach($answerlist as $k=>$val){
						$questr .= "<label></label><input type='hidden' id='orderid".($k+1).($key+1)."' name='orderid".($key+1)."[]'  value='".$val['ordernum']."'>";
						$questr .= "<input id='answer_title".($k+1).($key+1)."' name='answer_title".($key+1)."[]' value='".$val['answer_title']."' size='40'><br>"	;
						$questr .= "<input type='hidden' id='answer_id".($k+1).($key+1)."' name='answer_id".($key+1)."[]' value='".$val['id']."' size='40'><br>"	;
					}
				}
			}else{
				$questr .= "<label></label><input type='radio' id='answer_type1".($key+1)."' name='answer_type".($key+1)."' value='1' ".$radiochk1." onclick='add_answer(1,".($key+1).")'>答案单选";
				$questr .= "<label></label><input type='radio' id='answer_type2".($key+1)."' name='answer_type".($key+1)."' value='2' ".$radiochk2." onclick='add_answer(2,".($key+1).")'>答案多选";
				$questr .= "<label></label><input type='radio' id='answer_type3".($key+1)."' name='answer_type".($key+1)."' value='3' ".$radiochk3." onclick='add_answer(3,".($key+1).")'>答案输入<br>";
				$questr .= "<label></label><span id='ques".($key+1)."' style='display:none'></span><br>";
			}
			$questr .= "</span>";
		}
	}
	echo $questr;
}

function get_QuesResultList($sortid){

	$queslist = get_SurveyQues($sortid);
	if($queslist){
		$questr = '';
		foreach($queslist as $key=>$ques){
			$ques_id .= $ques['id'].',';
			$questr .= "<span >";
			$questr .= "<label></label><span>".($key+1).". ".$ques['ques_title']."</span><br>";

			if($ques['answer_type']!=3){
				$answerlist = get_surveyAnswer($ques['id']);
				$answer = array('A','B','C','D');
				foreach($answerlist as $k=>$val){
					$count = get_SureyResult($val['id'],$val['answer_type']);
					$questr .= "<label></label>&nbsp;&nbsp;&nbsp;&nbsp;".$answer[$k].'. '.$val['answer_title']."&nbsp;&nbsp;&nbsp;&nbsp; 投票次数：".$count."<br>"	;
				}
			}else{
				$textAnswer = get_SureyResult($ques['id'],$ques['answer_type']);
				if($textAnswer){
					foreach($textAnswer as $key=>$value){
						$questr .= "<label></label>&nbsp;&nbsp;&nbsp;&nbsp;(".($key+1)."). ".$value['answer_text']."<br>"	;
					}
				}
			}
			$questr .= "</span><br><br>";
		}
	}
	echo $questr;

}

//显示页面当前位置导航
function getNavSite( $nav_site, $cid ) {
	if( $_SESSION['hardware']=='pc' ) {
		$htext = '<img src="'.__ADMIN__.'/Public/imgs/path_pc.png" align="absmiddle" style="margin-top: -2px;" /> 电脑版: ';
	} else {
		$htext = '<img src="'.__ADMIN__.'/Public/imgs/path_mob.png" align="absmiddle" style="margin-top: -3px;" /> 手机版: ';
	}
	$hardware_text = '<span style="color:#BB4141;font-weight: bold;font-size: 13px;">'.$htext.'</span>';
	if( empty($nav_site) ) {
		$categoryDao = M('Category');
		$category = $categoryDao->where(array('id'=>$cid))->find();
		$levels = explode('|', $category['levels']);
		foreach ($levels as $key => $value) {
			if( empty($value) ) continue;
			$category2 = $categoryDao->where(array('id'=>$value))->field('title,is_fixed')->find();
			if( $category2['is_fixed']==0 ) {
				$title = $categoryDao->getField('title', array('id'=>$value));
			} else {
				$title = $category2['title'];
			}
			$title_langs = json_decode($title, true);
			if( is_array($title_langs) ) {
				$title = $title_langs[$_SESSION['lang']]['title'];
			}
			if( empty($title) ) continue;
			$nav_site .= $title.' > ';
		}
		echo $hardware_text.$nav_site.$category['title'];
	} else {
		echo $hardware_text.$nav_site;
	}
}

function getCurCategoryNav( $cid ) {
	if( !empty($cid) ) {
		$categoryDao = M('Category');
		$category = $categoryDao->where(array('id'=>$cid))->find();
		$levels = explode('|', $category['levels']);
		$count = count($levels);
		$nav_site='';
		for ($i = 2; $i < $count; $i++) {
			$value = $levels[$i];
			if( empty($value) ) continue;
			$category2 = $categoryDao->where(array('id'=>$value))->field('title,is_fixed')->find();
			$title = $category2['title'];
			if( empty($title) ) continue;
			$nav_site .= $title.' > ';
		}
		echo '<span style="color:#008200;">'.$nav_site.$category['title'].'</span>';
	}
}

//获取左边菜单URL
function getSideMenuUrl($p_alias,$id,$url) {
	if( empty($url) ) {
		echo __APP__.'/Admin/'.$p_alias.'/index/cid/'.$id;
	} else {
		echo __APP__.'/Admin/'.$url.'/cid/'.$id;
	}
}

//如果URL参数带lang优先返回
function getAddButtonLang($get_Lang, $lang) {
	if( !empty($get_Lang) ) {
		echo $get_Lang;
	} else {
		echo $lang;
	}
}

//获取手机同步按钮
function getSynchMobileButton($custom,$vo) {
	if($custom['def_lang']==$vo['lang']) {
		echo '<input type="button" value="同步" onclick="synchMobile('.$vo['id'].');">';
	}
}

//是否有手机网站
function isShowMobile($custom, $lang ) {
	if(empty($lang) && substr_count($custom['langs'],'mobile')==1) {
		return true;
	} else if(substr_count($custom['langs'],'mobile')==1) {
		if( $custom['def_lang']==$lang ) {
			return true;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

//当前产品是否有多张图
function isGoodsImages($id) {
	$gpDao = M('GoodsPhoto');
	$count = $gpDao->where(array('goods_id'=>$id))->count();
	if( $count> 1 ) {
		return true;
	} else {
		return false;
	}
}

function echoLangsOption() {
	$categoryDao = M('Category');
	if( $_SESSION['hardware']=='pc' ) {
		$pid = 3;
	} elseif( $_SESSION['hardware']=='mobile' ) {
		$pid = 4;
	}
	$langList = $categoryDao->where(array('pid'=>$pid,'is_publish'=>1))->field('alias,title')->order('is_default desc,ordernum desc')->select();
	foreach ($langList as $key => $value) {
		$option .= '<option value="'.$value['alias'].'" '.$selected.'>'.$value['title'].'</option>';
	}
	echo $option;
}

//输出复选框语言
function echoLangsCheckbox( $rmoveLang ) {
	if( empty($rmoveLang) ) {
		$rmoveLang = $_SESSION['lang'];
	}
	$categoryDao = M('Category');
	if( $_SESSION['hardware']=='pc' ) {
		$pid = 3;
	} elseif( $_SESSION['hardware']=='mobile' ) {
		$pid = 4;
	}
	$langList = $categoryDao->where(array('pid'=>$pid,'is_publish'=>1))->field('alias,title')->order('is_default desc,ordernum desc')->select();
	if( count($langList)>1 ) {
		foreach ($langList as $key => $value) {
			if($value['alias'] == $rmoveLang ) {
				continue;
			} else {
				$input .= '<input type="checkbox" name="synch_lang[]" value="'.$value['alias'].'"> <span>'.$value['title'].'</span>&nbsp;&nbsp;';
			}
		}
		echo '<label>同步语言</label>'.$input;
	}
}

function getPcMobileTab($pc_url, $mobile_url) {
	if(MODULE_NAME!='Mobile'){
		$pc = 'activity';
	}
	if(MODULE_NAME=='Mobile'){
		$mobile = 'activity';
	}
	$str = '<div style="margin:0;margin-top:60px;"><ul class="ui-tabs">
	<li class="'.$pc.'"><a href="__APP__/Admin/'.$pc_url.'">电脑版</a></li>
	<li class="'.$mobile.'"><a href="__APP__/Admin/'.$mobile_url.'">移动版</a></li></ul></div>';
	echo $str;
}

function getDomain($custom) {
	$urls = explode(',', $custom['url']);
	if(empty($urls[1])){
		echo $urls[0];
	}else{
		echo $urls[1];
	}
}

//默认语言导航标题
function getDefNavTitle($title_langs,$index=0) {
	if( is_array($title_langs[$index]) ) {
		echo $title_langs[$index]['title'];
	}
}


// 单位自动转换函数
function getRealSize($size) {
	$kb = 1024; // Kilobyte
	$mb = 1024 * $kb; // Megabyte
	$gb = 1024 * $mb; // Gigabyte
	$tb = 1024 * $gb; // Terabyte

	if ($size < $kb) {
		return $size . " B";
	} else if ($size < $mb) {
		return round ( $size / $kb, 2 ) . " KB";
	} else if ($size < $gb) {
		return round ( $size / $mb, 2 ) . " MB";
	} else if ($size < $tb) {
		return round ( $size / $gb, 2 ) . " GB";
	} else {
		return round ( $size / $tb, 2 ) . " TB";
	}
}

function aop($in){
	
	switch ($in){
		case 1: return  '上午';break;
		case 2: return '下午';break;
		default:return  '上午';
	}
}
