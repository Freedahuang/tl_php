var Remove = {
    handleException : function(){
		$("#msg").attr("innerHTML", "通讯错误，请稍候再试～");
    },

    updateUI : function(msg, id_book){
        //alert(id_book);
        if (msg == 'ok')
        {
            $("a[title='lend-remove'][alt='"+id_book+"']").parent().remove();
			if (id_book == 0)
			{
				$("a[title='lend-remove']").parent().remove();
			}
        }

	},
	
	execute : function(toUrl, id_book){

		$.ajax({
			type: 'POST',
			url: toUrl,
			async: true,
			cache: false,
			dataType : "text",
			data: "id_book="+id_book,

			success: function(textData)	{Remove.updateUI(textData, id_book)},
			error: function() {Remove.handleException();}
		});
	}

}

var Lend = {

    handleException : function(){
		$("#msg").attr("innerHTML", "通讯错误，请稍候再试～");
    },

    updateUI : function(msg){
        //alert(id_book);
        $("#lend-list").empty();
        $(msg.items).each(function(i){
            var tmp = $("#lend-list").attr("innerHTML");
            var val = "<li><a href=\"javascript:;\" title=\"lend-remove\" onclick=\"Remove.execute(remove_url, '"+msg.items[i].id+"')\" alt=\""+msg.items[i].id+"\" class=\"tip\">&#935;</a>&nbsp;<label><a href=\""+msg.items[i].url+"\">"+msg.items[i].name+"</a></label>&nbsp;"+msg.items[i].quantity+"/"+msg.items[i].lended+"</li>";
            $("#lend-list").attr("innerHTML", tmp+val);
        });
		//$("#qty-"+id_book).attr("innerHTML", msg);
	},
	
	execute : function(toUrl, id_book){

		$.ajax({
			type: 'POST',
			url: toUrl,
			async: true,
			cache: false,
			dataType : "json",
			data: "id_book="+id_book,

			success: function(jsonData)	{Lend.updateUI(jsonData)},
			error: function() {Lend.handleException();}
		});
	}

}

var User = {
    
    current_mobile : "",

    handleException : function(){
		$("#msg").attr("innerHTML", "通讯错误，请稍候再试～");
    },

    updateUI : function(msg){
        //alert(id_book);
        var val = "无此会员资料,号码可用";
		$("#id_user").val('0');

        if (msg.id)
        {
            val = msg.name+',能借'+msg.lend+'本,已借'+msg.lended+'本'+(msg.confirm>0?',需要认证':'')+(msg.active>0?'':',过期');
			if ($("input[alt='index']").val())
			{
				$("input[alt='index']").hide();
				val = '<a href="javascript:;" onclick="$(\'#mobile\').show();">重新认证</a>&nbsp;&nbsp;'+val;
			}
			$("#id_user").val(msg.id);
        }
        $("#user-info").attr("innerHTML", val);
	},
	
	execute : function(toUrl, mobile){
		$("#msg").attr("innerHTML", "");

        if (mobile.length == 11 && User.current_moblie != mobile)
        {
            User.current_moblie = mobile;
		$.ajax({
			type: 'POST',
			url: toUrl,
			async: true,
			cache: false,
			dataType : "json",
			data: "mobile="+mobile,

			success: function(jsonData)	{User.updateUI(jsonData)},
			error: function() {User.handleException();}
		});
        }

	}

}
