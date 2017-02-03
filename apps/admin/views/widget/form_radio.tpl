<p>
<label>{lang txt=$label}:</label><br />
<input type="radio" name="radio_{$id}" value="1" {if $val == 1}checked{/if}/>{lang txt='activate'}
<input type="radio" name="radio_{$id}" value="0" {if $val == 0}checked{/if}/>{lang txt='deactivate'}
<input type="hidden" id="{$id}" name="{$id}" value="{$val}">
</p>

<script type="text/javascript">
<!--
    $(document).ready(function(){ldelim}
        $("input[name='radio_{$id}']").click(function(){ldelim}
                $("#{$id}").val($(this).val());
                //alert($("#{$id}").val());
            {rdelim});

    {rdelim});
-->
</script>