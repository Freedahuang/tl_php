/*
 * Thickbox 3.1 - One Box To Rule Them All.
 * By Cody Lindley (http://www.codylindley.com)
 * Copyright (c) 2007 cody lindley
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 *
 * Patched Version by Jamie Thompson - Fixes IE7 Positioning Issues
 * http://jamazon.co.uk/web/2008/03/17/thickbox-31-ie7-positioning-bug/
*/

/*!!!!!!!!!!!!!!!!! edit below this line at your own risk !!!!!!!!!!!!!!!!!!!!!!!*/
var ThickboxI18nImage = '图像';
var ThickboxI18nOf = '或者';
var ThickboxI18nClose = '关闭';
var ThickboxI18nOrEscKey = '或者 Esc 键';
var ThickboxI18nNext = '下一步 >';
var ThickboxI18nPrev = '< 上一步';
var tb_pathToImage = 'data:image/gif;base64,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';


// fixes the fact that ie7 now reports itself as MSIE 6.0 compatible
$.browser.msie6 = 
        $.browser.msie 
        && /MSIE 6\.0/i.test(window.navigator.userAgent) 
        && !/MSIE 7\.0/i.test(window.navigator.userAgent);

//on page load call tb_init
$(document).ready(function(){   
	tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
	imgLoader = new Image();// preload image
	imgLoader.src = tb_pathToImage;
});

//add thickbox to href & area elements that have a class of .thickbox
function tb_init(domChunk){
	$(domChunk).click(function(){
	var t = this.title || this.name || null;
	var a = this.href || this.alt;
	var g = this.rel || false;
	tb_show(t,a,g);
	this.blur();
	return false;
	}).removeClass('thickbox');
}

