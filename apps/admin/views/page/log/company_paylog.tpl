{* header.tab.tpl与footer.tab.tpl必须成对出现 *}
{include file="global/header.tpl"}

{foreach from=$list item=item}
<p>
    于{$item.date_add}
    {$item.type_name}
    {$item.price_payed|string_format:"%5d"}RMB至
    {$item.date_type_expired}
    到期
</p>
{/foreach}

{if $list|count == 0}
暂无记录
{/if}

{include file="global/footer.tpl"}
