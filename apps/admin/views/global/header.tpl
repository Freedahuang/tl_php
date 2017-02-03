<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh">
    <head>
        <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
        <title>{lang txt='administrator panel'}</title>
        <link href="{$assets_uri}/css/global.css" rel="stylesheet" type="text/css" media="all" />
        <link href="{$assets_uri}/css/thickbox.css" rel="stylesheet" type="text/css" media="all" />
        <link href="{$assets_uri}/js/jquery.bettertip/jquery.bettertip.css" rel="stylesheet" type="text/css" media="all" />
        <script type="text/javascript" src="{$assets_uri}/js/jquery.js"></script>
        <script type="text/javascript">
        // <![CDATA[

            function goPage(page)
            {ldelim}
                var uri = document.location.href.replace(/(&page)[\/|=][\d]+$/g, "");
                if (uri.indexOf("?") == -1) {ldelim}
                    uri += "?";
                {rdelim};
                document.location.href = uri + "&page=" + page; 
            {rdelim}
        //]]>
        </script>
        <script type="text/javascript" src="{$assets_uri}/js/jquery.thickbox.js"></script>
        <script type="text/javascript" src="{$assets_uri}/js/jquery.bettertip/jquery.bettertip.js"></script>
        <script type="text/javascript" src="{$assets_uri}/js/jquery.tooltip.js"></script>
        <link rel="stylesheet" type="text/css" media="all" href="{$assets_uri}/css/calendar.css"  />
        <script type="text/javascript" src="{$assets_uri}/js/jquery.dynDateTime.js"></script>
        <script type="text/javascript" src="{$assets_uri}/js/jquery.dynDateTime.zh.js"></script>
    </head>
<body>
