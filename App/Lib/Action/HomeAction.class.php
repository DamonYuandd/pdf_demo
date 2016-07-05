<?php

class HomeAction extends CommonAction
{
	public $is_mobile = false;
	
	public $mobile_theme = '';
	
	public $system = null;

	public function _empty() {
		echo "<script>window.location.href='".__APP__."';</script>";
	//	$this->display('Index:'.MODULE_NAME);
	}
		
	function _initialize() {
	  
		parent::_initialize();

		$resutl = $this->getPart();	//获取路径
		$part = $this->getPartList();	//获取栏目
		$this->assign('part',$part);
		import('ORG.Util.MobileDetect');
		$detect = new MobileDetect();
		
		if( $detect->isMobile()==true ){
			$this->assign('Sys_Langs',$this->sysLangs('mobile'));	//获取语言
		}else{
			$this->assign('Sys_Langs',$this->sysLangs('pc'));
			}		
		$commomInfo = commonInfo();
		$commomInfo['qq_nums'] = explode(',',$commomInfo['qq_nums']);
		
		$systemDao = M('System');
		$this->mobile_theme = $commomInfo['mobile_theme'];	//获取手机主题
		//$this->web_theme = $commomInfo['web_theme']; 	//获取PC主题
		$this->web_theme = 'Home';
		$pPart = __ROOT__.'/App/Tpl/default/'.$commomInfo['web_theme'].'/Public';
		$this->assign('openTime',C('OPEN_TIME'));
        $this->assign('showTime',C('SHOW_TIME'));
		$this->assign('common',$commomInfo);
		$this->assign('flow_code',$commonInfo['flow_code']);	//流量统计
		//$pPart = __ROOT__.'/'.APP_NAME.'/Tpl/default/'.$this->web_theme.'/Public' ;	//前台图片路径	
		$this->assign('wTheme',$pPart);
	//	$domain = $systemDao->where( array('lang'=>'mobile') )->getField('domain');
	//	if( strpos($domain,$_SERVER["HTTP_HOST"])!==false ) {//设置手机域名
		if( strpos($part['url'],$_SERVER["HTTP_HOST"])==true ) {//域名
			$this->is_mobile = true;
			$this->mobileConfig($detect,$systemDao);
		}  else {
			$this->assignSystem($systemDao);
		}
	
	}
	
	//页面跳转
	function forward($msg,$url){
		echo "<script type='text/javascript'> alert('$msg');location.href='$url';</script>";
		
	}


	// 简历文件上传
	protected function _upload($uploaddir='',$field='image',$thumb=true,$width=100,$height=100) {
		import("ORG.Util.UploadFile");
		$upload = new UploadFile();
		$upload->maxSize = 3292200;
		$upload->allowExts = explode(',', 'doc,jpg,gif,png,jpeg');
		$upload->savePath = C('UPLOAD_FILE_RULE').'images/'.$uploaddir;
		if( $thumb==true ) {
			$upload->thumb = true;
			$upload->imageClassPath = 'ORG.Util.Image';
			$upload->thumbPrefix = 'm_,s_';
			$upload->thumbMaxWidth = '1000,'.$width;
			$upload->thumbMaxHeight = '1000,'.$height;
			$upload->thumbRemoveOrigin = true;
		}
		$upload->saveRule = uniqid;
		if (!$upload->upload()) {
			$this->error($upload->getErrorMsg());
		} else {
			$uploadList = $upload->getUploadFileInfo();
			//import("@.ORG.Image");
			//给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
			//Image::water($uploadList[0]['savepath'] . 'm_' . $uploadList[0]['savename'], '/ThinkPHP_2.2_Full/Examples/File/Tpl/default/Public/Images/logo2.png');
			$_POST[$field] = $uploadList[0]['savename'];
		}

	}


	public function mobileConfig($detect,$systemDao) {
		//$this->assignSystem($systemDao,'mobile');
		$this->assignSystem($systemDao);
		
	}

	//获取网站信息
	public function assignSystem($systemDao, $lang ) {
		if( empty($lang) ) {
			$lang = L('language'); //获取当前语言	
		}
		if($this->is_mobile==true){
			$hardware = 'mobile';
		}else{
			$hardware = 'pc';
		}
		$this->system = $systemDao->where( array('lang'=>$lang,'hardware' => $hardware) )->find();
		$this->assign('public_mobile', __ROOT__.'/'.APP_NAME.'/Tpl/default/'.$this->mobile_theme.'/Public');
		$this->assign('mobile_theme', $this->mobile_theme);
		$this->assign('system', $this->system);
	}

	//发送邮件
	function sendEmail($obj) {
			import ( "ORG.Email.PHPMailer" );
			$mail = new PHPMailer ();
	
			$mail->CharSet = "utf-8";
			$mail->Encoding = "base64";
			$mail->Host       = $host['email_smtp_host'];
			//$mail->SMTPDebug = 1;
			$mail->IsSMTP ();
			$mail->Host = $obj['email_smtp_host'];
			$mail->SMTPAuth = true;
			if(!empty($obj['email_smtp_port'])){
				$mail->Port = $obj['email_smtp_port'];
			}
			$mail->Username = $obj['email_username'];
			$mail->Password = $obj['email_password'];
			$mail->SetFrom ( $obj['email_address'], $obj['email_auto'] );
			$mail->AddReplyTo ( $obj['email_address'], $obj['email_auto'] );
			$mail->Subject = $obj['email_subject'];
			 
			$mail->MsgHTML ( $obj['body'] );
			$mail->AddAddress ( $obj['user_email'], $obj ["user_email"] );
			return $mail->Send ();
		}


