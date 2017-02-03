<?php

/* 全局常量设置 */
// define('_UPLOAD_DIR_', 
//     _ROOT_DIR_.'public'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR);

/*
* 对应smarty插件函数rewrite_link
* 支持以下2种URI 第二种需开启Rewrite模块 建议使用URI2
*
* URI1 = /?module=admin&controller=index&action=index&param1=1&param2=2
* URI2 = /module/controller/action/param1/value1/param2/value2
*/
define('_BASE_URI_', TL_Tools::getBaseUri());
define('_UPLOAD_URI_', _BASE_URI_.'upload/');
define('_REWRITE_URI_', true);

if (!empty($_SERVER['HTTP_HOST'])) {
    $host = TL_Tools::getHostOnly();
    $_arr = array();
    if (!filter_var($host, FILTER_VALIDATE_IP)) {
        $_arr = array_reverse(explode('.', $host));
    }
    $_dev = false;
    if ($host == '127.0.0.1' || $host == TL_Tools::getServerIp() || $_arr[0] == 'dev') {
        $assets_uri = 'http://'.$_SERVER['HTTP_HOST'].'/assets';
        $_dev = true;
    } else if (count($_arr) > 1) {
        $assets_uri = 'http://assets.'.$_arr[1].'.'.$_arr[0];
    }
    define('_ASSETS_URI_', $assets_uri);
    define('_IN_DEV_', $_dev);
}

/*
$filename = _ROOT_DIR_.'config'.DIRECTORY_SEPARATOR.
    'json'.DIRECTORY_SEPARATOR.'redis'.DIRECTORY_SEPARATOR.
    TL_Tools::getConfigSuffix();
if (file_exists($filename) && extension_loaded('Redis')) {
    $config = json_decode(file_get_contents($filename), true);
    if (isset($config['default'])) {
        $host = $config['default']['host'];
        $port = $config['default']['port'];
        ini_set('session.save_handler', 'redis');
        ini_set('session.save_path', 'tcp://'.$host.':'.$port);
    }
}
*/
define('_DEFAULT_MODULE_', 'api');              // www if available
define('_REDIS_ADMIN_', 'admin');

define('AWS_ACCESS_ID', 'your id');
define('AWS_ACCESS_KEY', 'your key');
define('AWS_BUCKET', 'your bucket');
define('AWS_REGION', 'ap-southeast-2');
