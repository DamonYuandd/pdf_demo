<?php
/**
 * 字符串截取，支持中文和其他编码
 *
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断字符串后缀
 * @return string
 */
function str_cut($str, $start=0, $length, $suffix="",$charset="utf-8")
{
	if(function_exists("mb_substr")){
		echo mb_substr($str, $start, $length, $charset).$suffix;return;
	}
	elseif(function_exists('iconv_substr')){
		echo iconv_substr($str,$start,$length,$charset).$suffix;return;
	}
	$re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$slice = join("",array_slice($match[0], $start, $length));
	echo $slice.$suffix;
}

/*********************************Home常用方法************************************/
/**
 * 
 * 通过ID或别名输出分类标题
 * @param int $id
 * @param string $alias
 */
function c_title( $my_id, $alias, $lang ) {
	if($lang == 'mobile'){
		$hare = 'mobile';
		$lang = L('language'); 
	}else{
		$hare = 'pc';
	}
	if( empty($lang) ) {
		$lang = L('language'); //获取当前语言
	}
	$categoryDao = M('Category');
	if( !empty($my_id) ) {
		echo $categoryDao->getField('title', array('id'=>$my_id,'lang'=>$lang,'hardware' => $hare));
	} else {
		echo $categoryDao->getField('title', array('alias'=>$alias,'lang'=>$lang,'hardware' => $hare));
	}
}

/**
 * 可通过上级ID和别名获取分类下拉列表
 * @param int $pid
 * @param string $alias
 */
function selectCategoryOptions( $pid, $alias ) {
	$categoryDao = M('Category');
	$lang = L('language'); //获取当前语言
	import ( "ORG.Util.MobileDetect" );
	$detect = new MobileDetect();
	if( !empty($alias) ) {
		if($detect->isMobile()){
			$pid = $categoryDao->getField('id', array('alias'=>$alias,'lang'=>$lang,'hardware' => 'mobile'));
		}else{
			$pid = $categoryDao->getField('id', array('alias'=>$alias,'lang'=>$lang,'hardware' => 'pc'));
		}
	}
	if($detect->isMobile()){
		$categoryList = $categoryDao->where(array('pid'=>$pid,'lang'=> $lang,'hardware' => 'mobile'))->order('ordernum desc')->select();
	}else{
		$categoryList = $categoryDao->where(array('pid'=>$pid,'lang'=>$lang,'hardware' => 'pc'))->order('ordernum desc')->select();
	}
	$str .= '<select name="category_id" id="category_id">';
	foreach($categoryList as $key=>$value) {
		$str .= '<option value="'.$value['id'].'">'.$value['title'].'</option>';
	}
	echo $str .= '</select>';
}

/**
 * 友情链接下拉
 */
function selectLinkOptions() {
	$linkDao = M('Link');
	$lang = L('language'); //获取当前语言
	$linkList = $linkDao->where(array('is_publish'=>1))->order('is_top desc, ordernum desc, create_time desc')->select();
	foreach($linkList as $key=>$value) {
		$str .= '<option value="'.$value['url'].'">'.$value['title'].'</option>';
	}
	echo $str;
}

/**
 * 切换语言--只有两种语言时可用
 */
function switchLang() {
	$lang = L('language');
	if( $lang=='zh-CN' || $lang=='zh-cn' ) {
		echo '<a href="__APP__?l=en-US">ENGLISH</a>';
	} else {
		echo '<a href="__APP__?l=zh-CN">简体中文</a>';
	}
}

/**
 * 查找产品多图片
 */
function selectGoodsPhoto() {
	$gpDao = M('GoodsPhoto');
	$gpList = $gpDao->where(array('goods_id'=>$_GET['id']))->order('ordernum desc')->select();
	return $gpList;
}

/**
 * 自动获取分类模板URL
 */
function getCategoryUrl( $vo ) {
    if( empty($vo['alias']) ) {
        $url = __APP__.'/'.MODULE_NAME.'/index/cid/'.$vo['my_id'].'.html';
    } else {
        $url = __APP__.'/'.$vo['alias'].'.html';
    }
    return $url;
}

