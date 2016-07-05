$(document).ready(function(){
		$("#bdshare").attr({'data': "{'url': document.referrer}"});
	})
	 var bds_config = { /* 使用者关注这个config配置即可 */
         "bdText": window.title,
         "bdPic": "http://"+ window.location.host +"/index.php/Index/erweima/hw/mobile?url="+document.referrer,
	  	// "bdPic": "https://chart.googleapis.com/chart?chs=180x180&cht=qr&chl="+document.referrer,
		}