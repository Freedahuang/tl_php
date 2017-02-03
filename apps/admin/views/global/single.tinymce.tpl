<script type="text/javascript" src="{$assets_uri}/js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
    var upload_file = "{rewrite_link controller='index' action='upload' token=$token}";
{literal}

    tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        plugins : //"safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        "safari,pagebreak,style,layer,table,preview,inlinepopups,paste,noneditable,visualchars,nonbreaking,xhtmlxtras,template,syntaxhl,media,searchreplace,fullscreen",
        language : "zh",
        remove_linebreaks : false, 
        extended_valid_elements : "textarea[id|cols|rows|disabled|name|readonly|class],pre[name|class]",
        // 插入url的时候不自动转换 如 image 地址
        convert_urls : false,
        paste_text_sticky: true,
        paste_text_sticky_default: true,
        
        // Theme options
        //theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons1 : 
        "bold,italic,underline,separator,fontsizeselect,justifyleft,justifycenter,|,forecolor,backcolor,removeformat,|,sub,sup,charmap",
        theme_advanced_buttons2 : "table,visualaid,|,bullist,numlist,formatselect,|,blockquote,link,unlink,anchor,image,media,|,fullscreen,preview,code", //"cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "", //"tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "", //"insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : false,

        // Example content CSS (should be your site CSS)
        //content_css : "/public/js/tiny_mce/themes/advanced/skins/default/content.css",
        content_css : "/assets/article.css",
        // 文件上传的执行文件
        upload_file : upload_file,

        // Drop lists for link/image/media/template dialogs
        /*
        template_external_list_url : "/js/tinymce/lists/template_list.js",
        external_link_list_url : "/js/tinymce/lists/link_list.js",
        external_image_list_url : "/js/tinymce/lists/image_list.js",
        media_external_list_url : "/js/tinymce/lists/media_list.js",
*/
        // Replace values for the template plugin
        template_replace_values : {
            username : "Some User",
            staffid : "991234"
        }
    });
</script>
{/literal}