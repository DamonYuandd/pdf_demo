$(function(){
	$('body').append('<div class="bottom-copyright bottom-fixed">欢迎使用网站后台</div>');
});


//URL跳转
function goToUrl( url ) {
	window.location.href = url;
}

function set_publish(){
	if($('#publish').attr('checked') == 'checked'){
			$('#is_publish').val('1');
	}else{
			$('#is_publish').val('0');
	}
	
}