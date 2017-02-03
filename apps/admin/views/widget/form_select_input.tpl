<p>
<label>{lang txt=$label}:</label><br />
<select id="{$id}" name="{$id}">
    <option value="">-- {lang txt="add"}{lang txt="content"} --</option>
    {include file="widget/opt-tree.tpl" items=$val selected=$necessarily}
</select>
<span class="tip"></span>
</p>

<script language="JavaScript">
<!--
$(document).ready(function(){ldelim}
    $("#{$id}").change(function(){ldelim}
        $("#{$necessarily}").val($(this).val());
    {rdelim});
{rdelim});
//-->
</script>