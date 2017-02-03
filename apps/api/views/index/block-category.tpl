
{foreach from=$category item=item1}
<div class="big-block">
    <h2><span><a class="no_underline">{$item1.name}</a></span></h2>
    <ul>
    {foreach from=$item1.sub item=item2}
        <li><label><a href="{rewrite_link controller='message' action='list' category=$item2.pinyin}">{$item2.name}</a></label><br />
        <span>
        {foreach from=$item2.sub item=item3}
        <a href="{rewrite_link controller='message' action='list' category=$item3.pinyin}">{$item3.name}<label class="count">({$item3.count})</label></a>
        {/foreach}
        </span>
        </li>
    {/foreach}
    </ul>
</div>
{/foreach}
