
function AutoScroll(obj){
    $(obj).find("ul:first").animate({
            marginTop:"-30px"
    },500,function(){
            $(this).css({marginTop:"0px"}).find("li:first").appendTo(this);
    });
}
$(document).ready(function(){
    var list = $("#scrollDiv > ul > li");
    if (list.length > 1){
        timerID = setInterval('AutoScroll("#scrollDiv")',3500)
        list.each(function(){
            $(this).hover(
                function(){
                    clearInterval(timerID);
                },
                function(){
                    timerID = setInterval('AutoScroll("#scrollDiv")',3500)
                }
            );
        });
    }

});
