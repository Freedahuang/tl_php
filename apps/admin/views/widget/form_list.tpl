{include file="global/pair.header.thickbox.tpl"}
{include file="global/toolbar.tpl"}

{include file="widget/form_header.tpl" id=$form.id val=$form.check}

{if $message}
    {lang txt='operation ok'}<br />
    <input type="button" id="close" name="close" value="{lang txt='back'}" class="button" 
        onclick="location.href='{rewrite_link controller=$controller_uri action='list'}'"/>
        {* as form list order by date_upd, return to list after edit *}

{else}
    {* 加载指定的表单元素 *}
    {foreach from=$form.fields item=field}
        {include file="widget/form_`$field.type`.tpl" label=$field.label id=$field.id val=$field.value necessarily=$field.required}
    {/foreach}

    {* 判断加载自定义的表单元素 *}
    {tpl_exists tpl='form_field.tpl' assign='exists'}
    {if $exists}{include file=$exists}{/if}

    <p>
    <input type="submit" name="submit" value="提交" class="button" />
    <input type="button" name="close" value="返回" class="button" onclick="history.go(-1);"/>
    </p>

    <p class="clear"/>
    {include file="widget/form_memo.tpl" val='order'}
{/if}
{include file="widget/form_footer.tpl"}

{include file="global/pair.footer.thickbox.tpl"}


