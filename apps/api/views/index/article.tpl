{include file="global/header.top.nav.tpl"}

<div id="main-block">
<span class="fLeft">

<div class="big-block">
    <h2><span><a class="no_underline">{if $selected.name}{$selected.name}{else}知识列表{/if}</a></span></h2>
    <ul class="pic-message-list">
        {foreach from=$list item=item}
        <li><span><label><a href="{rewrite_link controller='show' action='article' id=$item.id}">
            {$item.name}</a></label></span>
        </li>
        {/foreach}
        <li><span><label>
            {if $page > 1}
            <a href="javascript:goPage(1);">第一页</a>
            <a href="javascript:history.go(-1);">上一页</a> 
            {/if}
            {if $limit == $list|@count}
            <a href="javascript:goPage({$page+1})">下一页</a>
            {/if}
        </label></span></li>
    </ul>
</div>
</span>

<span class="fRight">
<div class="side-block">
    <h2><span><a class="no_underline">知识类别</a></span></h2>
    <ul>
        {foreach from=$category item=item}
        <li><h4><a href="{rewrite_link controller=$controller_uri action=$action_uri category=$item.id}">{$item.name}</a></h4></li>
        {/foreach}
    </ul>
</div>
{if $ad_index_side_bar}
<div class="side-block">
    <ul>
        <li>
            {$ad_index_side_bar}
        </li>
    </ul>
</div>
{/if}

</span>

</div>


{include file="global/footer.tpl"}
