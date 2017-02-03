$(document).ready(function(){
	$('input[readonly]').focus(function(){
	    this.blur();
	});
	$(function() {
		 $('input, textarea').placeholder();
		});
	$("input,textarea").placeholder().each(function(i,o){
		if ($(o).attr("placeholder") !== undefined) {
			$(o).blur(function(){
				var flag = false;
				var msg = "选项不能为空";
				if ($(this).attr("title") == "Email") {
					var emailRegExp = new RegExp("[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?");
					if (!emailRegExp.test($(this).val())||$(this).val().indexOf('.')==-1)
					{
						flag = true;
						msg = "Email地址格式不对";
					} else if (typeof verifyEmail != 'undefined' && verifyEmail instanceof Function) {
					   verifyEmail($(this).val());
					}
				}
				if ($(this).attr("title") == "Password") {
					if ($(this).val().length < 6 || $(this).val().length > 16) {
						flag = true;
						msg = "密码长度不符要求";
					}
				}
				if ($(this).attr("title") == "Captcha") {
					if (typeof verifyCaptcha != 'undefined' && verifyCaptcha instanceof Function) {
					   verifyCaptcha($(this).val());
					}
				}
				
				if ((!$(this).attr("readonly") && $(this).val() == "") || flag) {
					$("#inputError").html(msg);
					$(this).addClass("error");
					$(this).focus();
				} else {
					$(this).removeClass("error");
					$("#inputError").html("");
				}
			});			
		}
	});	
	
	$("textarea[maxlength]").bind('input propertychange', function() {  
        var maxLength = $(this).attr('maxlength');  
        if ($(this).val().length > maxLength) {  
            $(this).val($(this).val().substring(0, maxLength));  
        }  
    });
    
    $("input[type='input'],textarea").click(function(){
    	$("#inputError").html("");
    });
    
	$("input[name^='check_']").each(function(i, o){
		$(o).click(function(){
			var type = $(this).attr("name").replace(/check_/, "id_option_");
			$("input[name='"+$(this).attr("name")+"']").val("");
			$("input[name='"+$(this).attr("name")+"']").css("font-weight", "normal");
			$(this).val($(this).attr("placeholder"));
			//alert(type);
			//alert($(this).attr("alt"));
			$("#"+type).val($(this).attr("alt"));
			$(this).css("font-weight", "bold");
		});
	});

});

function checkForm()
{
	var flag = true;
	$("input, textarea").placeholder().each(function(i,o){
		//alert($(o).val("readonly"));
		if (!$(o).attr("readonly") && $(o).val() == "" && $(o).attr("placeholder") !== undefined) {
			var tip = "选项";
			if ($(o).attr("title")) {
				tip = $(o).attr("title");
			}
			$("#inputError").html(tip+"不能为空");
			$(o).focus();
			flag = false;
			return false;
		}
	});
	
	$("input[id^=id_option_], textarea[id^=id_option_]").each(function(i, o){
		if ($(o).val() == ""){
			var tip = "项目";
			if ($(o).attr("title")) {
				tip = $(o).attr("title");
			}
			$("#inputError").html(tip+"不能为空");
			flag = false;
			return false;
			t=setTimeout(function(){
				$("#inputError").html("");
				window.clearTimeout(t);
			}, 3000);
		}
	});

	if (typeof beforeSubmit != 'undefined' && beforeSubmit instanceof Function) {
	   beforeSubmit();
	}
	
	if (flag && flagSubmit) {return true;}
	return false;
}

function ajaxInputError(url, msg)
{
	$("#inputError").html(msg);
	t=setTimeout(function(){
		$("#inputError").html("");
		window.clearTimeout(t);
	}, 3000);	
	$.ajax({
        url: url + "?&time="+ Math.round(Math.random()*10000),
        type: 'GET',
        data: null,
        cache: false,
        dataType: 'json',
        success:function(data){
	        if (data.status == 'ok') {
	        }
        },
        error:function(){
        	//$("#inputError").html("操作超时");
        }
    });		
}
