<div id="left">
    {* 如果不存在限制列表则不进行检查 否则进行权限检查 *}
    {if !$items || $items|has:'ad'}
    <div>
    <a href="{$sidebar_1.link}"><img src="{$upload_uri}ad/{$sidebar_1.img}" class="ad"></a>
    </div>
    {/if}

    {if !$items || $items|has:'search'}
    <div id="search">
    <input type="text" name="search" class="input" value="{if $keyword}{$keyword}{else}靴子{/if}" onclick="this.select();"
        title="请输入产品名称关键字，或者产品编码！">
    <a href="javascript:search();" title="查询"><img src="{$theme_uri}images/icon/search.gif"></a>
    </div>
    {/if}

    {if !$items || $items|has:'category'}
    <div id="category">
    <dl>
        <dt>产品类别</dt>
    </dl>
        {include file="index/common/category-tree.tpl" items=$category_list}
    </div>
    {/if}

    {* 显示文章类别 *}
    {if $items|has:'article_category'}
    <div id="category">
    <dl>
        <dt>文章类别</dt>
    </dl>
        {include file="index/common/category-tree.tpl" items=$article_category_list}
    </div>
    {/if}

    {if $items|has:'history'}
    <div>
    <dl>
        <dt>最近浏览的商品</dt>
        {foreach from=$history key=key item=item}
        <dd><a href="{rewrite_link controller='show' action='detail' option='product' id=$key}">
        {$item|truncate:24:''}</a></dd>
        {/foreach}
    </dl>
    </div>
    {/if}

    {* 判断加载自定义的表单元素 *}
    {if $items|has:'custom'}
    {tpl_exists tpl=$current_controller|parse_dash|cat:"/left.tpl" assign='exists'}
    {if $exists}{include file=$exists}{/if}
    {/if}

    <script type="text/javascript">

    var uri = "{rewrite_link controller='show' action='product' option='search'}";
    {literal}
    function search() {
        document.location.href = uri+"?&keyword="+escape($("input[name='search']").val());
    }

    $(function(){
        $("#navigation>li[class='level0']>ul").each(function(){
            $(this).hide();
        });

        $("#navigation>li[class='level0 show']").each(function(){
            $(this).show();
        });

        $("input[name='search']").ToolTipDemo();
    });
    </script>
    {/literal}

</div>