/**
 * 查找子分类
 */
function selectSubCategory( $pid ) {
	import ( "ORG.Util.MobileDetect" );
	$detect = new MobileDetect();
	if($detect->isMobile()){
			$hardware = 'mobile';
	}else{
			$hardware = 'pc';
	}
    $categoryDao = M('Category');
    $lang = L('language');
    $categoryList = $categoryDao->where(array('pid'=>$pid,'is_publish'=>1,'lang'=>$lang,'hardware'=>$hardware))->order('ordernum desc')->select();
   return $categoryList;
}
/**
 * 单个广告输出
 * @param int $cid
 */
function getAdvertOne( $cid ) {
    $advertDao = M('Advert');
    $lang = L('language');
    $obj = $advertDao->where(array('category_id'=>$cid,'is_publish'=>1,'lang'=>$lang))->order('ordernum desc, id desc')->find();
    echo '<a href="'.$obj['url'].'"><img src="'.C('UPLOAD_FILE_RULE').C('USER_CNUM').'/images/advert/m_'.$obj['image'].'" width="'.$obj['width'].'" height="'.$obj['height'].'" /></a>';
}
//通过cid查找循环图片 
function getAdvertList($cid){
	$advertDao = M('Advert');
    $lang = L('language');
    $list = $advertDao->where(array('category_id'=>$cid,'is_publish'=>1,'lang'=>$lang))->order('ordernum desc, id desc')->select();
	return $list;
}
//循环图片根据别名
function getAdvertListAlias($alias){
	$cateDb = M('Category');
	$lang = L('language');
	import ( "ORG.Util.MobileDetect" );
	$detect = new MobileDetect();
	if($detect->isMobile()){
		$getCid = $cateDb->where(array('alias' => $alias,'is_publish' => 1,'hardware'=>'mobile','lang'=> $lang))->find();
	}else{
		$getCid = $cateDb->where(array('alias' => $alias,'is_publish' => 1,'hardware'=>'pc','lang'=>$lang))->find();
	}
	$advertDao = M('Advert');
    $list = $advertDao->where(array('category_id'=>$getCid['id'],'is_publish'=>1))->order('ordernum desc')->select();
	if($list){
		foreach($list as $key => $value){
			$list[$key]['img'] = C('UPLOAD_FILE_RULE').C('USER_CNUM').'/images/advert/m_'.$value['image'];
		}
	}else{
		$list[0]['target'] = '_top';
		$list[0]['url'] = __APP__;
		$list[0]['img'] = 'http://img.huyionline.cn/default/default.jpg' ;
	}
	return $list;
}
//根据别名获取文章信息
function getNewsByAlias($alias){
	$cateDb = M('Category');
	$lang = L('language');
	import ( "ORG.Util.MobileDetect" );
	$detect = new MobileDetect();
	if($detect->isMobile()){
			$hardware = 'mobile';
	}else{
			$hardware = 'pc';
	}
	$getCid = $cateDb->where(array('alias' => $alias,'is_publish' => 1,'hardware' => 'pc','lang'=>$lang,'hardware' => $hardware))->find(); //PC端
	$newsDao = M('News');
	$obj = $newsDao->where(array('category_id'=>$getCid['id'],'is_publish' => 1,'lang'=>$lang))->find();
	return $obj;
}
//通过别名获取一张广告
function getAdOneAlias($alias){
	$cateDb = M('Category');
	$lang = L('language');
	$getCid = $cateDb->where(array('alias' => $alias,'is_publish' => 1,'lang' => $lang))->find();
	$advertDao = M('Advert');
    $obj = $advertDao->where(array('category_id'=>$getCid['id'],'is_publish'=>1,'lang'=>$lang))->order('ordernum desc, id desc')->find();
	if($obj){
		if($obj['net_image']){
			$url = $obj['net_image'];
		}else{
			$url = C('UPLOAD_FILE_RULE').C('USER_CNUM').'/images/advert/m_'.$obj['image'];
		}
	}else{
		$obj['url'] = __APP__ ;
		$url = 'http://img.huyionline.cn/default/default.jpg' ;
	}
  //  echo '<a href="'.$obj['url'].'"><img src="'.C('UPLOAD_FILE_RULE').C('USER_CNUM').'/images/advert/m_'.$obj['image'].'" width="'.$obj['width'].'" height="'.$obj['height'].'" /></a>';
   echo '<a href="'.$obj['url'].'"><img src="'.$url.'" width="'.$obj['width'].'" height="'.$obj['height'].'" /></a>';
}
//通过别名获取一张广告2
function getAdOneAlias2($alias){
	$cateDb = M('Category');
	$lang = L('language');
	import ( "ORG.Util.MobileDetect" );
	$detect = new MobileDetect();
	if($detect->isMobile()){
			$hardware = 'mobile';
	}else{
			$hardware = 'pc';
	}
	
	$getCid = $cateDb->where(array('alias' => $alias,'is_publish' => 1,'lang' => $lang))->find();
	$advertDao = M('Advert');
    $obj = $advertDao->where(array('category_id'=>$getCid['id'],'is_publish'=>1,'lang'=>$lang))->order('ordernum desc, id desc')->find();
	if($obj){
		if($obj['net_image']){
			$url = $obj['net_image'];
		}else{
			$url = C('UPLOAD_FILE_RULE').C('USER_CNUM').'/images/advert/m_'.$obj['image'];
		}
	}else{
		$obj['url'] = __APP__ ;
		$url = 'http://img.huyionline.cn/default/default.jpg' ;
	}
   echo '<a href="'.$obj['url'].'"><img src="'.$url.'"/></a>';
  // echo '<a href="'.$obj['url'].'"><img src="'.C('UPLOAD_FILE_RULE').'0_yadmin_v2_1/images/advert/m_'.$obj['image'].'"/></a>';
}
function strigtags($string){
  echo strip_tags(htmlspecialchars_decode($string));
}
function sysInfo($lang){
	$db = M('System');
	$getlang = L('language'); 
	$getinfo = $db->where(array('hardware' => $lang , 'lang' => $getlang))->find();
	return $getinfo;
}

