<?php
/**
 * 主程序入口 包含一些基本的全局设置信息 以及常量等
 * 在这里启动整个程序的运行
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package main
 * @version 1.0
 * @license GNU Lesser General Public License
 */
 
 /*
* Tools::parseString 已经对单引号以及双引号进行转义
* 所以在这里关闭php的字符自动转义
*/
if(get_magic_quotes_runtime())
{
    // Deactivate
    set_magic_quotes_runtime(false);
}
if (get_magic_quotes_gpc()) {
    function magicQuotes_awStripslashes(&$value, $key) {$value = stripslashes($value);}
    $gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    array_walk_recursive($gpc, 'magicQuotes_awStripslashes');
}

if (!function_exists('mime_content_type')) {
    function mime_content_type($f) {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $f);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return trim(exec('file -b --mime-type '.escapeshellarg($f)));
        }
    }
}

if (!function_exists('json_last_error_msg')) {
    function json_last_error_msg() {
        static $errors = array(
            JSON_ERROR_NONE           => null,
            JSON_ERROR_DEPTH          => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR      => 'Unexpected control character found',
            JSON_ERROR_SYNTAX         => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8           => 'Malformed UTF-8 characters, possibly incorrectly encoded'
        );
        $error = json_last_error();
        return array_key_exists($error, $errors) ?  $errors[$error] : "Unknown error ({$error})";
    }
}

function tl_json_decode($json, $assoc=null) {
    $res = json_decode($json, $assoc);
    $err = json_last_error_msg();
    if ($err && $err != 'No error') {
        throw new Exception($err);
    }
    return $res;
}

/**
 * $str 原始字符串
 * $encoding 原始字符串的编码，默认GBK
 * $prefix 编码后的前缀，默认"&#"
 * $postfix 编码后的后缀，默认";"
 */
function unicode_encode($str, $encoding = 'UTF-8', $prefix = '&#', $postfix = ';') {
    $str = iconv($encoding, 'UCS-2', $str);
    $arrstr = str_split($str, 2);
    $unistr = '';
    for($i = 0, $len = count($arrstr); $i < $len; $i++) {
        $dec = hexdec(bin2hex($arrstr[$i]));
        $unistr .= $prefix . $dec . $postfix;
    } 
    return $unistr;
} 
 
/**
 * $str Unicode编码后的字符串
 * $encoding 原始字符串的编码，默认GBK
 * $prefix 编码字符串的前缀，默认"&#"
 * $postfix 编码字符串的后缀，默认";"
 */
function unicode_decode($unistr, $encoding = 'UTF-8', $prefix = '&#', $postfix = ';') {
    $arruni = explode($prefix, $unistr);
    $unistr = '';
    for($i = 1, $len = count($arruni); $i < $len; $i++) {
        if (strlen($postfix) > 0) {
            $arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
        } 
        $temp = intval($arruni[$i]);
        $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
    } 
    return iconv('UCS-2', $encoding, $unistr);
}

/* 
* Improve PHP configuration to prevent issues 
* 设置时区 PHP5
*/
//@ini_set('display_errors', 'off');
@ini_set('upload_max_filesize', '100M');
@ini_set('default_charset', 'utf-8');
if (function_exists('date_default_timezone_set'))
    date_default_timezone_set('PRC');

/* 
* 设置根目录 = 当前目录的上级目录 加快文件加载速度
* 设置可写目录 用于放置用户上传内容 smarty 编译内容等等
* 设置开启写权限的在一个目录 便于管理 
*/
define('_ROOT_DIR_', realpath(substr(dirname(__FILE__), 0, -6)).DIRECTORY_SEPARATOR);
include _ROOT_DIR_.'lib'.DIRECTORY_SEPARATOR.'TL'.DIRECTORY_SEPARATOR.'Loader.php';

/**
 * 兼容>PHP5.2的类属性检查函数
 */
if (!function_exists('property_exists')) {
    function property_exists($class, $property) {
        if (is_object($class)) {
            $class = get_class($class);
        }

        return array_key_exists($property, get_class_vars($class));
    }
}

/**
 * 类的自动加载
 */
if (!function_exists('default_autoload')) {
    function default_autoload($name) {
        $item = explode('_', $name);
    
        switch ($item[0]){
            case 'TL':
                $prefix = 'lib_';
                break;
            default:
                $prefix = 'mod_';
        }
        TL_Loader::loadFile($prefix.$name);
    }
}
spl_autoload_register('default_autoload');

TL_Loader::loadFile('config_define');
