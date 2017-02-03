{include file="global/header.top.nav.tpl"}

<div id="main-block">
    <div class="big-block fullWidth">
        <h2><span><a class="no_underline">{$webtitle}</a></span></h2>
        <div id="content">
        <ul>
        {foreach from=$list key=key item=item}
            <li><strong>{$key}</strong><span class="tip">请选择离您所在区域最近的分部</span><br />
            <span>
            {foreach from=$item item=sub}
            <label><a href="http://{$sub.slug}.doyo.lc">{$sub.name}</a></label>
            {$sub.addr}<br/>
            {/foreach}
            </span>
            </li>
        {/foreach}
        </ul>
        </div>

    </div>

</div>


{include file="global/footer.tpl"}
