
<div class="big-block">
    <h2><span><a class="no_underline">推荐商家</a></span></h2>
    <ul>
        <li>
        <span>
        {foreach from=$corp_index item=item}
        <a href="{$item.link}">{$item.name}</a>
        {/foreach}
        </span>
        </li>
    </ul>
</div>
