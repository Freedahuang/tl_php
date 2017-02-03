<?php
/**
 * Cookie处理类 配合Blowfish类对数据进行加密存储
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */
if(!defined('_BASE_URI_')) {
    exit('Access Denied');
}

abstract class Cookie_Encrypted
{
    /** @var array Contain cookie content in a key => value format */
    protected $_content;

    /** @var array Crypted cookie name for setcookie() */
    protected $_name;

    /** @var array expiration date for setcookie() */
    protected $_expire;

    /** @var array Website domain for setcookie() */
    protected $_domain;

    /** @var array Path for setcookie() */
    protected $_path;

    /** @var array Blowfish instance */
    protected $_bf;

    /** @var array 56 chars Blowfish initialization key */
    protected $_key;

    /** @var array 8 chars Blowfish initilization vector */
    protected $_iv;

    /**
      * Get data if the cookie exists and else initialize an new one
      *
      * @param $name Cookie name before encrypting
      * @param $path
      */
    public function __construct($name, $path = '', $expire = NULL)
    {
        $this->_content = array();
        $this->_expire = isset($expire) ? (time() + intval($expire)) : (time() + 365 * 24 * 3600); // 1728000
        $this->_name = md5($name);
        $this->_path = trim(_BASE_URI_.$path, '/\\').'/';
        if ($this->_path{0} != '/') $this->_path = '/'.$this->_path;
        $this->_path = rawurlencode($this->_path);
        $this->_path = str_replace('%2F', '/', $this->_path);
        $this->_path = str_replace('%7E', '~', $this->_path);
        $this->_key = 'a4PdNKabBbDQ3WD.nQkKvQkbHOPhNQa61O1UkOasPIVUzC9KJyD8Ph0M';
        $this->_iv = '0gCPuv5R';
        $this->_domain = $this->getDomain();
        $this->_bf = new TL_Blowfish($this->_key, $this->_iv);
        $this->update();
    }

    private function getDomain()
    {
        $r = '!(?:(\w+)://)?(?:(\w+)\:(\w+)@)?([^/:]+)?(?:\:(\d*))?([^#?]+)?(?:\?([^#]+))?(?:#(.+$))?!i';
        preg_match ($r, $_SERVER['HTTP_HOST'], $out);
        if (preg_match("/^(((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]{1}[0-9]|[1-9]).)". 
         "{1}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]).)". 
         "{2}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]){1}))$/", $out[4]))
            return false;
        $host = preg_replace('/^[^\.]*\.([^\.]*)\.(.*)$/', '\1.\2', $out[4]);
        if (!strpos($host, '.'))
            return false;
        return '.'.$host;
    }

    /**
      * Set expiration date
      *
      * @param integer $expire Expiration time from now
      */
    function setExpire($expire)
    {
        $this->_expire = intval($expire);
    }

    /**
      * Magic method wich return cookie data from _content array
      *
      * @param $key key wanted
      * @return string value corresponding to the key
      */
    function __get($key)
    {
        return isset($this->_content[$key]) ? $this->_content[$key] : false;
    }

    /**
      * Magic method wich check if key exists in the cookie
      *
      * @param $key key wanted
      * @return boolean key existence
      */
    function __isset($key)
    {
        return isset($this->_content[$key]);
    }

    /**
      * Magic method wich add data into _content array
      *
      * @param $key key desired
      * @param $value value corresponding to the key
      */
    function __set($key, $value)
    {
        if (preg_match('/¤|\|/', $key) OR preg_match('/¤|\|/', $value))
            throw new Exception('Forbidden chars in cookie');
        $this->_content[$key] = $value;
        $this->write();
    }

    /**
      * Magic method wich delete data into _content array
      *
      * @param $key key wanted
      */
    function __unset($key)
    {
        unset($this->_content[$key]);
        $this->write();
    }

    /**
      * Get cookie content
      */
    function update($nullValues = false)
    {
        if (isset($_COOKIE[$this->_name]))
        {
            /* Decrypt cookie content */
            $content = $this->_bf->decrypt($_COOKIE[$this->_name]);

            /* Get cookie checksum */
            $checksum = crc32($this->_iv.substr($content, 0, strrpos($content, '¤') + 2));

            /* Unserialize cookie content */
            $tmpTab = explode('¤', $content);

            foreach ($tmpTab AS $keyAndValue)
            {
                $tmpTab2 = explode('|', $keyAndValue);
                if (sizeof($tmpTab2) == 2)
                     $this->_content[$tmpTab2[0]] = $tmpTab2[1];
             }

            /* Blowfish fix */
            if (isset($this->_content['checksum']))
                $this->_content['checksum'] = intval($this->_content['checksum']);

            /* Check if cookie has not been modified */
            if (!isset($this->_content['checksum']) OR $this->_content['checksum'] != $checksum)
                $this->logout();
        }
        else
            $this->_content['date_add'] = date('Y-m-d H:i:s');
    }

    /**
      * Setcookie according to php version
      */
    private function _setcookie($cookie = NULL)
    {
        if ($cookie)
        {
            $content = $this->_bf->encrypt($cookie);
            $time = $this->_expire;
        }
        else
        {
            $content = 0;
            $time = time() - 1;
        }

        if (version_compare(substr(phpversion(), 0, 3), '5.2.0') == -1)
            return setcookie($this->_name, $content, $time, $this->_path, $this->_domain, 0);
        else
            return setcookie($this->_name, $content, $time, $this->_path, $this->_domain, 0, true);
    }

    /**
      * Save cookie with setcookie()
      */
    function write()
    {
        $cookie = '';

        /* Serialize cookie content */
        if (isset($this->_content['checksum'])) unset($this->_content['checksum']);
        foreach ($this->_content AS $key => $value)
            $cookie .= $key.'|'.$value.'¤';

        /* Add checksum to cookie */
        $cookie .= 'checksum|'.crc32($this->_iv.$cookie);

        /* Cookies are encrypted for evident security reasons */
        return $this->_setcookie($cookie);
    }

    /**
     * Get a family of variables (e.g. "filter_")
     */
    public function getFamily($origin)
    {
        $result = array();
        if (count($this->_content) == 0)
            return $result;
        foreach ($this->_content AS $key => $value)
            if (strncmp($key, $origin, strlen($origin)) == 0)
                $result[$key] = $value;
        return $result;
    }

    /**
     *
     */
    public function unsetFamily($origin)
    {
        $family = $this->getFamily($origin);
        foreach ($family AS $member => $value)
            unset($this->$member);
    }
    
    /**
      * Delete cookie
      */
    public function logout()
    {
        $this->_content = array();
        $this->_setcookie();
        unset($_COOKIE[$this->_name]);
    }    

}

/**
 * 对客户 cookie 数据进行加密存储 给 其他类提供存储支持 如 AuthMember
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Cookie extends Cookie_Encrypted
{
    /**
     * 根据键名 获取数据
     *
     * @param string $key
     * @return string
     */
    public function getValue($key)
    {
        $this->write();
        return $this->$key;
    }

    /**
     * 根据键名 设置数据 只能设置 字符串数据
     *
     * @param string $key
     * @param string $value
     */
    public function setValue($key, $value)
    {
        $this->$key = $value;
        $this->write();
    }
}