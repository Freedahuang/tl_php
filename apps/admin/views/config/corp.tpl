{* header.tab.tpl与footer.tab.tpl必须成对出现 *}
{include file="global/pair.header.tab.tpl"}


<ul class="indent">

<li><fieldset><legend>推荐商家设置</legend><div>
    <form method=post action="{rewrite_link controller=$controller_uri action=$action_uri option='corp_index' token=$token}">
    <p>
    <span class="tip">说明：每行一个商家，<a href="javascript:;" id="add_new">新增商家</a></span>
    </p>

    <!-- 样板 -->
    <div style="display:none;visibility:hidden;" id="template">
    <p>
    <label>名称:</label><input type="input" name="corp_name[]" value="" class="input"/>
    <label>链接:</label><input type="input" name="corp_link[]" value="" class="input"/>
    <label>时间:</label><input type="input" name="corp_day[]" value="" class="input"/>
    </p>
    </div>

    <p id="mail_list">
    <label>推荐商家:</label><br />
    {foreach from=$corp_index key=key item=val}
    <p>
    <label>名称:</label><input type="input" name="corp_name[]" value="{$val.name}" class="input"/>
    <label>链接:</label><input type="input" name="corp_link[]" value="{$val.link}" class="input"/>
    <label>时间:</label><input type="input" name="corp_day[]" value="{$val.day}" class="input"/>
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
