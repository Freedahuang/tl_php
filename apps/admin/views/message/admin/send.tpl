{include file="widget/form_list.tpl" form=$form}
<script type="text/javascript" src="{$assets_uri}/js/jquery.cookie.js"></script>
<script language="JavaScript">
<!--

var uri = "{rewrite_link controller=$controller_uri action=$action_uri}";

{literal}

$(document).ready(function(){
	var key = 'sendingList';
	var list = $.cookie(key);
	if (list != undefined) {
		list = JSON.parse(list);
	} else {
		list = {};
	}
	var str = '';
	for (var idx in list) {
		str += idx+'='+list[idx]+'\n';
	}
	$('#user').val(str);
	$('input[type=submit]').click(function(){
		if ($('#empty').val() == 1) {
			$.removeCookie(key, {path: '/'});
		} else {
			var res = {};
			var str = $('#user').val().split('\n');
			for(var idx in str) {
				if (str[idx].indexOf('=') != -1) {
					var item = str[idx].split('=');
					res[item[0]] = item[1];
				}
			}
			if (res != {}) {
				$.cookie('sendingList', JSON.stringify(res), {expires: 7, path: '/'});
			}
		}
	});
});

//-->
</script>
{/literal}