	/**
	 +----------------------------------------------------------
	 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
	 +----------------------------------------------------------
	 * @param string $len 长度
	 * @param string $type 字串类型
	 * 0 字母 1 数字 其它 混合
	 * @param string $addChars 额外字符
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function rand_string($len=6,$type='',$addChars='') {
	    $str ='';
	    switch($type) {
	        case 0:
	            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
	            break;
	        case 1:
	            $chars= str_repeat('0123456789',3);
	            break;
	        case 2:
	            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
	            break;
	        case 3:
	            $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
	            break;
	        case 4:
	            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
	            break;
	        default :
	            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
	            $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
	            break;
	    }
	    if($len>10 ) {//位数过长重复字符串一定次数
	        $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
	    }
	    if($type!=4) {
	        $chars   =   str_shuffle($chars);
	        $str     =   substr($chars,0,$len);
	    }else{
	        // 中文随机字
	        for($i=0;$i<$len;$i++){
	          $str.= msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
	        }
	    }
	    return $str;
	}
	//自定义栏目
	public function getPartList(){
		$partDao = M('Category');
		$lang = L('language'); 
		$part = $partDao->where(array('is_publish'=>'1','is_nav'=>1,'hardware' => array('not in','mobile')))->order('orderNum desc')->select();
		import('ORG.Util.MobileDetect');
		$detect = new MobileDetect();
		if( $detect->isMobile()==true ){
			$part3 = $partDao->where(array('is_publish'=>'1','is_nav'=>1,'hardware' => 'mobile'))->order('orderNum desc')->select();
		}
		if($part3){
			$part = array_merge_recursive($part,$part3);
		}
		foreach($part as $key => $value){
			if(json_parser($value['title'])){
				$title = json_decode($value['title'],true);
				$part2[$key]['title'] = $title[$lang]['title'];
			}else{
				$part2[$key]['title'] = $value['title'];
			}
			$part2[$key]['alias'] = $value['alias'];
			$part2[$key]['class'] = 'm_'.$value['alias'];
			$part2[$key]['id'] = $value['id'];
			if($value['furl']){
				$part2[$key]['url'] = $value['furl'];
			}else{
				$part2[$key]['url'] = __APP__.'/'.$value['alias'];
				}
			//$title[$key]['alias'] = $value['alias']; 
		} 
	
		return $part2;
	}
    //获取路径
	public function getPart(){
		//获取合同号
		//if( $this->isConnect==true ) {
			$data['db_host'] = C('ACCOUNT_DB_HOST');
			$data['db_user'] = C('ACCOUNT_DB_NAME');
			$data['db_pwd']  = C('ACCOUNT_DB_PWD');
			$data['db_port'] = C('ACCOUNT_DB_PORT');
			$data['db_name'] = C('ACCOUNT_DB_NAME');
		//}
		$partDao = D('Home.Account');
		//$partDao = $Dao->db(2,'mysql://'.$data['db_user'].':'.$data['db_pwd'].'@'.$data['db_host'].':'.$data['db_port'].'/'.$data['db_name']);
		$result = $partDao->where(array('url'=>array('like','%,'.$_SERVER['SERVER_NAME'].',%'),'is_publish' => 1))->find();
		C('USER_CNUM',$result['contract_number']);//获取合同号
		$_SESSION['userNum'] = $result['contract_number'];
		$this->assign('USER_CNUM',$result['contract_number']);//获取合同号
		$this->assign('configInfo',$result);
		return $result;
	}
	//根据别名获取手机的文章id
	public function getCid($alias){
		$db = M('Category');
		$result = $db->where(array('alias' => $alias,'is_publish'=>1,'lang'=>'mobile'))->find();
		return $result['id'];
	}
	//获取后缀
	public function getSuffix($file_name)
	{
		$extend =explode("." , $file_name);
		$va=count($extend)-1;
		return $extend[$va];
	}
	//播放视频
	public function videoShow(){
		//判断是否为IOS
		import ( "ORG.Util.MobileDetect" );
		$detect = new MobileDetect();
		$db = M('Video');
		$re = $db->where(array('is_show' => 1,'is_publish' => 1))->find();
		$suffix = $this->getSuffix($re['downfile']);
		$re['suffix'] = $suffix;
		//改变视频播放
		if($detect->isiOS()){
			if($re['y_or_t'] == 0) //调用优酷
			{
				preg_match('[(sid\/)(.*)(/v)]', $re['url'], $arr);	//截取字符串的某个部分
				$getId = $arr[2];
				$re['url'] = '<video width="'.$re['vWidth'].'" height="'.$re['vHeight'].'" controls="controls" src="http://v.youku.com/player/getRealM3U8/vid/'.$getId.'/type//video.m3u8"></video>';
			}else	//土豆
			{
				preg_match('[(v\/)(.*)(\/\&)]', $re['url'], $arr);	//截取字符串的某个部分
				$getId = $arr[2];
				if($getId == '')
				{
					
					preg_match('[(l\/)(.*)(\/\&)]', $re['url'], $arr);	//截取字符串的某个部分
					$getId = $arr[2];
				}
				$re['url'] = '<iframe width="'.$re['vWidth'].'" height="'.$re['vHeight'].'" frameborder="0" src="http://www.tudou.com/programs/view/html5embed.action?code='.$getId.'"></iframe>';
			}
		}
		$this->assign('result',$re);
	}
	public function sysLangs($hardware){
		$db = M('Category');
		$fid = $db->where(array('alias' => $hardware ,'is_publish' => 1))->find();
		$result = $db->where(array( 'pid' => $fid['id'],'hardware' => $hardware,'is_publish' => 1))->select();
		return $result;
	} 
}
?>