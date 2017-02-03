{include file="global/pair.header.thickbox.tpl"}

<form method=post id="upload_form"
    action="{rewrite_link controller=$controller_uri action=$action_uri option=$option no_thumb=$no_thumb submit=true token=$token}" 
    enctype="multipart/form-data" autocomplete="off">
<input id="upload_file" type="file" name="upload_file" value="" class="file input" /><br />

<span id="tip" class="editable tip">{if $message}{lang txt=$message}{/if}</span>
{if $image|is_pic}
<br /><img id="preview" src="{$image}" width='360' style="clear:both;">
{elseif $image}
<span id="preview" class="editable">{$image}</span>
{/if}
</form>

{literal}
<script type="text/javascript">  
<!--  
$(function(){  
    $("#upload_file").change(function(){
        $("#upload_form").submit();
        $("#tip").html('&nbsp;上传进行中 ...');
    });
      
});  
-->  
</script> 
{/literal}
{include file="global/pair.footer.thickbox.tpl"}


