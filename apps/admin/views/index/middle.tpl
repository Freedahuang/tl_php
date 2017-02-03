{include file="global/header.tpl"}

<table title="关闭/展开菜单" height="100%" cellspacing="0" cellpadding="0" width="19" border="0">

    <tr>
        <td onClick="javascript:toggleMenu();" align="middle" background="{$assets_uri}/css/mid_cell.gif" height="5">
        <img height="2" src="{$assets_uri}/css/mid_top.gif" width="18"></td>
    </tr>
    <tr>
        <td align="middle" background="{$assets_uri}/css/mid_cell.gif">&nbsp; </td>
    </tr>
    <tr>

        <td onClick="javascript:toggleMenu();" align="middle" background="{$assets_uri}/css/mid_cell.gif" height="100%">
        <img src="{$assets_uri}/images/arrow_left.gif" name="arrow" id="arrow" style="margin-left:3px;"/></td>
    </tr>
</table>

<script language="JavaScript">
<!--

var arrow_right = "{$assets_uri}/images/arrow_right.gif";
var arrow_left = "{$assets_uri}/images/arrow_left.gif";

{literal}

function toggleMenu()
{
    var obj = window.top.document.getElementById('forum');
    if(obj.cols != "0,18,*"){
        obj.cols = "0,18,*";
        $('#arrow').attr("src", arrow_right);
    }else{
        obj.cols = "160,18,*";
        $('#arrow').attr("src", arrow_left);
    }
}
//-->
</script>
{/literal}
{include file="global/footer.tpl"}