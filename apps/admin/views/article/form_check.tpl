<script language="JavaScript">
<!--
    var message = "{lang txt='no self-selection'}";
{literal}
    function checkForm(current_id)
    {
    	if ($("#id_article_category").val() == 0) {
    		alert("请选择类别");
    		$("#id_article_category").focus();
    		return false;
    	}
        return  true;
    }
{/literal}
//-->
</script>