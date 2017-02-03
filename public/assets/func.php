<?php

/**
 *  根据id 生成分级目录 $num 个 目录下文件最多 $num 个
 */
function genMultiDir($root_dir, $id, $num=1000)
{
    $max = $num * $num;
    $dir = array();
    getBase($id, $num, $dir);
    $dir = array_reverse($dir);
    $dst = implode(DIRECTORY_SEPARATOR, $dir);
    return getMultDir($root_dir, $dst);
}

function getBase($id, $num, &$res)
{
    $base = $num * $num;
    $good = intval($id / $base);
    $left = intval($id % $base);
    //var_dump($good);exit;
    $res[] = 'd'.$left;
    if ($good != 0) {
        getBase($good, $num, $res);
    }
}

function createDir($dir)
{
    clearstatcache();
    if (file_exists($dir))
        return true;

    return mkdir($dir);
}

function getMultDir($dir, $value)
{
    foreach (explode(DIRECTORY_SEPARATOR, $value) as $item) {
        $dir .= $item.DIRECTORY_SEPARATOR;
        if (!createDir($dir)) {
            throw new Exception('failed to create dir:'.$dir);
        }
    }
    return $dir;
}
function parse_js($string){
    $pregString="#var[\s]*([a-zA-Z_0-9]+)[\s]*=[\s]*([^;]*);#";
    preg_match_all($pregString,$string,$JsArrayPre);
    $num=count($JsArrayPre['0']);
    for($i=0;$i<$num;$i++){
        $jsVarName=$JsArrayPre['1'][$i];
        $JsArray[$jsVarName]= $JsArrayPre['2'][$i];
    }
    return $JsArray;
}

function base64DecodeUrl($string)
{
    $source = array('+', '/');
    $target = array('*', '-');
    return base64_decode(str_replace($target, $source, $string));
}
/*
for ($i = 0; $i <= 105; $i++) {
  $res = array();
  getBase($i, 6, $res);
  var_dump('iiiiiiii='.$i);
  var_dump($res);
}
*/
//genMultiDir('./content/soxn/apk/', 890, 3);
/*
for ($i=0; $i < 3670; $i++) { 
    $dir = genMultiDir('./content/soxn/apk/', $i, 3);
    var_dump($dir.$i);
    file_put_contents($dir.$i, 'test');
}
*/
if (!function_exists('apache_request_headers')) {
    function apache_request_headers() {
        foreach($_SERVER as $key=>$value) {
            if (substr($key,0,5)=="HTTP_") {
                $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
                $out[$key]=$value;
            }else{
                $out[$key]=$value;
            }
        }
        return $out;
    }
}
define('_ROOT_DIR_', realpath(substr(dirname(__FILE__), 0, -6)).DIRECTORY_SEPARATOR);

