{* header.tab.tpl与footer.tab.tpl必须成对出现 *}
{include file="global/pair.header.tab.tpl"}


<ul class="indent">

<li><fieldset><legend>邮件帐号设置</legend><div>
    <form method=post action="{rewrite_link controller=$controller_uri action=$action_uri option='mail_server' token=$token}">
    <p>
    <span class="tip">说明：每行一个帐户，<a href="javascript:;" id="add_new">新增帐户</a></span>
    </p>

    <!-- 样板 -->
    <div style="display:none;visibility:hidden;" id="template">
    <p>
    <label>邮址:</label><input type="input" name="email[]" value="" class="input"/>
    <label>密码:</label><input type="password" name="pwd[]" value="" class="input"/>
    </p>
    </div>

    <p id="mail_list">
    <label>邮件帐号:</label><br />
    {foreach from=$mail_server key=key item=val}
    <p>
    <label>邮址:</label><input type="input" name="email[]" value="{$key}" class="input"/>
    <label>密码:</label><input type="password" name="pwd[]" value="{$val.pwd}" class="input"/>
    </p>
    {/foreach}

    </p>

    <p><input type="submit" name="submit" value="提交" class="button"></p>
</form></div></fieldset></li>


</ul>

{literal}
<script type="text/javascript">
<!--
    $("#add_new").click(function(){
        $("#mail_list").append($("#template").attr("innerHTML"));
    });
//-->
</script>
{/literal}

{include file="global/pair.footer.tab.tpl"}
