{include file="global/header.tpl"}

{literal}
<style>
#container {
    width: 520px;
    margin: 5em auto 1em auto;
    padding: 0;
}

#login{
    height: 353px;
{/literal}

    background: url('{$assets_uri}/images/login.jpg') no-repeat top center;

{literal}

    color: #646363;
    text-align: right;
}

#login form {
    float:right;
    width:245px;
    text-align:left;
    margin: 0;
    padding: 150px 30px 25px 30px;
}

#login #submit {
    float: left;
    margin: 0.5em 0px;
    padding: 0;
    text-align: left;
}

#login #submit input {
    padding: 2px 4px;
}

#login #lost {
    float: left;
    margin: 1.9em 0 0 0;
    font-size: 0.95em;
}

</style>
{/literal}
<div id="container">

    <div id="login">
        <form action="{rewrite_link controller='auth' action='login' token=$token}" 
            method="post">
            <label>账号:</label><br />
            <input type="text" id="name" name="name" value="" class="input" style="width:212px;" tabindex=1/>
            <div style="margin: 0.5em 0 0 0;">
                <label>密码:</label><br />
                <input type="password" name="password" class="input" style="width:212px;"  tabindex=2/>
            </div>
            <div style="margin: 0.5em 0 0 0;">
                <label>验证码:</label><br />
                <input type="text" name="captcha" value="" class="input short" style="width:102px;"  tabindex=3>
                <a href="javascript:resetCaptcha();"><img src="" id="captcha" align="absBottom" style="display:none;"></a>
                <input type="submit" name="submit" value="登陆" class="button"  tabindex=4/>
            </div>

            <div>
                <div id="submit">
                    
                    <span class="tip">{lang txt=$message}</span>
                </div>
            </div>
        </form>
    </div>

</div>
<script type="text/javascript">
<!--
    if (self != top) {ldelim}
        top.location.href = window.location.href;
    {rdelim}

    function resetCaptcha()
    {ldelim}
        $("#captcha").attr("src", "{rewrite_link controller='page' action='captcha'}"+"?rand=" + Math.round(Math.random()*10000));
        $("input[name='captcha']").focus();
    {rdelim}

    
    var message = "{$message}";
    var url_login = "{rewrite_link controller=$controller_uri action=$action_uri}";

    {literal}
    $(document).ready(function(){

    $("input[name='captcha']").focus(function(){
        if ($("#captcha").attr("src") == "") {
            resetCaptcha();
            $("#captcha").show();
        }
    });    
    
    });
    var t=setTimeout(function(){
        if (message) {
            window.location.href = url_login;
        }
        window.clearTimeout(t);
    }, 1000);
    {/literal}
-->
</script>

{include file="global/footer.tpl"}