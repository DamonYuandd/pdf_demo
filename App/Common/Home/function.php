<?php

function homeAdvert2($advertHomeList2) {
	if( !empty($advertHomeList2) ) {
		for ($i=0;$i<3;$i++) {
			if( !empty($advertHomeList2[$i]) ) {
				echo $advertHomeList2[$i]['title'].'-';
				unset($advertHomeList2[$i]);
			}
			
		}
		homeAdvert2($advertHomeList2);
	} else {
		
	}
}
//服务网点列表
function homeContact() {
	$systemDao = M('System');
	$system = $systemDao->where(array('lang'=>'mobile'))->field('mobile_theme,address,telephone,email,sina_wb_url,qq_wb_url')->find();
	$html .= '<div class="contact"><ol class="olul">';
	if( !empty($system['address']) ) {
		$html .= '<li class="addlist"><a href="#"><div class="icon_left i_add"></div><dl><dt>联系地址</dt><dd>'.$system['address'].'</dd></dl></a></li>';
	}
	if( !empty($system['telephone']) ) {
		$html .= '<li class="addlist"><a href="#"><div class="icon_left i_tel"></div><dl><dt>联系电话</dt><dd>'.$system['telephone'].'</dd></dl></a></li>';
	}
	if( !empty($system['email']) ) {
		$html .= '<li class="addlist"><a href="#"><div class="icon_left i_tel"></div><dl><dt>客服邮箱</dt><dd>'.$system['email'].'</dd></dl></a></li>';
	}
	if( !empty($system['sina_wb_url']) ) {
		$html .= '<li class="addlist"><a href="'.$system['sina_wb_url'].'"><div class="icon_left i_tel"></div><dl><dt>新浪微博</dt><dd>'.$system['sina_wb_url'].'</dd></dl></a></li>';
	}
	if( !empty($system['qq_wb_url']) ) {
		$html .= '<li class="addlist"><a href="'.$system['qq_wb_url'].'"><div class="icon_left i_tel"></div><dl><dt>腾讯微博</dt><dd>'.$system['qq_wb_url'].'</dd></dl></a></li>';
	}
	echo $html .= '</ol></div>';
}


//首页联系我们列表
function mobileWebsite($is,$public_mobile) {
	import ( "ORG.Util.MobileDetect" );
	$detect = new MobileDetect();
	$mwDao = M('MobileContact');
	$lang = L('language');
	$dataList = $mwDao->where(array($is=>1,'is_publish'=>1,'hardware' => 'mobile','lang'=> $lang))->order('ordernum desc')->select();
	$html .= '<div class="contact"><ol class="olul">';
	foreach ($dataList as $key => $value) {
		$ct = $value['content_type'];
		if($ct=='url') {
			$href = $value['content'];
		} elseif ($ct=='mail' || $ct=='email') {
			$href = 'mailto:'.$value['content'];
		} elseif ($ct=='phone' || $ct=='mobile') {
			if($detect->isiOS())
			{
				$href = 'callto:'.$value['content'];
			}else if($detect->isAndroidOS())
			{
				$href = 'wtai://wp/mc;'.$value['content'];
			}
		} elseif($ct=='sms'){
			$href = 'smsto:'.$value['content'];
			}
		else {
			$href = '#';
		}
		if( $value['image']=='address' ) {
			$href = __APP__.'/Contact';
		}
		//$html .= '<li class="addlist"><a href="'.$href.'"><div class="icon_theme"><img src="'.$public_mobile.'/images/themes/blue/'.$value['image'].'.png"></div><dl><dt>'.$value['title'].'</dt><dd >'.str_cut($value['content'],0,50,'...').'</dd></dl></a></li>';
		$html .= '<li class="addlist"><a href="'.$href.'"><div class="icon_theme"><img src="'.$public_mobile.'/images/themes/blue/'.$value['image'].'.png"></div><dl><dt>'.$value['title'].'</dt><dd >'.$value['content'].'</dd></dl></a></li>';
	}
	echo $html .= '</ol></div>';
}
//
function mobileWebsite2($is,$public_mobile) {
	import ( "ORG.Util.MobileDetect" );
	$detect = new MobileDetect();
	$marketDao = M('Market');
	$dataList = $marketDao->where(array('category_id'=>$cid,'lang'=>'mobile','is_publish'=>1,'traffic1'=>'','traffic2'=>''))->select();
	$html .= '<div class="contact"><ol class="olul">';
	foreach ($dataList as $key => $value) {
		$ct = $value['content_type'];
		if($ct=='url') {
			$href = $value['content'];
		} elseif ($ct=='email' || $ct=='mail') {
			$href = 'mailto:'.$value['content'];
		} elseif ($ct=='phone' || $ct=='mobile') {
			if($detect->isiOS())
			{
				$href = 'callto:'.$value['content'];
			}else if($detect->isAndroidOS())
			{
				$href = 'wtai://wp/mc;'.$value['content'];
			}
		}elseif($ct=='sms'){
			$href = 'smsto:'.$value['content'];
			} 
		else {
			$href = '#';
		}
		if( $value['image']=='address' ) {
			$href = __APP__.'/Contact';
		}
		$html .= '<li class="addlist"><a href="'.$href.'"><div class="icon_theme"><img src="'.$public_mobile.'/images/themes/blue/'.$value['image'].'.png"></div><dl><dt>'.$value['title'].'</dt><dd>'.$value['content'].'</dd></dl></a></li>';
	}
	echo $html .= '</ol></div>';
}
//手机信息通过内容类型输出内容
function getMobileWebsiteContentByContentType($str_ct) {
	$mwDao = M('MobileContact');
	$lang = L('language');
	echo $mwDao->where(array('is_publish'=>1,'content_type'=>$str_ct,'lang' => $lang))->getField('content');
}