function tb_show(caption, url, imageGroup) {//function called when the user clicks on a thickbox link

	try {
		if (typeof document.body.style.maxHeight === "undefined") {//if IE 6
			$("body","html").css({height: "100%", width: "100%"});
			$("html").css("overflow","hidden");
			if (document.getElementById("TB_HideSelect") === null) {//iframe to hide select elements in ie6
				$("body").append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div><div id='TB_window'></div>");
				//$("#TB_overlay").click(tb_remove);
			}
		}else{//all others
			if(document.getElementById("TB_overlay") === null){
				$("body").append("<div id='TB_overlay'></div><div id='TB_window'></div>");

				// 覆盖层关闭取消
				//$("#TB_overlay").click(tb_remove);
			}
		}
		
		if(tb_detectMacXFF()){
			$("#TB_overlay").addClass("TB_overlayMacFFBGHack");//use png overlay so hide flash
		}else{
			$("#TB_overlay").addClass("TB_overlayBG");//use background and opacity
		}
		
		if(caption===null){caption="";}
		$("body").append("<div id='TB_load'><img src='"+imgLoader.src+"' /></div>");//add loader to the page
		$('#TB_load').show();//show loader
		
		var baseURL;
	   if(url.indexOf("?")!==-1){ //ff there is a query string involved
			baseURL = url.substr(0, url.indexOf("?"));
	   }else{ 
	   		baseURL = url;
	   }
	   
	   var urlString = /\.jpg$|\.jpeg$|\.png$|\.gif$|\.bmp$/;
	   var urlType = baseURL.toLowerCase().match(urlString);

		if(urlType == '.jpg' || urlType == '.jpeg' || urlType == '.png' || urlType == '.gif' || urlType == '.bmp'){//code to show images
				
			TB_PrevCaption = "";
			TB_PrevURL = "";
			TB_PrevHTML = "";
			TB_NextCaption = "";
			TB_NextURL = "";
			TB_NextHTML = "";
			TB_imageCount = "";
			TB_FoundURL = false;
			if(imageGroup){
				TB_TempArray = $("a[@rel="+imageGroup+"]").get();
				for (TB_Counter = 0; ((TB_Counter < TB_TempArray.length) && (TB_NextHTML === "")); TB_Counter++) {
					var urlTypeTemp = TB_TempArray[TB_Counter].href.toLowerCase().match(urlString);
						if (!(TB_TempArray[TB_Counter].href == url)) {						
							if (TB_FoundURL) {
								TB_NextCaption = TB_TempArray[TB_Counter].title;
								TB_NextURL = TB_TempArray[TB_Counter].href;
								TB_NextHTML = "<span id='TB_next'>&nbsp;&nbsp;<a href='#'>" + ThickboxI18nNext + "</a></span>";
							} else {
								TB_PrevCaption = TB_TempArray[TB_Counter].title;
								TB_PrevURL = TB_TempArray[TB_Counter].href;
								TB_PrevHTML = "<span id='TB_prev'>&nbsp;&nbsp;<a href='#'>" + ThickboxI18nPrev + "</a></span>";
							}
						} else {
							TB_FoundURL = true;
							TB_imageCount = ThickboxI18nImage + ' ' + (TB_Counter + 1) + ' ' + ThickboxI18nOf + ' ' + (TB_TempArray.length);											
						}
				}
			}

			imgPreloader = new Image();
			imgPreloader.onload = function(){		
			imgPreloader.onload = null;
			
			// Resizing large images - orginal by Christian Montoya edited by me.
			var pagesize = tb_getPageSize();
			var x = pagesize[0] - 150;
			var y = pagesize[1] - 150;
			var imageWidth = imgPreloader.width;
			var imageHeight = imgPreloader.height;
			if (imageWidth > x) {
				imageHeight = imageHeight * (x / imageWidth); 
				imageWidth = x; 
				if (imageHeight > y) { 
					imageWidth = imageWidth * (y / imageHeight); 
					imageHeight = y; 
				}
			} else if (imageHeight > y) { 
				imageWidth = imageWidth * (y / imageHeight); 
				imageHeight = y; 
				if (imageWidth > x) { 
					imageHeight = imageHeight * (x / imageWidth); 
					imageWidth = x;
				}
			}
			// End Resizing
			
			TB_WIDTH = imageWidth + 30;
			TB_HEIGHT = imageHeight + 60;
			$("#TB_window").append("<img id='TB_Image' src='"+url+"' width='"+imageWidth+"' height='"+imageHeight+"' alt='"+caption+"'/>" + "<div id='TB_caption'>"+caption+"<div id='TB_secondLine'>" + TB_imageCount + TB_PrevHTML + TB_NextHTML + "</div></div><div id='TB_closeWindow'><a href='#' id='TB_closeWindowButton' title='"+ThickboxI18nClose+"'>"+ThickboxI18nClose+"</a> "+ThickboxI18nOrEscKey+"</div>");
			
			$("#TB_closeWindowButton").click(tb_remove);
			
			if (!(TB_PrevHTML === "")) {
				function goPrev(){
					if($(document).unbind("click",goPrev)){$(document).unbind("click",goPrev);}
					$("#TB_window").remove();
					$("body").append("<div id='TB_window'></div>");
					tb_show(TB_PrevCaption, TB_PrevURL, imageGroup);
					return false;	
				}
				$("#TB_prev").click(goPrev);
			}
			
			if (!(TB_NextHTML === "")) {		
				function goNext(){
					$("#TB_window").remove();
					$("body").append("<div id='TB_window'></div>");
					tb_show(TB_NextCaption, TB_NextURL, imageGroup);				
					return false;	
				}
				$("#TB_next").click(goNext);
				
			}

			document.onkeydown = function(e){ 	
				if (e == null) { // ie
					keycode = event.keyCode;
				} else { // mozilla
					keycode = e.which;
				}
				if(keycode == 27){ // close
					tb_remove();
				} else if(keycode == 190){ // display previous image
					if(!(TB_NextHTML == "")){
						document.onkeydown = "";
						goNext();
					}
				} else if(keycode == 188){ // display next image
					if(!(TB_PrevHTML == "")){
						document.onkeydown = "";
						goPrev();
					}
				}	
			};
			
			tb_position();
			$("#TB_load").remove();
			$("#TB_Image").click(tb_remove);
			$("#TB_window").css({display:"block"}); //for safari using css instead of show
			};
			
			imgPreloader.src = url;
		}else{//code to show html
			
			var queryString = url.replace(/^[^\?]+\??/,'');
			var params = tb_parseQuery( queryString );

			TB_WIDTH = (params['width']*1) + 30 || 630; //defaults to 630 if no paramaters were added to URL
			TB_HEIGHT = (params['height']*1) + 40 || 440; //defaults to 440 if no paramaters were added to URL
			ajaxContentW = TB_WIDTH - 30;
			ajaxContentH = TB_HEIGHT - 45;
			
			if(url.indexOf('TB_iframe') != -1){// either iframe or ajax window		
					urlNoQuery = url.split('TB_');
					$("#TB_iframeContent").remove();
					if(params['modal'] != "true"){//iframe no modal
						$("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>"+caption+"</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' title='"+ThickboxI18nClose+"'>"+ThickboxI18nClose+"</a> "+ThickboxI18nOrEscKey+"</div></div><iframe frameborder='0' hspace='0' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='tb_showIframe()' style='width:"+(ajaxContentW + 29)+"px;height:"+(ajaxContentH + 17)+"px;' > </iframe>");
					}else{//iframe modal
					$("#TB_overlay").unbind();
						$("#TB_window").append("<iframe frameborder='0' hspace='0' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='tb_showIframe()' style='width:"+(ajaxContentW + 29)+"px;height:"+(ajaxContentH + 17)+"px;'> </iframe>");
					}
			}else{// not an iframe, ajax
					if($("#TB_window").css("display") != "block"){
						if(params['modal'] != "true"){//ajax no modal
						$("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>"+caption+"</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' title='"+ThickboxI18nClose+"'>"+ThickboxI18nClose+"</a> "+ThickboxI18nOrEscKey+"</div></div><div id='TB_ajaxContent' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px'></div>");
						}else{//ajax modal
						$("#TB_overlay").unbind();
						$("#TB_window").append("<div id='TB_ajaxContent' class='TB_modal' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px;'></div>");	
						}
					}else{//this means the window is already up, we are just loading new content via ajax
						$("#TB_ajaxContent")[0].style.width = ajaxContentW +"px";
						$("#TB_ajaxContent")[0].style.height = ajaxContentH +"px";
						$("#TB_ajaxContent")[0].scrollTop = 0;
						$("#TB_ajaxWindowTitle").html(caption);
					}
			}
					
			$("#TB_closeWindowButton").click(tb_remove);
			
				if(url.indexOf('TB_inline') != -1){	
					$("#TB_ajaxContent").append($('#' + params['inlineId']).children());
					$("#TB_window").unload(function () {
						$('#' + params['inlineId']).append( $("#TB_ajaxContent").children() ); // move elements back when you're finished
					});
					tb_position();
					$("#TB_load").remove();
					$("#TB_window").css({display:"block"}); 
				}else if(url.indexOf('TB_iframe') != -1){
					tb_position();
					if($.browser.safari){//safari needs help because it will not fire iframe onload
						$("#TB_load").remove();
						$("#TB_window").css({display:"block"});
					}
				}else{
					$("#TB_ajaxContent").load(url += "&random=" + (new Date().getTime()),function(){//to do a post change this load method
						tb_position();
						$("#TB_load").remove();
						tb_init("#TB_ajaxContent a.thickbox");
						$("#TB_window").css({display:"block"});
					});
				}
			
		}

		if(!params['modal']){
			document.onkeyup = function(e){ 	
				if (e == null) { // ie
					keycode = event.keyCode;
				} else { // mozilla
					keycode = e.which;
				}
				if(keycode == 27){ // close
					tb_remove();
				}	
			};
		}
		
	} catch(e) {
		//nothing here
	}
}

