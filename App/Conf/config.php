<?php
return array(

	/* 项目设定 */
	'APP_DEBUG'             => 1,
	'SHOW_PAGE_TRACE'       => 0,
	'TMPL_CACHE_ON'			=> false,
	'APP_GROUP_LIST'        => 'Home,Admin',
	'DEFAULT_ACTION'        => 'index', // 默认操作名称
	'TMPL_PARSE_STRING'     => array('__ADMIN__'=>__ROOT__.'/'.APP_NAME.'/Tpl/default/Admin',
									'__HOME__'=>__ROOT__.'/Public/design/',
								//	'__APP__'=> 'http://'.$_SERVER['HTTP_HOST']
									),

		//获取配置信息
		'DB_HOST'=>'localhost',
		'DB_NAME'=>'pdf',
		'DB_USER'=>'root',
		'DB_PWD'=>'root',
		'DB_PORT'=>'3306',
		'DB_PREFIX'=>'pdf_',

	/* URL设置 */
	'URL_ROUTER_ON'         => true,
	'URL_MODEL'             => 1,
	'URL_HTML_SUFFIX'       => '.html',

	/* 语言设置 */
	'LANG_SWITCH_ON'        => true, 
  	'DEFAULT_LANG'          => 'zh-cn',
  	'LANG_AUTO_DETECT'      => true,
	'UPLOAD_FILE_RULE'      => 'Public/',
	//'UPLOAD_FILE_RULE' => 'http://img.huyionline.cn/',
	'APP_ROOT_PATH' => 'Public/',
	//'APP_ROOT_PATH'  => 'J:\wamp\www',
	/* RBAC */
    'USER_AUTH_KEY'             =>'admin',	// 用户认证SESSION标记
    'TOTAL_NUM' => '1000',
	'IP_NUM' => 3,
	'OPEN_TIME' => '2016-06-19 00:00:00',
    'SHOW_TIME' => '2016-06-19 00:00:00',
    'CAN_VOTE' => true
);
?>