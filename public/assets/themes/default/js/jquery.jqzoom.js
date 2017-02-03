//**************************************************************
// jQZoom allows you to realize a small magnifier window,close
// to the image or images on your web page easily.
//
// jqZoom version 1.0
// Author Doc. Ing. Renzi Marco(www.mind-projects.it)
// Released on Dec 05 2007
// i'm searching for a job,pick me up!!!
// mail: renzi.mrc@gmail.com
//**************************************************************

(function($){

        $.fn.jqueryzoom = function(options){

        var settings = {
                xzoom: 260,        //zoomed width default width
                yzoom: 260,        //zoomed div default width
                offset: 10,        //zoomed div default offset
                position: "right"  //zoomed div default position,offset position is to the right of the image
            };

            if(options) {
                $.extend(settings, options);
            }

        $(this).hover(function(){
            $("#quantity").hide();
            
                var imageLeft = $(this).get(0).offsetLeft;
                var imageRight = $(this).get(0).offsetRight;
                var imageTop = $(this).get(0).offsetTop + 20;
                var imageWidth = $(this).get(0).offsetWidth;
                var imageHeight = $(this).get(0).offsetHeight;

                var bigimage = $(this).attr("alt");

                if($("div.zoomdiv").get().length == 0){
                    $(this).after("<div class='zoomdiv'><img class='bigimg' src='"+bigimage+"'/></div>");
                }

                if(settings.position == "right"){
                    leftpos = imageLeft + imageWidth + settings.offset;
                }else{
                    leftpos = imageLeft - settings.xzoom - settings.offset;
                }

                $("div.zoomdiv").css({ top: imageTop,left: leftpos });
                $("div.zoomdiv").width(settings.xzoom);
                $("div.zoomdiv").height(settings.yzoom);
                $("div.zoomdiv").show();

                $(document.body).mousemove(function(e){

                    var bigwidth = $(".bigimg").get(0).offsetWidth;
                    var bigheight = $(".bigimg").get(0).offsetHeight;

                    var scaley = 'x';
                    var scalex = 'y';

                    if(isNaN(scalex)|isNaN(scaley)){
                        var scalex = Math.round(bigwidth/imageWidth) ;
                        var scaley = Math.round(bigheight/imageHeight);
                    }

                    mouse = new MouseEvent(e);

                    scrolly = mouse.y - imageTop - ($("div.zoomdiv").height()*1/scaley)/2 ;
                    //$("div.zoomdiv").get(0).scrollTop = scrolly * scaley ;
                    $("div.zoomdiv").get(0).scrollTop = scrolly * scaley - (settings.yzoom + settings.offset)  ;

                    scrollx = mouse.x - imageLeft - ($("div.zoomdiv").width()*1/scalex)/2 ;
                    $("div.zoomdiv").get(0).scrollLeft = (scrollx) * scalex;
                    /* 新的大图 500 小图 300 要减去 260 + 10 的偏移量 怪异 可能是大图与小图的比例问题 否则右下角会有 240x240的区域无法响应鼠标 */
                    //$("div.zoomdiv").get(0).scrollLeft = (scrollx) * scalex - 270;
                    //$("div.zoomdiv").get(0).scrollLeft = (scrollx) * scalex - (settings.xzoom + settings.offset) ;
//$("#enlarge").attr("innerHTML", $("div.zoomdiv").get(0).scrollLeft);
                });
            },function(){
               $("div.zoomdiv").hide();
               $(document.body).unbind("mousemove");
               //$(".lenszoom").remove();
               $("div.zoomdiv").remove();

               $("#quantity").show();
            });

        }

})(jQuery);

function MouseEvent(e) {
    /* 鼠标位置获取的是 相对于页面的位置 而 图片放置在 #wrap容器里 需要减去他们之间的偏移量 */
    var offset = ($(document.body).width() - $("#wrap").width())/2;
    this.x = offset > 1 ? e.pageX - offset : e.pageX;
    this.y = e.pageY;
}


