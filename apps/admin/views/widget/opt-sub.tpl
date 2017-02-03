

    <option value="{$item.id}" {if $item.id == $selected}selected{/if} alt="{$item.name}">
    {if $item.level}{str_repeat repeat='|　' str=$item.level}|-{/if}{$item.name|truncate:46}{if !$item.active}[x]{/if}</option>
        {if $item.sub|@count > 0}
            {foreach from=$item.sub item=item}
                {include file="widget/opt-sub.tpl" items=$item}
            {/foreach}
        {/if}
