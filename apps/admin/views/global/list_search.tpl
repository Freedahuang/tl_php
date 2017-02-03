<input type="button" name="button" value='{lang txt="search"}...' class="button" onclick="search()"/>
<script language="JavaScript">
<!--

    var uri = "{rewrite_link controller=$controller_uri action=$action_uri}";

{literal}
    function search()
    {
        var q = window.prompt("请输入查询内容", "");
        if (q)
        {
            document.location.href += "?&q="+q; 
        }
    }

//-->
</script>
{/literal}

{if $q}
<label class="actiontip">当前查询的内容是 <span class="tip">{$q}</span>, 结果如下:</label>
{/if}