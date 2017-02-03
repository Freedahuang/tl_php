/*
 *
 * Copyright (c) 2006 Sam Collett (http://www.texotela.co.uk)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 */

 
/*
 * jQuery ToolTip Demo. Demo of how to add elements and get mouse coordinates
 *	There is also a ToolTip plugin found at http://interface.eyecon.ro/,
 *	  which uses a CSS class to style the tooltip, but shows it below the input/anchor, rather than where the mouse is
 *
 *
 * @name     ToolTipDemo
 * @param    bgcolour  Background colour
 * @param    fgcolour  Foreground colour (i.e. text colour)
 * @author   Sam Collett (http://www.texotela.co.uk)
 * @example  $("a,input").ToolTipDemo()
 *           $("a,input").ToolTipDemo("#fef") (change background colour)
 *           $("a,input").ToolTipDemo("#fef", "#e00") (change background and foreground colour)
 *
 */
$.fn.ToolTipDemo = function(bgcolour, fgcolour)
{
	this.mouseover(
		function(e)
		{
			if((!this.title && !this.alt) && !this.tooltipset) return;
			// get mouse coordinates
			// based on code from http://www.quirksmode.org/js/events_properties.html
			var mouseX = e.pageX || (e.clientX ? e.clientX + document.body.scrollLeft : 0);
			var mouseY = e.pageY || (e.clientY ? e.clientY + document.body.scrollTop : 0);
			mouseX += 10;
			mouseY += 10;
			bgcolour = bgcolour || "#eee";
			fgcolour = fgcolour || "#000";

			var fullHeight = $(document).height();
			var fullWidth = $(document).width();

			// if there is no div containing the tooltip
			if(!this.tooltipdiv)
			{
				// create a div and style it
				var div = document.createElement("div");
				$(div).css(
				{
					border: "2px outset #ddd",
					padding: "2px",
					backgroundColor: bgcolour,
					color: fgcolour,
					position: "absolute",
					width: (fullWidth / 2) + "px"
				})
				// add the title/alt attribute to it
				.html((this.title || this.alt));
				this.title = "";
				this.alt = "";
				$("body").append(div);
				this.tooltipset = true;


				this.tooltipdiv = div;
				//alert($(this.tooltipdiv).height());
			}

			// 获取页面tooltipdiv高度 让tooltipdiv能完全显示
			// hacked by leotan<tanjnr@gmail.com> 20100320
			var pageHeight = $(this.tooltipdiv).height();

			var pageTop = mouseY;

			if (pageHeight >= fullHeight){
				pageHeight = fullHeight - 50;
				$(this.tooltipdiv).height(pageHeight);
				$(this.tooltipdiv).css({overflow:"auto"});

				// 解除之前的mouseout邦定
				$(this).unbind("mouseout");

				$(this.tooltipdiv).mouseout(
					function()
					{
						$(this).hide();
					}
				);

			}

			if (fullHeight - mouseY < pageHeight){
				pageTop = fullHeight - pageHeight - 20;
			}


			$(this.tooltipdiv).css({left: mouseX + "px", top: pageTop + 3 + "px"});

			//alert("fullHeight = "+fullHeight+"\nmouseY=" + mouseY+"\npageHeight=" + pageHeight);
			$(this.tooltipdiv).show();
		}
	).mouseout(
		function()
		{
			if(this.tooltipdiv)
			{
				$(this.tooltipdiv).hide();
			}
		}
	);
	return this;
}