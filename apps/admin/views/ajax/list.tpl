[
{if $list}{foreach from=$list item=items name="list"}
    {ldelim}
        {foreach from=$items key=k item=v name="item"}
        "{$k}":"{$v}"{if !$smarty.foreach.item.last},{/if}
        {/foreach}
    {rdelim}{if !$smarty.foreach.list.last},{/if}
{/foreach}{/if}
]