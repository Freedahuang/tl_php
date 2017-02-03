<div class="side-block">
<ul><li>
<!-- SiteSearch Google -->
<form method="get" action="/message/search/" target="_top">
<table border="0" bgcolor="#ffffff">
<tr><td nowrap="nowrap" valign="top" align="left" height="32">
<a href="http://www.google.com/">
<img src="http://www.google.com/logos/Logo_25wht.gif" border="0" alt="Google" align="middle"></img></a>
<br/>
<input type="hidden" name="domains" value="{$host_name}"></input>
<label for="sbi" style="display: none">输入您的搜索字词</label>
<input type="text" name="q" size="21" maxlength="255" value="" id="sbi"></input>
<label for="sbb" style="display: none">提交搜索表单</label>
<input type="submit" name="sa" value="搜索" id="sbb"></input>
</td></tr>
<tr>
<td nowrap="nowrap">
<table>
<tr>
<td style='white-space: nowrap;'>
<input type="radio" name="sitesearch" value="" id="ss0"></input>
<label for="ss0" title="搜索网络"><font size="-1" color="#000000">互联网</font></label></td>
<td style='white-space: nowrap;'>
<input type="radio" name="sitesearch" checked value="{$host_name}" id="ss1"></input>
<label for="ss1" title="搜索 {$host_name}"><font size="-1" color="#000000">多余网</font></label></td>
</tr>
</table>
<input type="hidden" name="client" value="pub-5541481442397288"></input>
<input type="hidden" name="forid" value="1"></input>
<input type="hidden" name="ie" value="UTF-8"></input>
<input type="hidden" name="oe" value="UTF-8"></input>
<input type="hidden" name="safe" value="active"></input>
<input type="hidden" name="cof" value="GALT:#008000;GL:1;DIV:#FFFFFF;VLC:663399;AH:center;BGC:FFFFFF;LBGC:336699;ALC:0000FF;LC:0000FF;T:000000;GFNT:0000FF;GIMP:0000FF;LH:50;LW:138;L:http://{$host_name}/public/themes/default/images/top_logo.jpg;S:http://;FORID:11"></input>
<input type="hidden" name="hl" value="zh-CN"></input>
</td></tr></table>
</form>
<!-- SiteSearch Google -->
</li></ul>
</div>

<script type="text/javascript">
<!--
{literal}
    $(document).ready(function(){
        var insite = $("#region").attr("innerHTML");
        $("#sbi").val(insite+" ");
    });
{/literal}
//-->
</script>