//根据别名获取文章列表
function getNewsByList($alias,$is_home = 1,$hardware = 'pc'){
	$cateDb = M('Category');
	$lang = L('language');
	$getCid = $cateDb->where(array('alias' => $alias,'is_publish' => 1,'lang'=>$lang,'hardware' => $hardware))->find();
	$newsDao = M('News');
	$obj = $newsDao->where(array('category_id'=>$getCid['id'],'is_publish' => 1,'lang'=>$lang,'is_home' => $is_home,'hardware' => $hardware))->order('ordernum desc, id desc')->select();
	return $obj;
}

//产品分类
 function GoodsCate($num){
		$cate =  M('Category');
		import ( "ORG.Util.MobileDetect" );
		$detect = new MobileDetect();
		$cid = $cate->where(array('alias' => 'Goods','is_publish' => 1))->find();
		if($detect->isMobile()){
				$hardware = 'mobile';
		}else{
			$hardware = 'pc';
		}	
		if($num){
			$Ftitle = $cate->where(array('pid' => $cid['id'],'is_publish' => 1,'lang' => L('language'),'hardware' => $hardware))->order('ordernum desc, id desc')->limit($num)->select();
		}else{
		$Ftitle = $cate->where(array('pid' => $cid['id'],'is_publish' => 1,'lang' => L('language'),'hardware' => $hardware))->order('ordernum desc, id desc')->select();}
		return $Ftitle;
	}
//获取公共信息
function commonInfo(){
	    $db = M('Common');
	    $result = $db->find();
		return $result;
}
//获取PC联系信息
function pcContactInfo(){
	$db = M('System');
	$lang = L('language');
	$obj = $db->where(array('hardware' => 'pc' ,'lang' => $lang))->find();
	return $obj;
}
//手机下拉特殊处理
function SpecialTreatment($alias){
	if($alias == 'Goods' || $alias == 'About' || $alias == 'News'){
		return true;
	}else{
		return false;
	}
}
//判断JSON格式
 function json_parser($str){
        $arr = json_decode($str,true);
        if(gettype($arr) != "array"){
            return false;
        }else {
            return $arr;
        }
    }
