{include file="global/header.tpl"}

{include file="global/single.treeview.tpl"}

{literal}
<style>
body{
    overflow:scroll;
    overflow-x:hidden;
}
</style>
<script type="text/javascript">
$(document).ready(function(){
    // first example
    $("#navigation").treeview({
        animated: "fast",
        persist: "location",
        collapsed: true,
        unique: false
    });
    $("span").bind("mouseover", function(){
        this.style.textDecoration="underline";
        this.style.cursor="pointer";
        });
    $("span").bind("mouseout", function(){
        this.style.textDecoration="none";
        });
    $("ul#navigation li").each(function(){
        var count = $(this).children("ul").children().length;
        var value = $(this).children("span").attr("innerHTML");
        if (count == 0 && value) {
            $(this).hide();
        }
    });
    
    $("ul#navigation li a").bind("click", function() {
        $("ul#navigation li a").each(function(){
            $(this).removeClass("item_selected");
            });
        $(this).addClass("item_selected");
    });
});
</script>

{/literal}
<div id="sysmenu">
<h2>欢迎您{if $account->alias},{$account->alias}{else},{$account->name}{/if}<br /><br /><br /></h2>

{include file="menu/index-tree.tpl" item=$menu}

</div>
{include file="global/footer.tpl"}