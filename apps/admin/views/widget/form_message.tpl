{if $message}
{lang txt='operation ok'}
{else}
{lang txt='operation failed'}
{/if}
<br />
<input id="close" type="button" name="close" value="{lang txt='close'}" class="button" 
    onclick="self.parent.tb_remove();self.parent.location.reload();"/>
{if $id == 0}
{* 如果有自定义的按钮 只加载自定义的 *}
{* 判断加载自定义的表单元素 *}
{tpl_exists tpl=$form.tpl|replace:"-":"/"|cat:"/form_message.tpl" assign='exists'}

{if $exists}
    {include file=$exists}
{else}
{* 否则加载通用的 *}
<!--
<input type="button" name="button" value="{lang txt='add more'}" class="button" 
    onclick="document.location.href='{rewrite_link controller=$controller_uri action=$action_uri}'"/>
//-->
{/if}

{/if}

<script type="text/javascript">
<!--

    {literal}
    $(document).ready(function(){
        t = setTimeout(function() {
                self.parent.tb_remove();self.parent.location.reload();
                clearTimeout(t);
            }, 1000);
    });
    {/literal}
-->
</script>