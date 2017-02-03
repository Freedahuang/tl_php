<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('memory_limit','512M');
define('_IN_DEV_', true);
//define('_IN_TEST_', true);
require '../../config/_initialize.php';

$obj = new Data();
$obj->tblInit();

// 重建搜索索引
// $search = new Search('article');
// $article = new Article();
// $all = $article->getAllByParams();
// foreach ($all as $v) {
//     $search->set($v['id'], $v['name']);
// }
// $res = $search->get('大米');
// var_dump($res);