//helper functions below
function tb_showIframe(){
	$("#TB_load").remove();
	$("#TB_window").css({display:"block"});
}

function tb_remove() {
 	$("#TB_imageOff").unbind("click");
	$("#TB_closeWindowButton").unbind("click");
	$("#TB_window").fadeOut("fast",function(){$('#TB_window,#TB_overlay,#TB_HideSelect').trigger("unload").unbind().remove();});
	$("#TB_load").remove();
	if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
		$("body","html").css({height: "auto", width: "auto"});
		$("html").css("overflow","");
	}
	document.onkeydown = "";
	document.onkeyup = "";
	return false;
}

function tb_position() {
$("#TB_window").css({marginLeft: '-' + parseInt((TB_WIDTH / 2),10) + 'px', width: TB_WIDTH + 'px'});
	if ( !(jQuery.browser.msie6)) { // take away IE6
		$("#TB_window").css({marginTop: '-' + parseInt((TB_HEIGHT / 2),10) + 'px'});
	}
}

function tb_parseQuery ( query ) {
   var Params = {};
   if ( ! query ) {return Params;}// return empty object
   var Pairs = query.split(/[;&]/);
   for ( var i = 0; i < Pairs.length; i++ ) {
      var KeyVal = Pairs[i].split('=');
      if ( ! KeyVal || KeyVal.length != 2 ) {continue;}
      var key = unescape( KeyVal[0] );
      var val = unescape( KeyVal[1] );
      val = val.replace(/\+/g, ' ');
      Params[key] = val;
   }
   return Params;
}

function tb_getPageSize(){
	var de = document.documentElement;
	var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
	var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
	arrayPageSize = [w,h];
	return arrayPageSize;
}

function tb_detectMacXFF() {
  var userAgent = navigator.userAgent.toLowerCase();
  if (userAgent.indexOf('mac') != -1 && userAgent.indexOf('firefox')!=-1) {
    return true;
  }
}

