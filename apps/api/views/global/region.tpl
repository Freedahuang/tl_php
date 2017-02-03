<script type="text/javascript">
<!--
    {if !$region2 && $region1 != 'Beijing' && $region1 != 'Shanghai' && $region1 != 'Chongqing' && $region1 != 'Tianjin' && $region1 != 'Hongkong' && $region1 != 'Macao' && $region1 != 'Taiwan'}
        {assign var="region_switch_on" value=true}
    {/if}


    {if $action_uri != 'detail' && (!$region1 || $region_switch_on)}
    setTimeout('tb_show("位置选择", uri_region, null)', 3000);
    {else}
    update_region("{$region1}", "{$region2}");
    {/if}

//-->
</script>
