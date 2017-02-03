{* header.tab.tpl与footer.tab.tpl必须成对出现 *}
{*include file="global/pair.header.notab.tpl"*}
{include file="global/pair.header.tab.tpl"}

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p class="indent"><strong>&nbsp;欢迎进入后台管理，请选择左侧菜单栏项目进行操作！</strong></p>


{include file="global/pair.footer.tab.tpl"}

<script language="JavaScript">
<!--

    var url_graph = "{rewrite_link controller='page' action='graph'}";
{literal}

    $(document).ready(function(){
        $("a[title='hourly_graph']").bind("click", function() {
            $(this).each(function(){
                $("a[title='hourly_graph']").removeClass("item_selected");
                });
            $(this).addClass("item_selected");
        });
    });
    
    function switchGraph(id_sp_service, id_province)
    {
        $("#stat_graph").attr("src", url_graph+"?id_sp_service="+id_sp_service+"&id_province="+id_province);   
    }
//-->
</script>
{/literal}
