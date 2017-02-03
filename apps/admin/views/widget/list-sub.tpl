<li>
<a href="{rewrite_link controller=$controller_uri action='edit' id=$item.id thickbox=$thickbox}" 
    class='thickbox' title='编辑{$item.name}'>{$item.name}</a>
    {if $item.brief}[{$item.brief}]{/if}
    <span class="tip">({lang txt='id'}:{$item.id}/{lang txt='sort'}:{$item.sort})</span>
<a href="javascript:if (confirm('{lang txt='confirm'}{lang txt='toggle'}?')) {ldelim}location.href='{rewrite_link controller=$controller_uri action='list' submit=true option='toggle' id=$item.id token=$token}'{rdelim}" 
    title={if $item.active}"{lang txt='deactivate'}">&#8730;{else}"{lang txt='activate'}">&#935;{/if}</a>
    {if $item.sub|@count > 0}
    <ul>
    {foreach from=$item.sub item=item}
        {include file="widget/list-sub.tpl" items=$item}
    {/foreach}
    </ul>
    {/if}
</li>
