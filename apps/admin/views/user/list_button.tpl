{include file="widget/list_button.tpl"}
{include file="global/list_search.tpl"}
<input type="button" name="button" value='{lang txt="add to message sending list"}' class="button" id="add2sendingList"/>
<span id="add2tip"></span>
<script type="text/javascript" src="{$assets_uri}/js/jquery.cookie.js"></script>
<script language="JavaScript">
<!--

    var uri = "{rewrite_link controller=$controller_uri action=$action_uri}";

{literal}

    function toggle(type)
    {
        $("input[type='checkbox'][name^='"+type+"']").each(function(){
            if (this.checked)
                this.checked = false;
            else
                this.checked = true;
        });
    }
    $(document).ready(function(){
        $("#add2sendingList").click(function(){
        	var key = 'sendingList';
			var list = $.cookie(key);
            if (list != undefined) {
            	list = JSON.parse(list);
            } else {
            	list = {};
            }
            $("input[type='checkbox'][name^='multi_select']").each(function(){
                if (this.checked){
                    var item = $(this).val().split('=');
                    list[item[0]] = item[1];
                }
            });
            if (list != {}){
            	$.cookie(key, JSON.stringify(list), {expires: 7, path: '/'});
            	$('#add2tip').html('&#8730;');
            	setTimeout(function(){
            		$('#add2tip').html('');
            	}, 3000);
            }
        });
    });

//-->
</script>
{/literal}