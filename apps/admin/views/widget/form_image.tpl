<p>
<label>{lang txt=$label}:</label><br />
{foreach from=$val item=item}
    {if $item}
    <span>
    <img name="preview" src="{$item}" width="75px" /><a href="javascript:;" onclick="$(this).parent().remove();">删除</a>
    </span>
    {/if}
{/foreach}
{include file="widget/form_hidden.tpl" id="image" val=""}
</p>