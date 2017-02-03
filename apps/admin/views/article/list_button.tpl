{include file="widget/list_button.tpl"}
{include file="global/list_search.tpl"}
<select id="id_article_category" name="id_article_category">
    <option value="0">-- {lang txt="please select"} --</option>
    {include file="widget/opt-tree.tpl" items=$article_category selected=$id_article_category}
</select>
<script language="JavaScript">
<!--

    var uri = "{rewrite_link controller=$controller_uri action=$action_uri}";

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
    $(document).ready(function(){
        $("#id_article_category").change(function(){
            var result = '';
            $("input[type='checkbox'][name^='article_select']").each(function(){
                if (this.checked){
                    result += $(this).val()+',';
                }
            });
            if (result != "" && confirm("确定移动选中的文章至指定类别？")){
                document.location.href = uri[0] + "?&option=move&id_article_category="+$(this).val()+"&ids_article="+result;
            }else{
                document.location.href = uri[0] + "?&id_article_category="+$(this).val();
            }

        });
    });

//-->
</script>
{/literal}
