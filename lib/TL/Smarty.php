<?php
/**
 * 视图类，引用Smraty做输出 用法与Db类相同
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */
if(!defined('_ROOT_DIR_')) {
    exit('TL Smarty Access Denied');
}

TL_Loader::loadFile('lib_smarty_Smarty.class');

/**
 * 实现基本的视图功能 如 给视图赋值 输出视图 等
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Smarty extends TL_View
{
    /**
     * 引入的视图操作对象实例 默认 smarty
     *
     * @var obj
     */
    private $_view;

    /**
     * 构造器 设置视图操作的一些基本参数
     *
     * @return resource
     */
    public function __construct()
    {
        $this->_view = new Smarty();
        return $this->_view;
    }

    /**
     * 设置视图模板目录
     *
     */
    public function setViewDir($view_dir)
    {
        $this->_view->template_dir = $view_dir;
    }

    /**
     * 设置视图模板编译目录
     *
     */
    public function setCompiledDir($dir)
    {
        $this->_view->compile_dir = $dir;
    }

    /**
     * 传递变量给视图 便于视图调用
     *
     */
    public function assign($key, $value = null)
    {
        if ($value == null){
            $this->_view->assign($key);
        }
        else {
            $this->_view->assign($key, $value);
        }
    }

    /**
     * 加载过滤插件 便于视图调用
     *
     */
    public function load_filter($type, $callback)
    {
        $this->_view->load_filter($type, $callback);
    }

    /**
     * 输出视图
     *
     */
    public function display($tpl)
    {
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
        }
        $tpl = str_replace('-', DIRECTORY_SEPARATOR, $tpl).'.tpl';
        $this->_view->display($tpl);
    }

    public function debug($set=false)
    {
        $this->_view->force_compile = $set;
        $this->_view->compile_check = $set;
        /*
        $this->_view->debugging     = $set;
        $this->_view->debug_tpl     = _ROOT_DIR_.'lib'.DIRECTORY_SEPARATOR.
                                      'smarty'.DIRECTORY_SEPARATOR.
                                      'debug.tpl';
                                      */
    }

    /**
     * 获取控制器的 table 显示字段 并传递给视图
     *
     */
    public function getTable($data)
    {
        $this->_view->assign('table', TL_View::getTableFields($data));    
    }

    /**
     * 获取控制器的 form 显示字段 并传递给视图
     *
     */
    public function getForm($data)
    {
        $this->_view->assign('form', TL_View::getFormFields($data));    
    }
}