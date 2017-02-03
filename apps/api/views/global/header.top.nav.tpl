{include file="global/header.tpl"}

{if $ad_inner_top_banner}
<div id="top-logo" title="dooyv.com">
    <span class="ad_banner fRight">
        {$ad_inner_top_banner}
    </span>
</div>
{/if}

<div id="top-nav">
    <ul>

        <!-- 左部导航 -->
        <li><a href="/">> 返回首页</a></li>
        <!-- <li><a href="{rewrite_link controller='message' action='post'}" >发布信息</a></li> -->
        <!-- 左部导航 -->

        <!-- 右部按钮 -->
        <li class="fRight">
        {if $shop}
        <a href="javascript:;" class="no_underline">欢迎来到&nbsp;{$shop.name} 联系人:{$shop.owner} 电话:{$shop.phone} 地址:{$shop.addr}</a>
        {/if}
        {if $account->is_logined}<a href="{rewrite_link controller='auth' action='logout'}">退出</a>{/if}
        {if $user->is_logined}<a href="{rewrite_link controller='auth' action='logout' option='user'}">退出</a>{/if}
        </li>
        <!-- 右部按钮 -->

    </ul>
</div>
<script language="JavaScript">
<!--
    var uri_category = "{rewrite_link controller='message' action='list'}";
//-->
</script>
<script type="text/javascript" src="{$theme_uri}js/nav.js"></script>

<div id="main-wrap">

    <div id="body-wrap">