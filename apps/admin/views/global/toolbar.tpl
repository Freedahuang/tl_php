<link href="{$assets_uri}/css/header_menu.css" rel="stylesheet" type="text/css" media="all" />
<div id="menu_header">
<ul id="nav">
    <li class="top"><a href="{rewrite_link controller='index' action='welcome'}" class="top_link">
        <span>返回首页</span></a></li>
    {foreach from=$top_nav.sub item=v}
    {if $v.active && $v.link}
    <li class="top"><a href="{rewrite_link link=$v.link}" class="top_link">
        <span class="down">{$v.name}</span></a></li>
    {/if}
    {/foreach}
    <li class="top"><a href="{rewrite_link controller='auth' action='logout'}" id="privacy" class="top_link" target="_top">
        <span>退出系统</span></a></li>
</ul>
</div>