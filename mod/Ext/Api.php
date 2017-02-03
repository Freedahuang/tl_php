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
define('_THICKBOX_', '?keepThis=true&amp;TB_iframe=true&amp;width=620&amp;height=580&amp;');

abstract class Ext_Api extends TL_Controller
{
    protected $result = array(
        'ok' => 0
    );
    
    protected $cates = array(
        'article',
        'food',
        'sport'
    );
    
    protected $redis_admin;

    /** 
     * 执行控制器指定动作 dispatch 中的 action 之前的默认动作 preDispatch 
     * 由继承类实现 如再继承 TL_Controller_Front_Admin 进行一些统一处理 如验证权限
     * 然后所有的后台控制器 再继承至此类
     */
    public function preDispatch() {
        /*
        $this->_view->assign('controller_uri', $this->_controller_uri);
        $this->_view->assign('action_uri', $this->_action_uri);
        $this->_view->assign('theme_uri', _ASSETS_URI_.'/themes/default/');
        $this->_view->assign('image_uri', _ASSETS_URI_.'/themes/default/images/');
        $this->_view->assign('host_name', _HOST_NAME_);
        */
        $this->redis_admin = TL_Redis::getInstance(_REDIS_ADMIN_);
    }
    
    public function afterAction()
    {
        $this->output();
    }
    
    protected function output($error=0)
    {
        if ($error) {
            $this->result['error'] = $error;
        }
        $output = array();
        $output['success'] = intval($this->result['ok']);
        if (isset($this->result['data'])) {
            $output['data'] = $this->result['data'];
            $output['success'] = !isset($output['success']) ? 1 : $output['success'];
        }
        if (isset($this->result['error'])) {
            $output['error'] = $this->result['error'];
        } else if (!$output['success']) {
            $message = 'Unknow message for '.$this->_controller_uri.'/'.$this->_action_uri.'. May missing result output.';
            if (isset($this->result['errmsg'])) {
                $message = $this->result['errmsg'];
            }
            error_log($message);
            $output['error'] = 501;
        }
        if (isset($output['error'])) {
            $config = _ROOT_DIR_.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'error.txt';
            $error = tl_json_decode(file_get_contents($config), true);
            $message = $error[$output['error']];;
            if (isset($this->result['errmsg'])) {
                $message = $this->result['errmsg'];
            }
            $output['errmsg'] = $message;
        }
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($output);
        exit;
    }
    
    public function error($code)
    {
        parent::error($code);
        $this->output($code);
    }
}
