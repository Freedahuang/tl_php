
<iframe id="uploadframe" name="uploadframe" width="100%" height=0 frameborder="0"
    scrolling=no  marginwidth="0" marginheight="0" onload="autoHeight()" onresize="autoHeight()"
    src="{rewrite_link controller='ajax' action='upload' option=$option no_thumb=$no_thumb image=$image}"></iframe>
<p id="loading">
<span id="tip" class="editable tip">上传组件加载中 ...</span>
</p>

{literal}
<script type="text/javascript">  
<!--  
var min_height = 60;

function autoHeight()
{
    // preview的高度+input的高度
    var height = $("#uploadframe").contents().find("#preview").attr('height');
    // IE中上行取的值可能是NULL或UNDEFINED
    height = height ? height + min_height : 0; 
    //这样给以一个最小高度  
    $("#uploadframe").height( height < min_height ? min_height : height );  
   
}
$(function(){  
    $("#uploadframe").load(function(){
        // preview的高度+input的高度
        var height = $(this).contents().find("#preview").attr('height') + min_height;  
        //这样给以一个最小高度  
        $(this).height( height < min_height ? min_height : height );  
    });        
});  

$("#uploadframe").load(function(){
    $("#loading").remove();
});
-->  
</script> 
{/literal}