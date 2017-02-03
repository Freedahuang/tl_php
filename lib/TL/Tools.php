<?php
/**
 * 工具类 包含获取POST GET值 页面跳转 错误限制等函数
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Tools
{
    /**
     * 转换 admin-auth = Admin_Auth 或者 admin = Admin
     *
     * @return string
     */
    public static function parseHyphenString($string)
    {
        $split  = explode('-', $string);
        $pos = strpos($string, '_');
        if ($pos !== false) {
            $split  = explode('_', $string);
        }
        
        foreach ($split as $key => $value) {
            $split[$key] = ucfirst(strtolower($value));
        }
        return implode('_', $split); 
    }

    /**
     * 判断是否从视图中有提交动作 
     *
     * 在提交动作中检查token 所有提交动作 GET or POST <br />
     * 都必须带上submit和token变量 <br />
     * 
     * 优点：减少代码量 <br />
     * 缺点：无法提示表单过期（对于非法提交无动作提示也许是好事） <br />
     * 表单提交时间为15分钟 <br />
     *
     * @return boolean
     */
    public static function isSubmit($expired=800)
    {
        /* token 有效期 15 分钟 防止重复提交 */
        $token = self::isToken(self::safeInput('token', 'proto'), $expired);
        $submit = self::safeInput('submit', 'proto');
        return ($token && $submit) ? true : false;
    }

    /**
     * Token 令牌设计 防止重复提交 跨站攻击 等等
     * 类似验证码 只是由系统自动提交
     *
     * token必须为view视图组件的属性 <br />
     * token在controller控制器action动作中进行判断处理 处理完后重新生成 <br />
     *
     * @return string
     */
    public static function generateToken()
    {
        /* token 里包含当前HTTP_HOST 限制跨域 */
        return md5($_SERVER['HTTP_HOST'].'^&65%*e^&^78$&$7%$');        
    }

    public static function generateKey($name, $password)
    {
        /* token 里包含当前Session ID 判断来路 */
        return self::base64EncodeUrl(self::encrypt($name.';'.$password,'encode'));       
    }

    public static function parseKey($key)
    {
        return explode(';', self::encrypt(self::base64DecodeUrl($key),'decode'));
    }

    /**
     * 获取Token 令牌 
     *
     * @return string
     */
    public static function getToken()
    {
        $token = self::generateToken();
        return self::base64EncodeUrl(self::encrypt($token.','.(time()),'encode'));
    }

    /**
     * 验证令牌 
     *
     * @return boolean
     */
    public static function isToken($token, $expired)
    {
        $code = self::encrypt(self::base64DecodeUrl($token),'decode');
        $md5 = $code;
        $time = time();
        if (strpos($code, ',') !== false) {
            list($md5, $time) = explode(',', $code);
        }
        return ($md5 == self::generateToken() && $time - time() < $expired) ? true : false;
    }

    /**
     * 安全输入 对字符串进行转义格式化之类的 POST GET 数据
     *
     * 参数validity为格式验证 有如下选项 <br />
     * NULL|alpha|digit|password|email|array etc <br />
     * NULL = 未知类型|默认类型 对其进行字符转义 self::parseString(); <br />
     * alpha = 字母+数字+下划线 <br />
     * array = 数组 默认字符转义处理 <br />
     * 其他 <br />
     *
     * @param string $key String to sanitize
     * @param string $defult String
     * @param string $validity 
     * @return string Sanitized string
     */
    public static function safeInput($key, $mode = NULL, $default = NULL)
    {
        $result = self::getRawInput($key);
        if ($mode == 'proto' || is_array($result)) {
            return $result;
        }
        
//         if (is_array($result)) {
//             throw new Exception('Params ['.$key.'] is an array. you need set mode to "proto" for unsafe input.');
//         }
        
        $format['email'] = array('reg' => '^[\w\-\.]+@[\w\-\.]+(\.\w+)+$', 'default' => '');
        $format['alpha'] = array('reg' => '^[a-zA-Z0-9_\-]+$', 'default' => '');
        $format['zys_'] = array('reg' => '^[\x{4E00}-\x{9FFF}_a-zA-Z0-9]+$', 'default' => '');
        $format['digit'] = array('reg' => '^[.0-9\-]+$', 'default' => 0);
    
        if (!isset($format[$mode])) {
            if ($result !== null ) {
                return is_string($result) ? self::parseString($result) : $result;
            } else {
                return $default;
            }
        } else {
            if ($result !== null && preg_match("/{$format[$mode]['reg']}/u", $result)) {
                return $result;
            } else if ($default !== null) {
                return $default;
            }else {
                return $format[$mode]['default'];
            }
        }
    }
    /**
     * 判断是否 http 地址 用于 后台菜单 加载外部链接 
     *
     * @param string $string
     * @return boolean
     */
    public static function isHttp($string)
    {
        return preg_match("/[a-z]+:\/\/[^\s]*/i", $string);
    }

    /**
     * 根据 rewrite 规则 重写 uri 并 供Smarty调用
     *
     * @param array $params
     * @return string
     */
    public static function rewriteLink($params)
    {
        /* 处理菜单参数为link的状况 */
        if (!empty($params['link'])) {
            if (self::isHttp($params['link'])) {
                $_params = array(
                    'controller' => 'page', 
                    'action' => 'link',
                    'option' => self::base64EncodeUrl($params['link']));
            } else {
                $_ = explode('/', $params['link']);
                $_params = array(
                    'controller' => $_[0], 
                    'action' => $_[1]);
                for ($i = 2; $i < count($_); $i += 2) {
                    $_params[$_[$i]] = $_[$i+1];
                }
            }
            return self::rewriteLink($_params);
        } else {
            $result = self::getHttpHost();
            $single = array('module', 'controller', 'action', 'thickbox');
            list($type, $name) = self::getModuleName('admin');
            if (!isset($params['module']) && $type == 'query') {
                $params = array_reverse($params, true);
                $params['module'] = $name;
                $params = array_reverse($params, true);
            }
            if ($type != 'query') {
                unset($params['module']);
            }
            
            foreach ($params as $key => $value) {
                if ($value == NULL) {
                    continue;
                }
                $result .= _REWRITE_URI_ ? 
                    (in_array($key, $single) ? $value.'/' : $key.'/'.$value.'/') : 
                    $key.'='.$value.'&';
            }
            
            //return $result.(_REWRITE_URI_ ? '?' : '');
            return $result;
        }
    }
    
    /**
     * 返回当前的主机地址 host 后台菜单调用自身 host 的 http 链接时
     *
     * @param mixed array|string
     * @return string
     */
    public static function getHttpHost()
    {
        return (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').
            self::getHost();
    }

    public static function getHost()
    {
        return $_SERVER['HTTP_HOST']._BASE_URI_.(_REWRITE_URI_ ? '' : '?');
    }


    /**
     * 返回由英文转成 md5 = 键名的变量至 并供Smarty调用
     *
     * @param mixed array|string
     * @return string
     */
    private static $_LANG = null;
    public static function getLang($string)
    {
        if (self::$_LANG == null) {
            list($_, $module) = self::getModuleName(_DEFAULT_MODULE_);
            self::$_LANG = include _ROOT_DIR_.'config'.DIRECTORY_SEPARATOR.
                'lang'.DIRECTORY_SEPARATOR.$module.'.zh.php';
        }

        $txt = $suf = '';
        if (is_array($string))
            list($txt, $suf) = $string;
        else
            $txt = $string;

        $result = (isset(self::$_LANG[$txt]) ? self::$_LANG[$txt] : '');
        if (empty($result)) {
            $key = md5($txt);
            $result = (isset(self::$_LANG[$key])) ? 
                self::$_LANG[$key] : (str_replace('"', '&quot;', $txt));
        }
        $result .= $suf ? '<span class="tip">'.$suf.'</span>' : '';

        return $result;    
    }

    /**
     * 从客户端获取数据 包括 POST GET 以及 REWRITE 后的 URI
     * 成功返回值 失败返回 NULL
     *
     * COOKIE 涉及到命名空间 另外有专门的类对其进行处理
     *
     * @param string $key
     * @return mixed string|NULL
     */
    public static function getRawInput($key)
    {
        $_URI = array_change_key_case(self::getParameterFromRequestUri(), CASE_LOWER);
        $_POST = array_change_key_case($_POST, CASE_LOWER);
        $header = getallheaders();
        if (isset($header['Content-Type']) && strpos($header['Content-Type'], 'application/json') !== false) {
            $_POST = json_decode(file_get_contents('php://input'), true);
        }
        $_GET = array_change_key_case($_GET, CASE_LOWER);
        
        return array_key_exists($key, $_POST) ? $_POST[$key] : 
            (array_key_exists($key, $_GET) ? $_GET[$key] :
            (array_key_exists($key, $_URI) ? $_URI[$key] : null));
    }

    /**
     * 获取 $_SERVER 变量 进行URL处理 让MVC可以使用类似 Zend Framework
     * URL = /index/index/param1/1/param2/2
     * 
     * [REQUEST_URI] => /tt/index/index/you/sdfds <br />
     * [SCRIPT_NAME] => /tt/index.php <br />
     *
     * 步骤：  <br />
     * 去除前导目录 /tt 再拆分成数组 <br />
     *
     * @return array
     */
    private static function getParameterFromRequestUri()
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        $prefix_uri = self::getBaseUri();

        /* 去除子目录前缀 */
        if (is_int(strpos($request_uri, $prefix_uri)))
            $request_uri = substr($request_uri, strlen($prefix_uri));

        return self::parseParameterFromRequestUri($request_uri);
    }

    /**
     * 解析来自 URI 的字符 实现 类似 ZF 的 URI 结构
     *
     * @param array
     * @return array
     */
    private static function parseParameterFromRequestUri($request_uri)
    {
        $result = array();
        $pos = 2;
        /*
        * 函数 array_slice($array, 0) = 重建数组索引 0,1,2,3,4
        * 函数 array_filter() = 去除值为空的数组变量
        */
        $uris = array_slice(array_filter(explode('/', $request_uri), 
            create_function('$value', 'return $value == "" ? false : true;')),
            0);
        
        $host = self::getHostOnly();
        // 域名且是二级才
        if (!filter_var($host, FILTER_VALIDATE_IP)) {
            $_arr = explode('.', $host);
            if (count($_arr) > 2) {
                $result['module'] = array_reverse($_arr)[2];
                if (isset($uris[0])) {
                    $result['controller'] = $uris[0];
                }
            } else if (_REWRITE_URI_ && isset($uris[0])) {
                $result['module'] = $uris[0];
            }
        } else if (_REWRITE_URI_ && isset($uris[0])) {
            $result['module'] = $uris[0];
        }
        if (isset($result['controller']) && isset($uris[1])) {
            $result['action'] = $uris[1];
        } else if (count($uris) > 2) {
            $result['controller'] = $uris[1];
            $result['action'] = $uris[2];
            $pos = 3;
        }
        for ($i = $pos; $i < count($uris); $i = $i + 2) {
            if (isset($uris[$i+1])) {
                $result[$uris[$i]] = $uris[$i+1];
            }
        }

        return $result;
    
    }
    /**
     * 获取URI基本路径 如 文件在 /tt/index.php 目录下
     * 或文件在 /index.php目录下 并在控制器传递给视图
     *
     * @return string
     */
    public static function getBaseUri()
    {
        return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/').'/';
    }

    /**
     * 函数parseString，对字符串进行预处理
     *
     * 函数接受2个参数$string和$check_sql <br />
     * 函数判断$check_sql的布尔值，以决定是否对字符串进行防止SQL注入检查 <br />
     * 而后检查php环境是否设置了自动转义，已决定对字符串进行转义 <br />
     *
     * 函数返回处理后的字符串 <br />
     *
     * @param string 待处理字符串
     * @param boolean 是否进行SQL关键字检查
     * @return string
     */
    public static function parseString($string)
    {
        // 如果未设置自动转义
        return self::convertSqlToOrd($string);
        //return get_magic_quotes_gpc() ? $string : addslashes($string);
    }

    /**
     * 未完成函数 应该在safeInput函数中进行强类型检查 已确保客户端数据输入安全性
     *
     * 函数checkSQL，对字符串进行SQL检查并转义  <br />
     *
     * 函数接受1个参数$string <br />
     * 使用preg_replace_callback依次检查SQL关键字 <br />
     * 并用callback函数对匹配的关键字使用convertStringToOrdList函数进行处理 <br />
     * 
     * 使用callback的原因是可以对不区分大小写的关键字进行匹配 <br />
     * 如 Insert into table 可以被 insert 匹配到，得到并处理即时字符串 Insert 而不是 insert <br />
     * 其他如str_ireplace无法达到此效果 <br />
     *
     * 函数返回处理后的字符串 <br />
     *
     * @param string 待处理字符串
     * @return string
     */
    public static function convertSqlToOrd($string)
    {
        /* 
        * tinymce要求特殊字符'|"|<|>转义 
        * 转义单双引号也可以防止 SQL 利用注释攻击
        */
        $_blocked_sql_list = '\'|"|<|>|%';

        
        // 查找相关关键字 并替换成 ord 格式 如 &#101 等
        foreach (explode('|', $_blocked_sql_list) as $blocked)
        {
            $string = preg_replace_callback(
                "/{$blocked}/i",
                create_function(
                 // 这里使用单引号很关键，
                 // 否则就把所有的 $ 换成 \$
                 '$matches',
                 'return TL_Tools::convertStringToOrd($matches[0]);'
                ),
                $string);
        }
        return $string;
    }
    /**
     *
     * 函数convertStringToOrd，转换字符串至ASCII码
     *
     * 函数接受1个参数$string <br />
     * 转换字符串至ASCII码，并附加 &# 以供HTML识别 <br />
     * 
     * 例： <br />
     * &#76; = L 等等 <br />
     *
     * 函数返回处理后的字符串 <br />
     *
     * @param string 待处理字符串
     * @return string
     */
    public static function convertStringToOrd($string)
    {
        $result = '';
        foreach (str_split($string) as $chr)
        {
            $result .= '&#'.ord($chr).';';
        }
        return $result;
    }

    /**
     *
     * 函数convertStringToChr，转换ASCII码至字符串
     *
     * 函数接受1个参数$string <br />
     * 转换附加了 &# 的ASCII码至字符串，并 以供非HTML输出 <br />
     * 转成ASCII是为了防止SQL注入，并且可以在HTML格式下正常显示 <br />
     * 转回来是为了输出成其他格式文件，如TXT格式 <br />
     *
     * 已知BUG <br />
     *
     * &#76&#79&#65&#68&#95&#70&#73&#76&#69 = LOAD_FILE <br />
     * 防止 &#76&#79&#65&#68&#95&#70&#73&#76&#699 <- != LOAD_FILE9 <br />
     * 
     * 例： <br />
     * &#76 = L 等等 <br />
     *
     * 函数返回处理后的字符串 <br />
     *
     * @param string 待处理字符串
     * @return string
     */
    public static function convertStringToChr($string)
    {
        return preg_replace_callback(
            "/&#([0-9]+);/i",
            create_function(
             // 这里使用单引号很关键，
             // 否则就把所有的 $ 换成 \$
             '$matches',
             'return chr($matches[1]);'
            ),
            $string);
    }


    /**
     * Redirect user to another page 页面跳转函数
     *
     * @param string $url Desired URL
     * @param string $baseUri Base URI (optional)
     */
    public static function redirect($url='')
    {
        if ($url) {
            list($type, $name) = self::getModuleName(_DEFAULT_MODULE_);
            if ($type == 'query') {
                $url = $name.'/'.$url;
            }
            
            if ($url && strpos($url, '://') === false){
                $url = self::rewriteLink(
                                self::parseParameterFromRequestUri(
                                    self::getBaseUri().$url
                                )
                            );
            }
        } else {
            $url = TL_Tools::safeInput('referrer', 'proto');
            if ($url) {
                $url = TL_Tools::base64DecodeUrl($url);
            } else {
                $url = $_SERVER['HTTP_REFERER'];
            }
        }
        
        header('Location: '.$url);
        exit;
    }
    
    public static function getModuleName($defualt)
    {
        $host = self::getHostOnly();
        $name = $defualt;
        
        // query=模块名在 /module 
        // domain=模块名在二级域名
        $type = filter_var($host, FILTER_VALIDATE_IP) ? 'query' : 'domain';
        if ($type == 'query') {
            $name = TL_Tools::safeInput('module', 'alpha', $defualt);
        } else {
            $_arr = array_reverse(explode('.', $host));
            if (isset($_arr[2])) {
                $name = $_arr[2];
            } else {
                $type = 'query';
                $name = TL_Tools::safeInput('module', 'alpha', $defualt);
            }
        }
        
        return array($type, $name);
    }

    public static function getHostOnly()
    {
        $res = $_SERVER['HTTP_HOST'];
        $pos = strpos($res, ':');
        if ($pos !== false) {
            $res = substr($res, 0, $pos);
        }
        return $res;
    }
    /**
     * 第三方 加密解密字符串 原理类似 把 待加密的字符串 与 密匙字符串 转换成二进制做并按位异或(^)运算
     * 如
     * 加密过程 01(待加密)^11(密匙) = 10(加密后) 
     * 解码过程 10(加密后)^11(密匙) = 01(解密后)
     *
     * 关键点 保持 密匙二进制长度 与 加密字符串长度一致
     *
     * 加密 encrypt('daichao','E','daichao');
     * 解密 encrypt('被加密过的字符串','D','daichao');
     *
     * @param string $string
     * @param string $operation E|D
     * @param string $key
     * @return string
     */
    public static function encrypt($string, $operation='encode', $key='a4PdNKabBbDQ3W7.nQkKvQkbHOPQa61O1UhNkOasPIVUzC9KJyD8Ph0M')
    {
        $key=md5($key);
        $key_length=strlen($key);
        $string=$operation=='decode'?self::base64DecodeUrl($string):substr(md5($string.$key),0,8).$string;
        $string_length=strlen($string);
        $rndkey=$box=array();
        $result='';
        for($i=0;$i<=255;$i++)
        {
            $rndkey[$i]=ord($key[$i%$key_length]);
            $box[$i]=$i;
        }
        for($j=$i=0;$i<256;$i++)
        {
            $j=($j+$box[$i]+$rndkey[$i])%256;
            $tmp=$box[$i];
            $box[$i]=$box[$j];
            $box[$j]=$tmp;
        }
        for($a=$j=$i=0;$i<$string_length;$i++)
        {
            $a=($a+1)%256;
            $j=($j+$box[$a])%256;
            $tmp=$box[$a];
            $box[$a]=$box[$j];
            $box[$j]=$tmp;
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
        }
        if($operation=='decode')
        {
            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
            {
                return substr($result,8);
            }
            else
            {
                return '';
            }
        }
        else
        {
            return self::base64EncodeUrl($result);
        }
    }

    /**
     * 析取权限字符串 controller1=action1,action2;controller2=action3,action4
     * 返回数组
     *
     * @param string $string
     * @return array
     */
    public static function convertStringToArray($string)
    {
        $result = array();
        foreach (explode(';', $string) as $privilege)
        {
            $controller = $privilege;
            $actions = '';
            if (strpos($privilege, '=') !== false) {
                list($controller, $actions) = explode('=', $privilege);
            }
            $result[$controller] = explode(',', $actions);
        }
        return $result;
    }

    /**
     * 析取临时购物车字符串 id_product_code1-id_attribute_size=quantity1;id_product_code2=quantity2
     * 返回数组
     * @param string $string
     * @return array
     */
    public static function strToArray($string, $k = ';', $v = '=')
    {
        if (!$string)
            return array();

        $result = array();
        foreach (explode($k, $string) as $item)
        {
            list($id, $quantity) = explode($v, $item, 2);
            if ($id){
                $result[$id] = $quantity;
            }
        }
        return $result;
    }

    /**
     * 组合临时购物车数组
     * 返回字符串 与 本类 parseCart 对应
     *
     * @param array $cart
     * @return string
     */
    public static function arrayToStr($cart, $k = ';', $v = '=')
    {
        if (!is_array($cart))
            return '';

        $result = '';
        foreach ($cart as $key => $item)
            $result .= $key.$v.$item.$k;
        return $result;
    }

    /**
     * 对客户端提交的 经过js escape函数出里的字符解码
     *
     * @param string $str
     * @return string
     */
    public static function unescape($str)
    {
        $ret = '';
        $len = strlen($str);

        for ($i = 0; $i < $len; $i++)
        {
            if ($str[$i] == '%' && $str[$i+1] == 'u')
            {
                $val = hexdec(substr($str, $i+2, 4));

                if ($val < 0x7f) 
                    $ret .= chr($val);

                else if($val < 0x800) 
                    $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));

                else 
                    $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));

                $i += 5;
            }
            else if ($str[$i] == '%')
            {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            }
            else $ret .= $str[$i];
        }
        return $ret;
    }

    /**
     * 时间比较函数 返回分钟
     *
     * @param string $d1
     * @param string $d2
     * @return number
     */
    public static function timeDiff($d1, $d2)
    {  
        if(is_string($d1))$d1=strtotime($d1);
        if(is_string($d2))$d2=strtotime($d2);
        return ($d2-$d1)/60; // 1天 = 86400秒，1小时 = 3600
    }

    /**
     * 获取区域时间端 月份 传递一个参数 比如从现在起2009-02-25前3个月
     * 返回 array(
     *          '2008' => array('11', '12'),
     *          '2009' => array('1'),
     *          );
     *
     * @param string $interval
     * @return array
     */
    public static function timeInterval($interval, $self = true)
    {  
        $result = array();
        $interval = $self ? $interval - 1 : $interval;
        $time = mktime(0, 0, 0, date("m") - $interval, date("d"), date("Y"));
        $start_year = date("Y", $time);
        $start_month = date("n", $time);
        $curr_month = date("n");
        $curr_month = $self ? $curr_month + 1 : $curr_month;

        for ($i = $start_year; $i <= date("Y"); $i++)
        {
            for ($j = $start_month; $j <= 12; $j++)
            {
                if ($j == $curr_month)
                    break;

                $result[$i][] = $j;
            }
            $start_month = 1;
        }
        return $result;
    }

    /**
     * 获取客户端IP 成功返回 IP 失败返回 NULL
     *
     * @return string|NULL
     */
    public static function getClientIP()
    {  
        if (getenv('HTTP_CLIENT_IP')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        }else if (getenv('HTTP_X_FORWARDED_FOR')) {
            list($onlineip) = explode(',', getenv('HTTP_X_FORWARDED_FOR'));
        }else if (getenv('REMOTE_ADDR')) {
            $onlineip = getenv('REMOTE_ADDR');
        }else {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        return $onlineip ? $onlineip : NULL;
    }

    /**
    * 获取服务器端IP地址
     * @return string
     */
    public static function getServerIp() { 
        if (isset($_SERVER)) { 
            if($_SERVER['SERVER_ADDR']) {
                $server_ip = $_SERVER['SERVER_ADDR']; 
            } else { 
                $server_ip = $_SERVER['LOCAL_ADDR']; 
            } 
        } else { 
            $server_ip = getenv('SERVER_ADDR');
        } 
        return $server_ip; 
    }

    public static function base64EncodeUrl($string)
    {
        $source = array('+', '/');
        $target = array('*', '-');
        $result = base64_encode($string);
        $result = str_replace($source, $target, $result);
        return rtrim($result, '=');
    }

    public static function base64DecodeUrl($string)
    {
        $source = array('+', '/');
        $target = array('*', '-');
        return base64_decode(str_replace($target, $source, $string));
    }

    //将内容进行UNICODE编码，编码后的内容格式：YOKA\u738b （原始：YOKA王）
    public static function unicode_encode($name)
    {
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2)
        {
            // 参考 http://www.cloved.cn/?p=152 UCS-2 在WIN与LIN之间的区别
            // 大小头问题
            $c = $name[$i];
            $c2 = $name[$i + 1];
            if (!preg_match('/WIN/i',PHP_OS)){
                $c1 = $c2;
                $c2 = $c;
                $c = $c1;
            }
            if (ord($c) > 0)
            {    // 两个字节的文字
                $str .= '\u'.sprintf('%02s', base_convert(ord($c), 10, 16)).
                    sprintf('%02s', base_convert(ord($c2), 10, 16));
            }
            else
            {
                $str .= $c2;
            }
        }
        return $str;
    }

    // 将UNICODE编码后的内容进行解码，编码后的内容格式：YOKA\u738b （原始：YOKA王）
    public static function unicode_decode($name)
    {
        // 转换编码，将Unicode编码转换成可以浏览的utf-8编码
        $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
        preg_match_all($pattern, $name, $matches);
        if (!empty($matches))
        {
            $name = '';
            for ($j = 0; $j < count($matches[0]); $j++)
            {
                $str = $matches[0][$j];
                if (strpos($str, '\\u') === 0)
                {
                    $code = base_convert(substr($str, 2, 2), 16, 10);
                    $code2 = base_convert(substr($str, 4), 16, 10);
                    // to-do 大小头问题
                    $c = chr($code).chr($code2);
                    $c = iconv('UCS-2', 'UTF-8', $c);
                    $name .= $c;
                }
                else
                {
                    $name .= $str;
                }
            }
        }
        return $name;
    }

  public static function json_encode($a=false)
  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
    {
      if (is_float($a))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
        static $jsonReplaces = array(array(/*"\\", "/", "\n", "\t", "\r", "\b", "\f", */'"'), array(/*'\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', */'\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
    {
      if (key($a) !== $i)
      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)
    {
      foreach ($a as $v) $result[] = self::json_encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($a as $k => $v) $result[] = self::json_encode($k).':'.self::json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }

    // 以数组的形式返回域名
    // ['suffix' => 'cc', 'domain' => 'doyo', 'dmain2' => 'm', 'domain3' => 'beijing-dongcheng']
    // 格式为 后缀，主域名，二级域名，三级域名
    public static function getDomainArray($string = '')
    {
        $string = strlen($string) ? $string : $_SERVER['HTTP_HOST'];
        $domains = array_reverse(explode('.', $string));
        $suffix = array_shift($domains);
        $domain = array_shift($domains);

        $result = compact('suffix', 'domain');

        if (empty($domains)){
            return $result;
        }
        
        $count = 2;
        foreach ($domains as $item){
            $result['domain'.$count] = $item;
            $count++;
        }
        return $result;
    }

    public static function iconv2Html($data, $type='utf-8')
    {
       if (is_array($data)) {
          foreach($data as $k=>$v) {
              $data[$k] = self::iconv2Html($v);
          }
       }
       else {
          $data = mb_convert_encoding($data, "html-entities", $type);
       }
       return $data;
    }
    
    /* vim: set expandtab: */
    /**
     *@packageBugFree
     *@version$Id: FunctionsMain.inc.php,v 1.32 2005/09/24 11:38:37 wwccss Exp $
     *
     *
     * Return part of a string(Enhance the function substr())
     *
     *@authorChunsheng Wang <wwccss@263.net>
     *@paramstring $String the string to cut.
     *@paramint $Length the length of returned string.
     *@parambooble $Append whether append "...": false|true
     *@returnstring the cutted string.
     */
    public static function truncateStr($String, $Length, $Append=false)
    {
        if (strlen($String) <= $Length){
            return $String;
        }
        else {
            $I=0;
            while ($I<$Length) {
                $StringTMP=substr($String,$I,1);
                if ( ord($StringTMP)>=224 ) {
                    $StringTMP=substr($String,$I,3);
                    $I=$I+3;
                }
                elseif (ord($StringTMP)>=192 ) {
                    $StringTMP=substr($String,$I,2);
                    $I=$I+2;
                }
                else {
                    $I=$I+1;
                }
                $StringLast[]=$StringTMP;
            }
    
            $StringLast=implode("",$StringLast);
    
            if($Append) {
                $StringLast.="...";
            }
            return $StringLast;
        }
    }
    
    public static function reverseStr($string)
    {
        $string_length = mb_strlen($string, 'utf-8');
        $return = '';
        for ($i = $string_length -1; $i >= 0; $i--)
        {
            $return .= mb_substr($string, $i, 1, 'utf-8');
        }
        return $return;
    }
    
    public static function reverseArr($arr, $flag=true) {
        foreach ($arr as $key => $val) {
            if (is_array($val))
                $arr[$key] = self::reverseArr($val, $flag);
        }
        return array_reverse($arr, $flag);
    }
    
    public static function getConfigSuffix()
    {
        $config_test = '';
        if (file_exists(_ROOT_DIR_.'var'.DIRECTORY_SEPARATOR.'config.test')) {
            $config_test = trim(file_get_contents(_ROOT_DIR_.'var'.DIRECTORY_SEPARATOR.'config.test'));
        } 
        
        $config_suffix = 'online';
        if (isset($_SERVER['HTTP_HOST']) && strpos($config_test, $_SERVER['HTTP_HOST']) !== false) {
            $config_suffix = 'test';
        } else if (_IN_DEV_) {
            $config_suffix = 'local';
        }
        return $config_suffix;
    }
    
    public static $ns_base = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
    public static function num2str($num) 
    {
        $len = strlen(self::$ns_base);
        $result = '';
        do {
            $result = self::$ns_base[$num % $len] . $result;
            $num = intval($num / $len);
        } while ($num != 0);
        
        return $result;
    }
    
    public static function str2num($str)
    {
        $len = strlen(self::$ns_base);
        $strmap = array_flip(str_split(self::$ns_base));
        
        $result = 0;
        
        for ($n = 0; $n < strlen($str); $n++) {
            $result *= $len;
            $result += $strmap[$str{$n}];
        }
        
        return $result; 
    }
    
    public static function ip2mod($mod)
    {
        return abs(ip2long(self::getClientIP()));
    }
}
