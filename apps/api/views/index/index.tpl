{include file="global/header.top.nav.tpl"}

<div id="main-block">
<span class="fLeft">
    <div class="big-block">
        <h2><span><a class="no_underline">图书查询</a></span></h2>
        <form class="inner" method=post action="{rewrite_link controller=$controller_uri action=$action_uri}">
            <p>
                <label>ISBN/书名</label>
                <input type="text" id="name" name="name" value="{$keyword}" class="input" />
                <input type="submit" name="submit" value="查询" class="button" />
                <a href="{rewrite_link controller='index' action='tags'}">标签浏览</a>
                <input type="hidden" name="token" value="{$token}"/>
            </p>
            <p>
                <span id="msg" class="tip"></span>
            </p>
        </form>

        <ul class="pic-message-list">
            {foreach from=$list item=item}
            <li><span><img src="{$item.image}" align="absbottom">
                <span><label><a href="{rewrite_link controller='show' action='article' id=$item.id}">{$item.name}</a><label><br/>
                {foreach from=$item.info item=info}
                    {if $info}{$info|truncate:30:""}/{/if}
                {/foreach}
                <br/>ISBN: {$item.isbn}&nbsp;&nbsp;
                数量: {$item.quantity}
                借出: {$item.lended}
                {if $item.quantity-$item.lended > 0}
                <input type="button" name="lend" alt="{$item.id}" title="lend" value="放入借阅单" class="button" />
                {/if}
                <br/></span></span>
            </li>
            {/foreach}
            {if !$keyword}{include file="global/page-li.tpl"}{/if}
        </ul>
    </div>
</span>

<span class="fRight">
{include file="index/block-right.tpl"}
</span>

</div>
<script language="JavaScript">
<!--
var quantity_url = "{rewrite_link controller='shop' action='quantity'}";
var lend_url = "{rewrite_link controller='lend' action='add' submit='1' token=$token}";
var remove_url = "{rewrite_link controller='lend' action='remove' submit='1' token=$token}";
var current_url = "{rewrite_link controller='shop' action='book'}";

{literal}

$(document).ready(function(){
    $("input[name='qty']").each(function(){
        var option = $(this).attr("title");
        var id_book = $(this).attr("alt");
        $(this).bind('click', function(){
            Quantity.execute(quantity_url, option, id_book);
        });
    });

    $("input[name='lend']").each(function(){
        var id_book = $(this).attr("alt");
        $(this).bind('click', function(){
            Lend.execute(lend_url, id_book);
        });
    });

    $("a[title='lend-remove']").each(function(){
        var id_book = $(this).attr("alt");
        $(this).bind('click', function(){
            Remove.execute(remove_url, id_book);
        });
    });

    $("#btn-stock").bind('click', function(){
        document.location.href = current_url+"option/stock/";
    });

});

var Quantity = {

    handleException : function(){
        $("#msg").attr("innerHTML", "通讯错误，请稍候再试～");
    },

    updateUI : function(msg, id_book){
        //alert(id_book);
        $("#qty-"+id_book).attr("innerHTML", msg);
    },
    
    execute : function(toUrl, option, id_book){

        $.ajax({
            type: 'POST',
            url: toUrl,
            async: true,
            cache: false,
            dataType : "text",
            data: "option="+option+"&id_book="+id_book,

            success: function(textData)    {Quantity.updateUI(textData, id_book)},
            error: function() {Quantity.handleException();}
        });
    }
}

//-->
</script>

{/literal}
{include file="global/footer.tpl"}
