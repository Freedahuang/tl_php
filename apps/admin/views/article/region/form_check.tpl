<script language="JavaScript">
<!--
    var message = "{lang txt='no self-selection'}";
{literal}
    function checkForm(current_id)
    {
        var res = true;
        var name = tree;
        $("select[name^=name_]").each(function(i,n){
            if ($(n).val() != '') {
                name = name[$(n).val()];
            } else {
                res = false;
            }
        });

        if (!res) {
            alert('请完成项目选择');
        } else {
            $("#name").val(name);
        }
        return res;
    }
{/literal}
//-->
</script>