//判断图片是否存在
function img_exits($ur,$title,$w = 400,$h =400,$m = 'm_',$s = 's_')
{
	$url = $ur.$title;
	//$check = 'http://172.16.9.20/0-Y+09_v2/outPutImg.php?url=';
	$check = 'http://img.huyionline.cn/default/outPutImg.php?url=';
    if(@fopen($url, 'r'))
		echo $check.$url.'&pw='.$w.'&ph='.$h;
	//echo $url;
     //  return true;
    else {
		$url2 = $ur.$m.$titile;
		if(@fopen($url2, 'r')){
		   echo $check.$ur.$m.$title.'&pw='.$w.'&ph='.$h;
		//	echo $ur.$m.$title;
		}else{
			echo $check.$ur.$m.$title.'&pw='.$w.'&ph='.$h;
		//	echo $ur.$s.$title;
		}
	}
    //    return false;
}
//判断是否为空值
function is_empty($obj,$tips='正在建设中...'){
	if(!$obj){
		return $tips;
	}else{
		return $obj;
	}
}
//apple Icon 
function app_icon($part,$app,$index = true){
	if($index == true){
		echo '<link rel="stylesheet" href="'.__PUBLIC__.'/css/add2home.css" type="text/css" charset="utf-8"><link rel="apple-touch-icon" href="'.$part.'/images/mobile/'.$app.'" />'.'<script>var app_logo=\''.$part.'/images/mobile/'.$app.'\';</script>'.'<script type="text/javascript" src="'.__PUBLIC__.'/js/add2home.js"></script>';
	}else{
		echo '<link rel="stylesheet" href="'.__PUBLIC__.'/css/add2home.css" type="text/css" charset="utf-8"><link rel="apple-touch-icon" href="'.$part.'/images/mobile/'.$app.'" />';
	}
}
// 视频播放
function videoPlayer($w = 400,$h = 400,$is_return = false){	//宽，高，是否采用返回类型
	$db = M('Video');
	$result = $db->where(array('is_publish' => 1 , 'is_show' => 1))->find();
	if($is_return == false){	//采用默认方式
		if($result['is_online'] == 0){	//播放本地文件
		echo '<div id="a1"></div><script type="text/javascript">var cpath = "'.__ROOT__.'/Public/video/assets/";</script>;<script type="text/javascript" src="'.__ROOT__.'/Public/video/ckplayer.js" charset="utf-8"></script>';
		echo "<script type=\"text/javascript\">
            var flashvars={
            f:'http://img.huyionline.cn/".C('USER_CNUM')."/images/video/".$result['downfile']."',//视频地址
            a:'',//调用时的参数，只有当s>0的时候有效
            s:'0',//调用方式，0=普通方法（f=视频地址），1=网址形式,2=xml形式，3=swf形式(s>0时f=网址，配合a来完成对地址的组装)
            c:'0',//是否读取文本配置,0不是，1是
            x:'',//调用xml风格路径，为空的话将使用ckplayer.js的配置
            i:'".C('UPLOAD_FILE_RULE').C('USER_CNUM').'/images/video/m_'.$result['image']."',//初始图片地址
            d:'',//暂停时播放的广告，swf/图片
            u:'',//暂停时如果是图片的话，加个链接地址
            l:'',//视频开始前播放的广告，swf/图片/视频
            r:'',//视频开始前播放图片/视频时加一个链接地址
            t:'5',//视频开始前播放swf/图片时的时间
            e:'2',//视频结束后的动作，0是调用js函数，1是循环播放，2是暂停播放，3是调用视频推荐列表的插件
            v:'80',//默认音量，0-100之间
            p:'0',//视频默认0是暂停，1是播放
            h:'1',//播放http视频流时采用何种拖动方法，0是按关键帧，1是按关键时间点
            q:'',//视频流拖动时参考函数，默认是start
            m:'0',//默认是否采用点击播放按钮后再加载视频，0不是，1是,设置成1时不要有前置广告
            g:'',//视频直接g秒开始播放
            j:'',//视频提前j秒结束
            k:'',//提示点时间，如 30|60鼠标经过进度栏30秒，60秒会提示n指定的相应的文字
            n:'',//提示点文字，跟k配合使用，如 提示点1|提示点2
            b:'0x000',//播放器的背景色，如果不设置的话将默认透明
            w:''//指定调用自己配置的文本文件,不指定将默认调用和播放器同名的txt文件
            //调用播放器的所有参数列表结束
            };
            var params={bgcolor:'#000000',allowFullScreen:true,allowScriptAccess:'always'};
            var attributes={id:'ckplayer_a1',name:'ckplayer_a1'};
            swfobject.embedSWF('__ROOT__/Public/video/ckplayer.swf', 'a1', '".$w."', '".$h."', '10.0.0','__ROOT__/Public/video/expressInstall.swf', flashvars, params, attributes);	
        </script>" ;
		}else{	//播放在线视频
			echo $result['url'];
		}
	}else{	//返回数组方式调用
		return $result;
	}
}

