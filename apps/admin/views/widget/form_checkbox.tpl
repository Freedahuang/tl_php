<p>
<label>{lang txt=$label}:</label><br />
<ul>
{foreach from=$val item=item}
<li><input type="checkbox" name="_{$id}" value="{$item.id}" {if $necessarily|has:$item.id}checked{/if} style="margin-top:2px"/>
{$item.name}
</li>
{/foreach}
</ul>
<input type="hidden" id="{$id}" name="{$id}" value="">
</p>

<script language="JavaScript">
<!--
function updateCheckbox{$id}()
{ldelim}
    var _obj = $("input[type='checkbox'][name='_{$id}']:checked")
    var _{$id} = "";
    $.each(_obj, function(i, n){ldelim}
        _{$id} += $(n).val();
        if (i < _obj.length-1) {ldelim}
            _{$id} += ",";
        {rdelim}
    {rdelim});
    $("#{$id}").val(_{$id});
{rdelim}
$(document).ready(function(){ldelim}
    updateCheckbox{$id}();
    $("input[type='checkbox'][name='_{$id}']").click(function(){ldelim}
        updateCheckbox{$id}();
    {rdelim});
{rdelim});
//-->
</script>