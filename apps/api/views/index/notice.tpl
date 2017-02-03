{include file="global/header.tpl"}

<div id="main-wrap">
<br /><br /><br />
    <div id="body-wrap">
        <div class="big-block fullWidth">
            <h2><span><a class="no_underline">操作提示</a></span></h2>
            <ul>
                <li>{if $message}{$message}{else}出错了！您访问的页面不存在，如有任何问题，请与<a href="mailto:{$support_mail}">管理员</a>联系。{/if}</li>
                <li>本页面将于5秒后自动跳转至<a href="{$back}" class="tip">{if $back == '/'}原始{else}{$back}{/if}</a>页面！</li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
<!--
    //setTimeout('document.location.href="{$back}"', 6000);
    setTimeout('history.go(-1)', 6000);
//-->
</script>
</body>
</html>