//根据别名获取第一个单页信息
function oneWidget($alias,$hare = 'mobile'){
	$categoryDao = M('Category','AdvModel');
	$modelDao = M('News');
	$lang = L('language');
	$category = $categoryDao->where( array('alias'=>$alias) )->find();
	$category = $categoryDao->where( array('pid'=>$category['id'], 'is_publish'=>1,'hardware' => $hare , 'lang' => $lang) )->order('ordernum desc')->first();
	$obj = $modelDao->where( array('category_id'=>$category['id'], 'lang'=>$lang, 'is_publish'=>1) )->find();
	return $obj;
}

//根据别名获取分类信息
function cateByAlias($alias){
	$db = M('Category');
	$lang = L('language'); //获取当前语言
	$category = $db->where( array('alias'=>$alias ,'hardware' => 'pc','lang'=>$lang) )->find();
		if(!$category){
			$category = $db->where( array('alias'=>$alias) )->find();
	}
	return $category;
}


//CURL
function actionPost($url,$data){ // 模拟提交数据函数
	$curl = curl_init(); // 启动一个CURL会话
	curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
	curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
	curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookie.txt'); // 读取上面所储存的Cookie信息
	curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
	curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
	$tmpInfo = curl_exec($curl); // 执行操作
	if (curl_errno($curl)) {
		echo 'Errno'.curl_error($curl);
	}
	curl_close($curl); // 关键CURL会话
	return $tmpInfo; // 返回数据
}

 function getBytes($string) {
	$bytes = array();
	for($i = 0; $i < strlen($string); $i++){
		$bytes[] = ord($string[$i]);
	}
	$bytes = implode(',', $bytes);
	return $bytes;
}

function CBCencode($data,$privateKey,$iv){
	$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128,$privateKey , $data, MCRYPT_MODE_CBC, $iv);
	return $encrypted;
	//echo(base64_encode($encrypted));
}


//验证手机号码
function checkMobile($mobilephone){
	if(preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$mobilephone)){
		return true;
	}else{
		return false;
	}
}

//验证邮件格式
function checkEmail($email){
	$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
	if ( preg_match( $pattern, $email ) )
	{
		 return true;
	}else{
		 return false;
	}
}

// 定义一个函数getIP()
function getIP()
{
	global $ip;

	if (getenv("HTTP_CLIENT_IP"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if(getenv("HTTP_X_FORWARDED_FOR"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if(getenv("REMOTE_ADDR"))
		$ip = getenv("REMOTE_ADDR");
	else
		$ip = "Unknow";

	return $ip;
}

//输出祖名
function echoGroup($id,$type= 'group'){
	$obj = M($type)->where(array('id' => $id))->find();
	return $obj['title'];
}
