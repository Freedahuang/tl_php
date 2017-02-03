{if $table.add != 'remove'}
<a href="{rewrite_link controller=$controller_uri action='add' thickbox=$thickbox}" 
    title='{lang txt="add"} {lang txt=`$table.type`}' {if $table.nopop == false}class="thickbox"{/if}>
<input type="button" name="button" value='{lang txt="add"}...' class="button" /></a>
{/if}
{if $table.production_update}
<input type="button" name="button-pd" value='{lang txt="production_update"}...' class="button" />
{/if}
<script language="JavaScript">
<!--
    var url_pd = "{rewrite_link controller=$controller_uri action='pd' submit=1 token=$token}";
{literal}
    function toggle(type)
    {
        $("input[type='checkbox'][name^='"+type+"']").each(function(){
            if (this.checked)
                this.checked = false;
            else
                this.checked = true;
        });
    }
    
    function getSelectedIds()
    {
        var result = '';
        $("input[type='checkbox'][name^='list_select']").each(function(){
            if (this.checked){
                result += $(this).val()+',';
            }
        });
        return result;
    }
    
    $(document).ready(function(){
        $("input[name='button-pd']").click(function(){
            if (confirm("确定执行此操作？")) {
                location.href = url_pd;
            }
        });
    });
{/literal}
//-->
</script>