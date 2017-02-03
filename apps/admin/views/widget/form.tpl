{include file="global/pair.header.thickbox.tpl"}

{include file="widget/form_header.tpl" id=$form.id val=$form.check}

{if $message}
    {include file="widget/form_message.tpl" id=$form.id message=$message}
{else}
    {include file="widget/form_memo.tpl"}

    {* 判断加载自定义的表单元素 *}
    {tpl_exists tpl=$form.tpl|replace:"-":"/"|cat:"/form_field.tpl" assign='exists'}
    {if $exists}{include file=$exists}{/if}


    {* 加载指定的表单元素 *}
    {foreach from=$form.fields item=field}
        {include file="widget/form_`$field.type`.tpl" label=$field.label id=$field.id val=$field.value necessarily=$field.required}
    {/foreach}

    {if $form.submit != 'remove'}
        {include file="widget/form_submit.tpl"}
    {/if}
{/if}
{include file="widget/form_footer.tpl"}

{include file="global/pair.footer.thickbox.tpl"}


<script language="JavaScript">
<!--

{literal}


    function pre_toggle(target) 
    {
        if ($("#"+target).css('height') == "200px") {
            $("#"+target).css('height', 'auto');
        } else {
            $("#"+target).css('height', '200px');
        }
    }

//-->
</script>
{/literal}