//网点分公司信息列表
function mobileMarket($cid,$public_mobile) {
	import ( "ORG.Util.MobileDetect" );
	$detect = new MobileDetect();
	$lang = L('language');
	$marketDao = M('Market');
	$dataList = $marketDao->where(array('category_id'=>$cid,'lang'=>$lang,'hardware' => 'mobile','is_publish'=>1,'traffic1'=>'','traffic2'=>''))->order('ordernum desc')->select();
	$html = '';
	foreach ($dataList as $key => $value) {
		$ct = $value['image'];
		if($ct=='url') {
			$href = $value['content'];
		} elseif ($ct=='email' || $ct =='mail') {
			$href = 'mailto:'.$value['content'];
		} elseif ($ct=='phone' || $ct=='mobile' || $ct =='tel') {
			if($detect->isiOS())
			{
				$href = 'callto:'.$value['content'];
			}else if($detect->isAndroidOS())
			{
				$href = 'wtai://wp/mc;'.$value['content'];
			}
			//$href = 'callto:'.$value['content'];
		} elseif($ct=='sms'){
			$href = 'smsto:'.$value['content'];
			}
		else {
			$href = '#';
		}
		$html .= '<li class="addlist"><a href="'.$href.'"><div class="icon_theme"><img src="'.$public_mobile.'/images/themes/blue/'.$value['image'].'.png"></div><dl><dt>'.$value['title'].'</dt><dd>'.$value['content'].'</dd></dl></a></li>';
	}
	echo $html;
}

//网点交通信息输出
function mobileMarketTraffic($cid) {
	$marketDao = M('Market');
	$lang = L('language');
	$traffic = $marketDao->where(array('category_id'=>$cid,'lang'=>$lang,'hardware' => 'mobile','is_publish'=>1,'traffic1'=>array('neq',''),'traffic2'=>array('neq','')))->find();
	return $traffic;
}

//二级菜单输出
function mobileTwoMenu($alias,$url) {
	$categoryDao = M('Category');
	$lang = L('language');
	$category = $categoryDao->where( array('alias'=>$alias, 'is_publish'=>1) )->find();
	$categoryList = $categoryDao->where( array('pid'=>$category['id'], 'is_publish'=>1, 'lang'=>$lang,'hardware' => 'mobile') )->order('ordernum desc')->select();
	foreach ($categoryList as $key => $value) {
		$html .= '<a href="'.__APP__.'/'.$url.'/cid/'.$value['id'].'.html'.'"><div class="tnav"><div class="two-navbar-icon"></div>'.$value['title'].' </div></a><div class="navline"></div>';
	}
	echo $html;
}

