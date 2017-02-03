{* header.tab.tpl与footer.tab.tpl必须成对出现 *}
{include file="global/pair.header.tab.tpl"}

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

<a href="{rewrite_link controller=$controller_uri action='add' thickbox=$thickbox}" 
    title='{lang txt="add"} {lang txt="item"}' class="thickbox">
<input type="button" name="button" value='{lang txt="add"}...' class="button" /></a><br /><br />
{include file="widget/list-tree.tpl" items=$items}

{include file="global/pair.footer.tab.tpl"}
