
<div class="side-block">
    <h2><span><a class="no_underline">会员认证</a></span></h2>
    <form class="inner" method=post action="{rewrite_link controller='auth' action='user'}">
        <p>
            <label>会员号/手机号:</label><br />
            <input type="text" id="name" name="name" value="" class="input" alt="index" />
        </p>
        <p>
            <label>密码:</label><br />
            <input type="password" id="password" name="password" class="input" />
        </p>
        <p>
            <label>验证码:</label><a href="javascript:resetCaptcha();" class="memo" title="看不清，换一张">看不清，换一张</a><br />
            <input type="text" name="captcha" value="" class="input short" tabindex=3>
            <img src="" id="captcha" align="absbottom">
        </p>
        <p>
            <input type="hidden" name="token" value="{$token}" />
            <input type="submit" name="submit" value="登陆" class="button" />
            <input type="reset" name="reset" value="重置" class="button"/>
            没有帐号? <a href="{rewrite_link controller='auth' action='reg'}">注册一个吧!</a>
        </p>
    </form>
</div>

<script language="JavaScript">
<!--

    function resetCaptcha()
    {ldelim}
        $("#captcha").attr("src", "{rewrite_link controller='page' action='captcha'}"+"?rand=" + Math.round(Math.random()*10000));
        $("#captcha").show();
    {rdelim}
    $(document).ready(function(){ldelim}
        //resetCaptcha();
        $("#captcha").hide();
        $("input[name='captcha']").bind("click", function(){ldelim}if($(this).val() == "")resetCaptcha();{rdelim});
    {rdelim});

//-->
</script>

<div class="side-block">
    <h2><span><a class="no_underline">借阅列表</a></span><label class="pic_tip">请致电预约</label></h2>
    <ul class="pic-message-list" id="lend-list">
<!--         <li><label>
            <a href="{rewrite_link controller='message' action='detail' category=$item.default_category id=$item.id}"
                title="{$item.name}">
            借阅管理</a></label><label class="pic_tip">图</label>
        </li> -->
        {foreach from=$lend_list item=item}
        <li><a href="javascript:;" title="lend-remove" alt="{$item.id}" class="tip">&#935;</a><label>
            <a href="{$item.url}" title="{$item.name}" target="_blank">
            {$item.name}</a>&nbsp;{$item.quantity}/{$item.lended}</label>
        </li>
        {/foreach}
    </ul>
</div>

