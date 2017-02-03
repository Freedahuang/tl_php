<li>
<input type="checkbox" name="{$id}[]" value="{$item.path}" {if $necessarily|has:$item.path}checked{/if} />
{$item.name}
    {if $item.brief}[{$item.brief}]{/if}
    {if $item.sub|@count > 0}
    <ul>
    {foreach from=$item.sub item=item}
        {include file="widget/form_tree_sub.tpl" items=$item}
    {/foreach}
    </ul>
    {/if}
</li>
