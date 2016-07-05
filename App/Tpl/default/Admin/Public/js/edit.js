var data = new Data();

$(function(){

	if(data.get_id=='') {
		$("input[name=is_publish]").attr("checked",true);
	}

	//自动选择当前分类
	var levels = data.levels.split("|");
	var cid = data.category_id;
	if( cid=='' ) {cid = data.get_cid;}

	if( levels.length==2 ) {
		$('#one_category_id').val(cid);
	} else {
		$('#one_category_id').val(levels[2]);
	}

	if(levels[3]==undefined){
		$('#two_category_id').val(cid);
	} else {
		$('#two_category_id').val(levels[3]);
		$('#three_category_id').val(cid);
	}
	
	//再次检查当前最后分类有没有下级分类并再次读取
	if($('#three_category_id').val()=='-1') {
		if($('#two_category_id').val()!='-1') {
			window.changeCategory($('#two_category_id'), 'three_category_id', data.get_lang);
		} else {
			if($('#one_category_id').val()!='-1') {
				window.changeCategory($('#one_category_id'), 'two_category_id', data.get_lang);
			}
		}
	}
	var checkLang = data.lang?data.lang:data.current_lang;
	
	$("input[name=lang][value="+data.get_lang+"]").attr("checked",true);
	$("input[name=mode][value="+data.get_lang+"]").attr("checked", true);
	$("input[name=mode][value="+data.get_lang+"]").attr('disabled',false);
	$("input[name=is_comment][value="+data.is_comment+"]").attr("checked",true);
	$("input[name=is_publish][value="+data.is_publish+"]").attr("checked",true);


	//切换语言,下拉分类改变对应语言
	$("input[name=lang]").click(function(){
		var lang = $(this).val();
		//回调方法其它处理
		if(typeof(langCallBeforeFunction) == "function") {
			if(window.langCallBeforeFunction(lang)==true) {
				return;
			}
		}
		$.getJSON(data._APP_+"/Admin/Index/selectCategoryByPid",{"pid":data.c_root,"lang":lang},function(json){
			//回调方法其它处理
			if(typeof(langCallBackFunction) == "function") {
				window.langCallBackFunction(lang);
			}
			//分类显示处理
			$('#one_category_id').html('');
			$('#two_category_id').html('');
			$('#three_category_id').html('');
			$('#two_category_id').hide();
			$('#three_category_id').hide();
			$('#li_is_comment').hide();
			$('#one_category_id').append('<option value="-1" selected>请选择</option>');
			if( json.list!=undefined ) {
				$(json.list).each(function(i,obj){
					var str_tr = '<option value="'+obj.id+'">'+obj.title+'</option>';
					$('#one_category_id').append(str_tr);
				});
			} else {
				//显示隐藏能否论评
				var id = $('#one_category_id').val();
				$.getJSON(data._APP_+"/Admin/Index/getCategory",{"id":id,"lang":$(this).val()},function(json){
					if(json.is_comment == '1') {
						$('#li_is_comment').show();
					} else {
						$('#li_is_comment').hide();
					}
				});
			}
		});
	});
	
	//展开表单元素
	$('#more_options').change(function() {
		if( $(this).is(':checked') ) {
			$('#more_options_box').show();
		} else {
			$('#more_options_box').hide();
		}
	});
	
	$('#more_seo').change(function() {
		if( $(this).is(':checked') ) {
			$('#more_seo_box').show();
		} else {
			$('#more_seo_box').hide();
		}
	});

	//有封面文章使用
	$('#delete_image').click(function(){
		if( confirm('确定要删除图片吗？') ) {
			$.get(data._APP_+'/Admin/'+data.actionName+'/deleteImage',{'id':data.get_id},function(bool){
				if( bool==1 ) {
					$('input[name=image]').val('');
					$('#span_image').css('display','none');
				}
			});
		}
	});
	
	//表单提交有分类需要选择提示
	$(':submit[name=save]').click(function(event){
		if($('#one_category_id').is(':visible')) {
			if($('#one_category_id').val()==-1) {
				alert('请选择分类！');
				$('#one_category_id').focus();  
				event.preventDefault();
			}
		}
	});
	
	$('#synch_mobile').click(function(){
		if( $(this).is(':checked') ) {
			$('#li_mobile_category').show();
		} else {
			$('#li_mobile_category').hide();
		}
	});
		
});

//分类下拉联动---------------------------------------------------------------
function changeCategory(_this,target_id,lang){
	var c_lang = '';
	if ( !lang ) {
		lang = $('#lang:checked').val();
		if ( !lang ) {
			lang = $('input[name=mode]:checked').val();
		}
	}
	if( lang=='mobile' ) {
		c_lang = 'mobile_';
	}
	if( target_id=='two_'+c_lang+'category_id' ) {
		$('#three_'+c_lang+'category_id').html('');
		$('#three_'+c_lang+'category_id').append('<option value="-1" selected>请选择</option>');
	}

	var id = $(_this).val();
	$.getJSON(data._APP_+"/Admin/Index/selectCategoryByPid",{"pid":id,"lang":lang},function(json){
		$('#'+target_id).html('');
		$('#'+target_id).append('<option value="-1" selected>请选择</option>');
		if (json.list != undefined) {
			$(json.list).each(function(i, obj){
				var str_tr = '<option value="' + obj.id + '">' + obj.title + '</option>';
				$('#' + target_id).append(str_tr);
			});
			$('#' + target_id).show();
		}
		else {
			$('#' + target_id).hide();
			
			//显示隐藏能否论评
			var id = $(_this).val();
			$.getJSON(data._APP_ + "/Admin/Index/getCategory", {
				"id": id,
				"lang": data.get_lang
			}, function(json){
				if (json != null && json.is_comment == '1') {
					$('#li_is_comment').show();
				}
				else {
					$('#li_is_comment').hide();
				}
			});
		}
	});
}


//分类下拉联动---------------------------------------------------------------
function changeMobileCategory(_this,target_id){
	if( target_id=='two_mobile_category_id' ) {
		$('#three_mobile_category_id').html('');
		$('#three_mobile_category_id').append('<option value="-1" selected>请选择手机分类</option>');
	}
	var id = $(_this).val();
	$.getJSON(data._APP_+"/Admin/Index/selectMobileCategoryByPid",{"pid":id},function(json){
		$('#'+target_id).html('');
		$('#'+target_id).append('<option value="-1" selected>请选择手机分类</option>');
		if (json.list != undefined) {
			$(json.list).each(function(i, obj){
				var str_tr = '<option value="' + obj.id + '">' + obj.title + '</option>';
				$('#' + target_id).append(str_tr);
			});
			$('#' + target_id).show();
		} else {
			$('#' + target_id).hide();
		}
	});
}