{assign var='tpl' value=$form.tpl|replace:"-":"/"|cat:"/form_check.tpl"}
{tpl_exists tpl=$tpl assign='exists'}

{if $exists}
    {include file=$tpl}
{else}
    <script language="JavaScript">
    <!--
        var message = "{lang txt='no self-selection'}";
        function checkForm(current_id)
        {ldelim}
        {if $val == 'check'}
            if (current_id > 0 && $('#id_parent').val() == current_id)
            {ldelim}
                $("#id_parent+span[class='tip']").attr('innerHTML', message);
                return false;
            {rdelim}
        {/if}
            return true;
        {rdelim}
    //-->
    </script>   
{/if}

{include file="global/single.treeview.tpl"}

{literal}
<script type="text/javascript">
$(document).ready(function(){
    // first example
    $("#navigation").treeview({
        persist: "location",
        collapsed: false,
        unique: false
    });
});
</script>
{/literal}

<form method="post"
    action="{rewrite_link controller=$controller_uri action=$action_uri id=$id option=$option token=$token}" 
    class="notab" onsubmit="return checkForm({$id});">