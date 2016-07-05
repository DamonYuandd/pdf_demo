var data = new Data();

$(function(){

	$("#rowpage").val(data.rowpage);
	
	//全选/返选
	$("#chk_all").click(function(){
	     $("input[name='ids[]']").attr("checked",this.checked);
	});

	//排序
	$("#on_ordernum").click(function(){
		if(window.tipCheckbox()) {
			$("#form_list").attr('action',data._APP_+'/Admin/'+data.actionName+'/ordernum/cid/'+data.get_cid);
			$("#form_list").submit();
		}
	});

	//移动
	$("#on_move").click(function(){
		if(window.tipCheckbox()) {
			$('#category_lang').show();
			$('#category_button').val('移动');
			$("#form_list").attr('action',data._APP_+'/Admin/'+data.actionName+'/move');
			window.openCategoryBox();
		}
	});

	//复制
	$("#on_copy").click(function(){
		if(window.tipCheckbox()) {
			$('#category_lang').show();
			$('#category_button').val('复制');
			$("#form_list").attr('action',data._APP_+'/Admin/'+data.actionName+'/copy');
			window.openCategoryBox();
		}
	});
	
	//选择语言改变分类
	$('#category_lang').change(function(){
		$lang = $(this).val();
		window._selectCategoryByPid('one_category_id',data.c_root,$lang);
		$('#two_category_id').html('');
		$('#three_category_id').html('');
		$('#two_category_id').hide();
		$('#three_category_id').hide();
	});

	//移动和复制按钮,检查分类有没有选择
	$('#category_button').click(function(){
		var lang = $('#category_lang').val();
		$("#form_list").attr('action',$("#form_list").attr('action')+'/lang/'+lang);
	});

	//删除
	$("#on_delete").click(function(){
		if(window.tipCheckbox()) {
			if(confirm('确认删除勾选的数据吗？删除后无法恢复！')) {
				$("#form_list").attr('action',data._APP_+'/Admin/'+data.actionName+'/delete/cid/'+data.get_cid);
				$("#form_list").submit();
			} else {
				return false;
			}
		}
	});

	//搜索
	$("#search_button").click(function(){
		var searchKey = $('#searchKey').val();
		if(searchKey == '请输入关键字' || jQuery.trim(searchKey)=='') {
			alert('请输入搜索关键字！');
			return false;
		}
		window.location.href = window.getSearchUrl();
	});

	//每页显示行数
	$("#rowpage").change(function(){
		window.location.href = window.getSearchUrl();
		
	});

	//发布状态
	$("input[name=is_publish]").click(function(){
		window.updateField('isPublish',$(this).attr('value'), this.checked);
	});

	//首页状态
	$("input[name=is_home]").click(function(){
		window.updateField('isHome',$(this).attr('value'), this.checked);
	});

	//置顶状态
	$("input[name=is_top]").click(function(){
		window.updateField('isTop',$(this).attr('value'), this.checked);
	});
	
	//表格变色
	$('.grid-table tbody tr').hover(
	  function () {
	    $(this).css('background-color','#FCF4DA');
	  }, 
	  function () {
		  $(this).css('background-color','');
	  }
	);
	
	$('body').append('<div id="opt_msg"></div>');
});

//更新单个字段
function updateField(action,id, fval) {
	var url =data._APP_+'/Admin/'+data.actionName+'/'+action+'/id/'+id+'/fval/'+fval+'/cid/'+data.get_cid;
	var mes = null,opt_msg = null;
	$.get(url,{},function(bool){
		if( bool==1 ) {
			opt_msg = $("#opt_msg").css("background-color","#319E01").text('状态已改变');
		} else if( bool == 'no-access') {
			opt_msg = $("#opt_msg").css('background-color','#D50D0D').text('没有权限');
		}else {
			opt_msg = $("#opt_msg").css('background-color','#D50D0D').text('状态未改变');
		}
		opt_msg.show().animate({left: '+=50%'}, 100).delay(2000).animate({left: '-=50%'}, 100).fadeOut(100);
	});
}

//返回搜索URL
function getSearchUrl() {
	return data._APP_+'/Admin/'+data.actionName+'/index/rowpage/'+$('#rowpage').val()+'/searchKey/'+$('#searchKey').val()+'/cid/'+data.get_cid;
}

//搜索获取蕉点清空文字
function inputText(_this,defText) {
	if(_this.value == defText){
		_this.value = '';
		return;
	}
	if(_this.value == '') {
		_this.value = defText;
	}
}

//移动或复制分类选择
function openCategoryBox(){
	$('#category_box').toggle();
	$('#two_category_id').html('');
	$('#three_category_id').html('');
	$('#two_category_id').hide();
	$('#three_category_id').hide();
	window._selectCategoryByPid('one_category_id',data.c_root,'');
}

//分类组装下拉列表<option>
function _selectCategoryByPid(element_id, $id, $lang ) {
	if($id=='pc' || $id=='mobile') {
		return;
	}
	$.getJSON(data._APP_+"/Admin/Index/selectCategoryByPid",{"pid":$id,"lang":$lang},function(json){
		$('#'+element_id).html('');
		if( json.list==null ) {
			$('#'+element_id).hide();
		} else {
			$('#'+element_id).show();
		}
		if(element_id=='one_category_id') {
			$('#one_category_id').append('<option value="pc" selected="">电脑分类顶级分类</option><option value="mobile" selected="">手机分类顶级分类</option>');
		} else {
			$('#'+element_id).append('<option value="-1" selected>请选择</option>');
		}
		$(json.list).each(function(i,obj){
			var str_tr = '<option value="'+obj.id+'">'+obj.title+'</option>';
			$('#'+element_id).append(str_tr);
		});
	});
}

//返回最后选择的分类ID
function _getLastSelectedCategoryId() {
	var three_category_id = $('#three_category_id').val();
	var two_category_id = $('#two_category_id').val();
	var one_category_id = $('#one_category_id').val();
	if( three_category_id ) {
		return three_category_id;
	}
	if( two_category_id ) {
		return two_category_id;
	}
	if( one_category_id ) {
		return one_category_id;
	}
}

//批量操作未选择checkbox提示
function tipCheckbox() {
	var n = $("input[name='ids[]']:checked").length;
	if( n==0 ) {
		alert('还没有勾选数据呢！');
		return false;
	} else {
		return true;
	}
}

//分类下拉联动---------------------------------------------------------------
function changeCategory(_this,target_id){
	if( target_id=='two_category_id' ) {
		$('#three_category_id').html('');
		$('#three_category_id').append('<option value="" selected>请选择</option>');
	}
	var id = $(_this).val();
	var lang = $('#category_lang').val();
	window._selectCategoryByPid(target_id, id, lang);
//	$.getJSON(data._APP_+"/Admin/Index/selectCategoryByPid",{"pid":id},function(json){
//		$('#'+target_id).html('');
//		$('#'+target_id).append('<option value="" selected>请选择</option>');
//		$(json.list).each(function(i,obj){
//			var str_tr = '<option value="'+obj.id+'">'+obj.title+'</option>';
//			$('#'+target_id).append(str_tr);
//		});
//	});
}

//删除数据
function deleteData(url) {
	if(confirm('确认删除数据吗？删除后无法恢复！')) {
		window.location.href = url;
	} else {
		return false;
	}
}

//手机同步
function synchMobile(id) {
	window.location.href=data._APP_+'/Admin/'+data.actionName+'/synchMobile/id/'+id;
}
