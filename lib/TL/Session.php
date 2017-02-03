<?php
/**
 * Session 处理类 用于给 SessionAuthenticate 或 ImageAuthenticate 提供支持
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

session_start();


/**
 * 对客户 cookie 数据进行加密存储 给 其他类提供存储支持 如 AuthMember
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Session
{
    /** 
     * 存储器的命名空间 防止冲突
     *
     * @var string
     */
    private $_namespace;

    /**
     * 构造器 设置命名空间  
     *
     * @param string $namespace
     */
    public function __construct($namespace)
    {
        $this->_namespace = $namespace;
    }

    /**
     * 根据键名 取出数据
     *
     * @param string $key
     * @param string $value
     * @return mixed boolean|NULL
     */
    public function getValue($key)
    {
        return isset($_SESSION[$this->_namespace][$key]) ? $_SESSION[$this->_namespace][$key] : NULL;
    }

    /**
     * 根据键名 设置数据
     *
     * @param string $key
     * @param string $value
     */
    public function setValue($key, $value)
    {
        $_SESSION[$this->_namespace][$key] = $value;
    }

    /**
     * 获取当前 SESSION ID
     *
     * @return string
     */
    public static function getId()
    {
        return session_id();
    }
}


