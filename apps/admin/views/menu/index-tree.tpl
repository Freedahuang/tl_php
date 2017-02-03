
<ul id="navigation">
    {foreach from=$menu item=item}
        {include file="menu/index-sub.tpl" item=$item}
    {/foreach}
    <li><a href="{rewrite_link controller='auth' action='logout'}" target="_top">退出系统</a></li>
</ul>
