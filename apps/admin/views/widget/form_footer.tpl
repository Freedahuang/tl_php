</form>

<script language="JavaScript">
<!--

{literal}

$(document).ready(function(){
    $("input[name='add']").click(function(){
        var id = $(this).attr("alt");
        var num = Number.parseInt($("#"+id).val(), 10)+1;
        if (num < 100) {
            $("#"+id).val(num);
            if (typeof pickerAdd != 'undefined' && pickerAdd instanceof Function) {
               pickerAdd(num, id);
            }
        }
    });
    $("input[name='minus']").click(function(){
        var id = $(this).attr("alt");
        var num = Number.parseInt($("#"+id).val(), 10)-1;
        if (num >= 0) {
            $("#"+id).val(num);
            if (typeof pickerMinus != 'undefined' && pickerMinus instanceof Function) {
               pickerMinus(num, id);
            }
        }
    });

});

//-->
</script>
{/literal}