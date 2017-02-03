<?php
/**
 * 控制器类 Factory 方法
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

/**
 * 用于实现一些控制器处理的公共方法，以及加载模板类(view)视图 其他控制器必须继承此类
 *
 * 控制器类必须放置在制定目录下 如 /application/controller/ <br />
 * 视图路径默认 /application/view/controller/action.tpl <br />
 * 并命名为 IndexController.php 文件名 <br />
 * IndexController 类名 <br />
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */

abstract class TL_Controller
{
    /**
     * 用来存储当前单件模式的实例对象
     *
     * @var obj
     */
    private static $_instance = NULL;

    /**
     * 用来存储当前单件模式的视图实例对象
     *
     * protected属性可以便于被继承类引用
     *
     * @var obj
     */
    protected $_view = NULL;

    /**
     * 用来记录当前的控制器 uri
     *
     * @var string
     */
    protected $_controller_uri = NULL;

    /**
     * 用来记录当前的动作 uri
     *
     * @var string
     */
    protected $_action_uri = NULL;
    private $_tpl_dir = '';
    public $_custom_tpl = '';
    protected $_list_page;
    public $referrer = '';

    /**
     * 获取当前控制器并加载视图 以及并设置主题目录
     *
     * 该类使用了单件模式 返回对象实例 确保一个用户只有一个实例在运行
     *
     * @return obj
     */
    public static function getInstance()
    {
        if(!isset(self::$_instance)) {
            /**
             * 获取当前控制器并加载 
             * url里面控制器的命名规则与 class一致 
             * 
             * admin 为 module 保留字，
             * 如 /admin/auth/login/?key=val&...
             * 
             * 默认指向 api，
             * 如 [/api]/auth/login/?key=val&...
             * 
             * 扩展 module 格式为 v+数字如
             * 如 /v5/auth/login/?key=val&...
             */
            $controller_uri   = TL_Tools::safeInput('controller', 'alpha', 'index');
            $controller       = TL_Tools::parseHyphenString($controller_uri);
            list($_, $module) = TL_Tools::getModuleName('api');
            
            /**
             * 加载转换后的contoller
             */
            try {
                TL_Loader::loadFile('apps_'.$module.'_controllers_'.$controller);
            } catch (Exception $e) {
                self::_ERROR(504);
                exit();
            }

            $controller      = $controller.'_Controller';
            self::$_instance = new $controller();

            self::$_instance->_view = TL_View::getInstance();
            if (_IN_DEV_) {
                self::$_instance->_view->caching = false;
                if ($controller_uri != 'ajax') {
                    self::$_instance->_view->debug(true);
                }
            }
            self::$_instance->_tpl_dir = _ROOT_DIR_.'apps'.DIRECTORY_SEPARATOR.
                       $module.DIRECTORY_SEPARATOR.
                       'views';
            self::$_instance->_view->setViewDir(self::$_instance->_tpl_dir);

            $tmp_dir = 'smarty'.DIRECTORY_SEPARATOR.$module;
            $cmp_dir = TL_FSO::getMultDir(sys_get_temp_dir(), $tmp_dir);
            self::$_instance->_view->setCompiledDir($cmp_dir);

            self::$_instance->_controller_uri = $controller_uri;

        }
        return self::$_instance;
    }

    /**
     * 运行当前控制器 并执行用户 request 的动作 
     * 包括对动作的权限检查 以及设置视图的一些全局变量等
     * 出错由 异常机制抛出
     *
     */
    public function dispatch()
    {
        /* 
         * 获取并实现当前的动作(action) 或抛出异常 url=?controller=admin-auth&action=index
         */
        $action_uri = TL_Tools::safeInput('action', 'alpha', 'index');
        $controller = TL_Controller::getInstance();
        $method     = $action_uri.'Action';
        
        if (method_exists($controller, $method)) {
            
            $controller->_action_uri = $action_uri;

            $controller->setCommonDefine();
            $controller->preDispatch();

            /* 控制器处理用户输入以及业务逻辑处理 顺序倒数第二 */
            $controller->$method();
            
            $controller->afterAction();

            /* 输出视图 视图路径默认 /application/view/auth/index.tpl */
            $tpl = str_replace('-', DIRECTORY_SEPARATOR, $controller->_controller_uri).DIRECTORY_SEPARATOR.$controller->_action_uri;
            if (!file_exists($this->_tpl_dir.DIRECTORY_SEPARATOR.$tpl.'.tpl')) {
                $custom_tpl = $controller->_action_uri;
                if ($this->_custom_tpl) {$custom_tpl = $this->_custom_tpl;};
                $tpl = 'widget'.DIRECTORY_SEPARATOR.$custom_tpl;
            }

            $controller->_view->display($tpl);
        }else {
            $this->error(504);
        }
    }

    /** 
     * 执行控制器指定动作 dispatch 中的 action 之前的默认动作 preDispatch 
     * 由继承类实现 如再继承 TL_Controller_Front_Admin 进行一些统一处理 如验证权限
     * 然后所有的后台控制器 再继承至此类
     */
    public function preDispatch(){}
    public function afterAction(){}

    /* 设置常用常量 */
    public function setCommonDefine()
    {
        $this->_view->assign('assets_uri', _ASSETS_URI_);
        $this->_view->assign('base_uri', _BASE_URI_);
        
        /* 每次业务逻辑完成后重新生成输出安全令牌 防止重复提交  */
        $this->_view->assign('token', TL_Tools::getToken());
        
        /* thickbox窗口规格 统一设置 */
        $this->_view->assign('thickbox', _THICKBOX_);  

        $this->_view->assign('upload_uri', _UPLOAD_URI_);
        
        $page_url   = 'http';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
            $page_url .= 's';
        }
        $url = $page_url.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $this->referrer = TL_Tools::safeInput('referrer', 'proto');
        $this->_view->assign('referrer', TL_Tools::base64EncodeUrl($url));
        
        /* 获取页数以及最大数据条目信息 */
        $page = TL_Tools::safeInput('page', 'digit');
        $this->_list_page = $page < 1 ? 1 : $page;
        $this->_view->assign('page', $this->_list_page);
    }

    public static function _ERROR($code)
    {
        $config = _ROOT_DIR_.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'error.txt';
        $error = tl_json_decode(file_get_contents($config), true);
        $message = 'Error:'.$code.' @'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if (isset($error[$code])) {
            $message .= ' ~'.$error[$code];
        }
        return $message;
    }
    
    public function error($code)
    {
        error_log(self::_ERROR($code));
    }
}