<input type="button" name="button" value='{lang txt="send notification"}' class="button" id="send"/>
<select id="status_selected" name="status_selected">
    {include file="widget/opt-tree.tpl" items=$message_status selected=$status_selected}
</select>
<script language="JavaScript">
<!--

var uri = "{rewrite_link controller=$controller_uri action=$action_uri}";
var uri_send = "{rewrite_link controller=$controller_uri action=send}";

{literal}

$(document).ready(function(){
    $("#status_selected").change(function(){
        location.href = uri + "?&status_selected="+$(this).val();
    });
    $("#send").click(function(){
        location.href = uri_send;
    });    
});

//-->
</script>
{/literal}
