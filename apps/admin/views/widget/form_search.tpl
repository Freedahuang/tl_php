{include file="pair.header.thickbox.tpl"}

{* 判断加载自定义的JS元素 *}
{tpl_exists tpl="`$form.type`/form_search.tpl" assign='exists'}
{if $exists}{include file=$exists}{/if}

<script language="JavaScript">
<!--

{literal}

// JS 对象 用于Ajax取相应名称的产品列表
var ajaxSearch = {


    // 生成新列表
    genNewList : function(jsonData) {
        // 生成新的产品列表
        $("#result").attr('innerHTML', '');
        
        var count = 0;
        for (product in jsonData.products)
        {
            var id = jsonData.products[product]['id'];
            var name = jsonData.products[product]['name'];
            $("#result").append("<dd><a href=\"javascript:handleProduct("+id+", '"+option+"');\">"+name+"</a></dd>");
            count++;
        }

        if (count == 0)
            $("#tip").attr('innerHTML', '没有匹配的检索结果');
        else
            $("#tip").attr('innerHTML', '');
    },

    //更新产品列表
    updateList : function(jsonData) {
        //user errors display
        if (jsonData.hasError)
        {
            var errors = '';
            for(error in jsonData.errors)
                //IE6 bug fix
                if(error != 'indexOf')
                    errors += jsonData.errors[error] + "\n";
            alert(errors);
        }
        // 生成新的产品列表
        ajaxSearch.genNewList(jsonData);
        ajaxSearch.updateDisplay();
    },

    //获取产品列表
    getList : function(value){

        //send the ajax request to the server
        $.ajax({
            type: 'GET',
            url: url,
            async: true,
            cache: false,
            dataType : "json",
            data: data+value,
            success: function(jsonData)    {ajaxSearch.updateList(jsonData)},
            error: function() {alert('ERROR : unable to get the product list');}
        });
    },

    updateDisplay : function(){
        $("form p:has(span)").show();

    }//, IE bug
}


//when document is loaded...
$(document).ready(function(){
    
    $("form p:has(span)").hide();
    // expand/collapse management
    $('#search').bind('click', function(){
        ajaxSearch.getList($("#name").val());
    });

    $('#name').keydown(function(e){
        var key = e.charCode || e.keyCode || 0;
        if (key == 13)
        {
            ajaxSearch.getList(this.value);
            return false;
        }
    });
});

{/literal}

//-->
</script>

{include file="widget/form_header.tpl" id=$form.id val=$form.check}

<p>
<label>{lang txt=$form.fields[0].label}:</label><br />
<input type="text" id="{$form.fields[0].id}" name="{$form.fields[0].id}" value="" class="input" />
<input type="button" name="search" id="search" value="{lang txt='search'}" class="button"/>
</p>

<p>
<label>{lang txt='search result'}:</label><span id="tip" class="tip"></span><br />
<dl id="result"></dl>
</p>

{include file="widget/form_footer.tpl"}


{include file="pair.footer.thickbox.tpl"}
