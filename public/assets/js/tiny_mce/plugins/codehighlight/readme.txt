这个高亮插件是为tinymce编辑器做的，使用的是geshi开源程序来实现多语言的代码高亮的
1.解压后把插件放入tinymce的plugins目录中
2.别忘了把这个插件在tinymce编辑器初始化的时候加载进来
tinyMCE.init({
	plugins : "codehighlight",
	theme_advanced_buttons3_add : "codehighlight"
});