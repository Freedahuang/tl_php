<?php
/**
 * 类的加载器
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

if (!defined("_ROOT_DIR_")) {
    die('Hacking attemp!');
}

/**
 * 类的加载器
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Loader
{
    /**
     * 已加载的文件清单，防止重复加载
     *
     * @var array
     */
    private static $_located = array();

    /**
     * 加载文件操作
     *
     * @param string The name of the class, case SenSItivE
     */
    public static function loadFile($name)
    {
        if (in_array($name, self::$_located) || class_exists($name, false)) {
            return true;
        }

        $file = _ROOT_DIR_.str_replace("_", DIRECTORY_SEPARATOR, $name).".php";

        if (file_exists($file)) {
            require_once $file;
            array_push(self::$_located, $name);
            return true;
        }else {
            /* 加载失败则抛出异常 */
            throw new TL_Exception('load file '.$file.' fail!');
        }

    }
}


