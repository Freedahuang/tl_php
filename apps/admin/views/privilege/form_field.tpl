
{foreach from=$auth_list key=controller item=controller_property}
    <p>
    <input type="button" name="button" onclick="toggle('{$controller}')" class="button" 
        value="选择/取消 {$controller_property.alias}">
    <input type="hidden" name="controllers[]" value="{$controller}">
    <p>
    {foreach from=$controller_property.actions key=action item=action_property}
        <input type="checkbox" name="{$controller}[]" value="{$action}" 
            {if isset($account_privilege[$controller]) && in_array($action, $account_privilege[$controller])}checked{/if}>
        {$action_property.name}</label><br />
    {/foreach}
    </p></p>
{/foreach}

{literal}
<script language="JavaScript">
<!--
    function toggle(type) {
        $("input[type='checkbox'][name^='"+type+"']").each(function(){
            if (this.checked)
                this.checked = false;
            else
                this.checked = true;
        });
    }
//-->
</script>
{/literal}


