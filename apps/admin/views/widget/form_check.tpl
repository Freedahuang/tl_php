<script language="JavaScript">
<!--
    var message = "{lang txt='no self-selection'}";
{literal}
    function checkForm(current_id)
    {
        if (current_id > 0 && $('#parent_id').val() == current_id)
        {
            $("#parent_id+span[class='tip']").attr('innerHTML', message);
            return false;
        }
        return true;
    }
{/literal}
//-->
</script>