{include file="global/header.tpl"}

<div id="main-wrap">
    <div id="top-logo" title="{$host_name}">
        <span class="ad_banner fRight">
            {$ad_index_top_banner}
        </span>
    </div>
    <div id="top-menu">
        <ul>
            <li {if $menu_tab == 'index-category'}class="selected"{/if}>
                <span><a href="/">首页分类</a></span></li>
            <!-- <li {if $menu_tab == 'category-Shichang'}class="selected"{/if}>
                <span><a href="{rewrite_link controller='index' action='category' select='Shichang'}"
                title="个人闲置/二手用品交易平台!">跳蚤市场</a></span></li> -->
            <!-- <li {if $menu_tab == 'category-Shenghuo'}class="selected"{/if}>
                <span><a href="{rewrite_link controller='index' action='category' select='Shenghuo'}"
                title="提供票务,家政,维修等生活服务信息!">生活资讯</a></span></li> -->
            <li><span><a href="{rewrite_link controller='show' action='tool'}"
                title="黄道吉日 婚丧嫁娶 尽在这里">今日运程</a></span></li>
            <li><span><a href="{rewrite_link controller='index' action='article'}"
                title="生活知识,防骗手册,居家生活必备!">知识宝库</a></span></li>
            <li {if $menu_tab == 'message-post'}class="selected"{/if}>
<!--                 <span><a href="{rewrite_link controller='message' action='post'}" -->                
                <span><a href="{rewrite_link controller='message' action='post'}"
                title="无需注册,免费发布,人工审核">发布信息</a></span></li>
            <li class="last"><span><a href="{rewrite_link controller='index' action='random'}"
                title="试试让系统随机为您选取一条信息!">手气不错</a></span></li>

            <li class="fRight"><a href="{rewrite_link controller='show' action='rss' area=$region1|cat:'-'|cat:$region2}" 
                title="订阅最近更新">RSS</a>
            <a>您所在的位置：<span id="region" alt="">
            {if $region1}{$region1}{elseif $region2} - {$region2}{else}未选择{/if}
            </span></a><a href="{rewrite_link controller='index' action='region' domain=$domain thickbox=$thickbox}" 
                title="更改位置" class="thickbox">?</a></li>
        </ul>
    </div>

    <div id="body-wrap">