
{if $item.active}
<li {if $item.link == "" and $item.sort > 100}class="open"{/if}>
    {if $item.link}
        <a href="{rewrite_link link=$item.link}" target="main">{$item.name}</a>
    {else}<span>{$item.name}</span>{/if}

    {if $item.sub}
    <ul>
    {foreach from=$item.sub item=menu}
        {include file="menu/index-sub.tpl" item=$menu}
    {/foreach}
    </ul>
    {/if}

</li>
{/if}

