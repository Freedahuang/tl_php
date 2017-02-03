<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
if (is_readable('../geshi.php')) {
    $path = '../';
} elseif (is_readable('geshi.php')) {
    $path = './';
} else {
    die('Could not find geshi.php - make sure it is in your include path!');
}
require $path . 'geshi.php';

$fill_source = false;
if (isset($_POST['submit'])) {
    if (get_magic_quotes_gpc()) {
        $_POST['source'] = stripslashes($_POST['source']);
    }
    $fill_source = true;

    $geshi = new GeSHi($_POST['source'], $_POST['language']);
    $geshi->set_header_type(GESHI_HEADER_PRE_VALID);
    //取消css样式，用标签中的style属性
    $geshi->enable_classes(false);
    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    $geshi->set_overall_style('font: normal normal 90% monospace; color: #000066; border: 1px solid #d0d0d0; background-color: #f0f0f0;', false);

    $geshi->set_line_style('background: #fcfcfc;', 'background: #f0f0f0;');
    $geshi->set_code_style('font:14px sans-serif;color: #000020;', true);
    $geshi->set_tab_width(4);

    $geshi->set_link_styles(GESHI_LINK, 'color: #000060;');
    $geshi->set_link_styles(GESHI_HOVER, 'background-color: #f0f000;');
/*
    $geshi->set_header_content('');
    $geshi->set_header_content_style('font-family: sans-serif; color: #808080; font-size: 70%; font-weight: bold; background-color: #f0f0ff; border-bottom: 1px solid #d0d0d0; padding: 2px;');
    */

    $geshi->set_footer_content('Parsed in <TIME> seconds at <SPEED>, using GeSHi <VERSION>');
    $geshi->set_footer_content_style('font-family: sans-serif; color: #808080; font-size: 100%; background-color: #f0f0ff; border-top: 1px solid #d0d0d0; padding: 2px;');
    
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>高亮代码</title>
    <style type="text/css">
    <!--
    html {
        background-color: #f0f0f0;
    }
    body {
        font-family: Verdana, Arial, sans-serif;
        margin: 10px;
        border: 2px solid #e0e0e0;
        background-color: #fcfcfc;
        padding: 5px;
    }
    h2 {
        margin: .1em 0 .2em .5em;
        border-bottom: 1px solid #b0b0b0;
        color: #b0b0b0;
        font-weight: normal;
        font-size: 150%;
    }
    h3 {
        margin: .1em 0 .2em .5em;
        color: #b0b0b0;
        font-weight: normal;
        font-size: 120%;
    }
    textarea {
        border: 1px solid #b0b0b0;
        color: #333;
        margin-left: 20px;
    }
    select, input {
        margin-left: 20px;
    }
    p {
        font-size: 90%;
        margin-left: .5em;
    }
    -->
    </style>
    <script language="javascript" type="text/javascript" src="../../tiny_mce_popup.js"></script>
    <script language="javascript" type="text/javascript" src="jscripts/functions.js"></script>
</head>
<body>
<h2>代码高亮</h2>
<?php
if (isset($_POST['submit'])) {
    echo '预览：<br/>';
    echo '<div id="result_code">';
    echo $geshi->parse_code();
    echo '</div>';
    echo '<input type="button" onclick="javascript:insertCode(\'result_code\');" value="插入代码">';
    echo '<hr />';
}
?>
<form action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post">
<table>
<tr>
<td>开发语言</td>
<td>
<select name="language" id="language">
<?php
if (!($dir = @opendir(dirname(__FILE__) . '/geshi'))) {
    if (!($dir = @opendir(dirname(__FILE__) . '/../geshi'))) {
        echo '<option>No languages available!</option>';
    }
}
$languages = array();
while ($file = readdir($dir)) {
    if ( $file[0] == '.' || strpos($file, '.', 1) === false) {
        continue;
    }
    $lang = substr($file, 0,  strpos($file, '.'));
    $languages[] = $lang;
}
closedir($dir);
sort($languages);
foreach ($languages as $lang) {
    if (isset($_POST['language']) && $_POST['language'] == $lang) {
        $selected = 'selected="selected"';
    } else {
        $selected = '';
    }
    echo '<option value="' . $lang . '" '. $selected .'>' . $lang . "</option>\n";
}
?>
</select></td>
</tr>
<tr>
<td>代码</td>
<td><textarea rows="20" cols="60" name="source" id="source"></textarea></td>
</tr>
<tr>
<th colspan="2"><input type="submit" name="submit" value="预览代码" />
<input type="submit" name="clear" onclick="document.getElementById('source').value='';document.getElementById('language').value='';return false" value="清除" /></th>
</tr>
</table>
</form>
</body>
</html>
