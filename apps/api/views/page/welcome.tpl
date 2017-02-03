{* header.tab.tpl与footer.tab.tpl必须成对出现 *}
{include file="admin/global/pair.header.notab.tpl"}
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><strong>&nbsp;欢迎进入后台管理，请选择左侧菜单栏项目进行操作！</strong></p>
<p>&nbsp;</p>
<p>&nbsp;下载{ html_select_date prefix="Stat_Commisson_" year_extra="id='Stat_Commission_Year'" month_extra="id='Stat_Commission_Month'" start_year="-1" end_year="+1" month_format="%m" field_order="YM" }<a href="javascript:;" onclick="return getStatCommission();">收入报表</a></p>

{include file="admin/global/pair.footer.tab.tpl"}

<script language="JavaScript">
<!--
var stat_commission_url = "{rewrite_link controller='admin-stat' action='commission'}";


{literal}
    function getStatCommission()
    {
        document.location.href = stat_commission_url+"?year="+$("#Stat_Commission_Year").val()+"&month="+$("#Stat_Commission_Month").val();
        return true;
    }
//-->
</script>
{/literal}
