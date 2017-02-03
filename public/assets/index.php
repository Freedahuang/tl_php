<?php
if (in_array($_SERVER['HTTP_HOST'], array('zflm.com', 'www.zflm.com'))) {
    header("Content-type: text/html; charset=utf-8");
echo '域名备案中，请移步<a href="http://www.zflmclub.com">www.zflmclub.com</a>';
exit;  
}

require './func.php';

if (empty($_GET)) {exit;}
$root_dir = './content/';
$id = intval($_GET['id']);
unset($_GET['id']);
$name = $_GET['name'];
unset($_GET['name']);
$value = implode(DIRECTORY_SEPARATOR, $_GET);
$dst = getMultDir($root_dir, $value);
$dir = genMultiDir($dst, $id);
$path = $dir.$name;

$content = file_get_contents('php://input');
if (!empty($content)) {
    file_put_contents($path, $content);
} 

echo ltrim($path, '.');

