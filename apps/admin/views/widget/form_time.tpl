
<p>
<label>{lang txt=$label}:</label><br />
<input type="text" id="{$id}" name="{$id}" value="{$val}" class="input" alt="date-time-{$id}" />
<span class="tip">
{if $necessarily}*{/if}
</span>
</p>

<script language="JavaScript">
<!--
    $("input[alt='date-time-{$id}']").dynDateTime({ldelim}
            showsTime: true,
            ifFormat: "%Y-%m-%d %H:%M:%S",
            daFormat: "%l;%M %p, %e %m,  %Y",
            align: "TM",
            electric: false,
            //singleClick: false,
            //displayArea: ".siblings('.dtcDisplayArea')",
            //button: ".next()" //next sibling
    {rdelim});
//-->
</script>