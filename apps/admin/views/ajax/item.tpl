
    {ldelim}
        {foreach from=$item key=k item=v name="item"}
        "{$k}":"{$v}"{if !$smarty.foreach.item.last},{/if}
        {/foreach}
    {rdelim}
