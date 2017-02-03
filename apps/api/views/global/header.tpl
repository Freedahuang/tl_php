<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh">
    <head>
        <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
        <title>{$webtitle} - {$site_name}</title>
        <meta name="keywords" content="闲置物品,二手市场,跳蚤市场,分类信息,{$site_name}" />    
        <meta name="description" content="{$webtitle} - {$site_name}，您的生活助手。为您提供二手物品交易、生活服务信息发布平台。无需注册，免费发布查询，请登陆{$host_name}" />
        <link href="{$theme_uri}css/global.css" rel="stylesheet" type="text/css" media="all" />
        <script type="text/javascript" src="{$base_uri}public/js/jquery.js"></script>
        <link href="{$base_uri}public/css/thickbox.css" rel="stylesheet" type="text/css" media="all" />
        <script type="text/javascript">
        // <![CDATA[
            var tb_pathToImage = 'https://cdn.jsdelivr.net/thickbox/3.1/loadingAnimation.gif';
            var regx_page = /(\?&page)[\/|=][\d]+$/g;
            {if $action_uri == 'post'}
            var uri_region = "{rewrite_link controller='index' action='region' option=$action_uri domain=$host_name thickbox=$thickbox}";
            {else}
            var uri_region = "{rewrite_link controller='index' action='region' option=$action_uri thickbox=$thickbox}";
            {/if}

            function goPage(page)
            {ldelim}
                var uri = document.location.href.replace(regx_page, "");
                document.location.href = uri + "?&page=" + page; 
            {rdelim}
        //]]>
        </script>
        <script type="text/javascript" src="{$base_uri}public/js/jquery.thickbox.js"></script>
        <script type="text/javascript" src="{$base_uri}public/js/tools.js"></script>
        <script type="text/javascript" src="{$upload_uri}region.js"></script>
        <script type="text/javascript" src="{$upload_uri}category.js"></script>

    </head>
<body>
<a name="top"></a>