//SEO自动输出，优先输出文章SEO
function echoSEO($system, $id, $headTitle) {
	if( !empty($id) ) {
		if( MODULE_NAME=='Product' ) {
			$model = 'Goods';
		} else {
			$model = MODULE_NAME;
		}
		$dao = M($model);
		if( !empty($dao) ) {
			
			$obj = $dao->where(array('id'=>$id))->find();
			$seo_title = $obj['seo_title'];
			$seo_keywords = $obj['seo_keywords'];
			$seo_description = $obj['seo_description'];
			if( empty($seo_title) ) {
				$seo_title = $system['seo_title'];
			}
			if( empty($seo_keywords) ) {
				$seo_keywords = $system['seo_keywords'];
			}
			if( empty($seo_description) ) {
				$seo_description = $system['seo_description'];
			}
		}
	}else if( MODULE_NAME=='About' ){
			$model = 'News';
			$lang = L('language');
			$dao = M($model);
			$id = $dao->where(array('lang' => $lang,'category_id' => $_GET['cid']))->find();
		if( !empty($dao) ) {
			$obj = $dao->where(array('id'=>$id['id']))->find();
			$seo_title = $obj['seo_title'];
			$seo_keywords = $obj['seo_keywords'];
			$seo_description = $obj['seo_description'];
			if( empty($seo_title) ) {
				$seo_title = $system['seo_title'];
			}
			if( empty($seo_keywords) ) {
				$seo_keywords = $system['seo_keywords'];
			}
			if( empty($seo_description) ) {
				$seo_description = $system['seo_description'];
			}
		}
			}  
	
	elseif($system) {
		$seo_title = $system['seo_title'];
		$seo_keywords = $system['seo_keywords'];
		$seo_description = $system['seo_description'];
	}
	if( !empty($headTitle) ) {
		$seo_title = $headTitle;
	}
	echo '<title>'.$seo_title.'</title><meta name="keywords" content="'.$seo_keywords.'"><meta name="description" content="'.$seo_description.'">';
}



//网点分公司信息列表
function mobileMarket_zou($cid,$public_mobile) {
	import ( "ORG.Util.MobileDetect" );
	$detect = new MobileDetect();
	$lang = L('language');
	$marketDao = M('Market');
	$dataList = $marketDao->where(array('category_id'=>$cid,'lang'=>$lang,'hardware' => 'mobile','is_publish'=>1,'traffic1'=>'','traffic2'=>''))->order('ordernum desc')->select();
	$html = '';
	foreach ($dataList as $key => $value) {
		$ct = $value['image'];
		if($ct=='url') {
			$href = $value['content'];
		} elseif ($ct=='email' || $ct=='mail') {
			$href = 'mailto:'.$value['content'];
		} elseif ($ct=='phone' || $ct=='mobile' || $ct =='tel') {
			if($detect->isiOS())
			{
				$href = 'callto:'.$value['content'];
			}else if($detect->isAndroidOS())
			{
				$href = 'wtai://wp/mc;'.$value['content'];
			}
			//$href = 'callto:'.$value['content'];
		} elseif($ct=='sms'){
			$href = 'smsto:'.$value['content'];
			}
		else {
			$href = '#';
		}
		$html .= '<li class="netbox-bar"><a href="'.$href.'"><div class="neticon"><img src="'.$public_mobile.'/images/themes/blue/'.$value['image'].'.png"></div><dl><dt>'.$value['title'].'</dt><dd>'.$value['content'].'</dd></dl></a></li>';
	}
	echo $html;
}



//联系我们列表
function mobileWebsite_zou($is,$public_mobile) {
	import ( "ORG.Util.MobileDetect" );
	$detect = new MobileDetect();
	$mwDao = M('MobileContact');
	$lang = L('language');
	$dataList = $mwDao->where(array($is=>1,'is_publish'=>1,'hardware' => 'mobile','lang'=> $lang))->order('ordernum desc')->select();
	$html .= '<ol>';
	foreach ($dataList as $key => $value) {
		$ct = $value['content_type'];
		if($ct=='url') {
			$href = $value['content'];
		} elseif ($ct=='mail' || $ct=='email') {
			$href = 'mailto:'.$value['content'];
		} elseif ($ct=='phone' || $ct=='mobile') {
			if($detect->isiOS())
			{
				$href = 'callto:'.$value['content'];
			}else if($detect->isAndroidOS())
			{
				$href = 'wtai://wp/mc;'.$value['content'];
			}
		} elseif($ct=='sms'){
			$href = 'smsto:'.$value['content'];
			}
		else {
			$href = '#';
		}
		if( $value['image']=='address' ) {
			$href = __APP__.'/Contact';
		}
		$html .= '<li class="netbox-bar"><a href="'.$href.'"><div class="neticon"><img src="'.$public_mobile.'/images/themes/blue/'.$value['image'].'.png"></div><dl><dt>'.$value['title'].'</dt><dd>'.$value['content'].'</dd></dl></a></li>';
	}
	echo $html .= '</ol>';
}