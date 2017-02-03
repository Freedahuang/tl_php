<?php

/**
* 控制器类 
*
* 控制器类必须放置在制定目录下 如 /application/controller/
* 视图路径默认 /application/view/controller/action.tpl
* 并命名为 indexController.php 文件名
* IndexController 类名
*
* @author Liang Tan <tanjnr@gmail.com>
* @version 1.0
*/

class Ext_Api_Auth_Redis extends Ext_Api_Auth
{
    protected $target_val = null;
    protected $user_class = null;
    
    public function preDispatch()
    {
        parent::preDispatch();
        if (in_array($this->_action_uri, array('add', 'del'))) {
            $this->target_val = TL_Tools::safeInput('target');
            if (empty($this->target_val)) {
                $this->output(103);
            }
        }
        $cls = $this->_controller_uri;
        $this->user_class = new $cls($this->user->uid);
    }

    public function getAction()
    {
        $offset = TL_Tools::safeInput('offset', 'digit');
        $count = TL_Tools::safeInput('count', 'digit');
        $data = $this->user_class->get($offset, $count);
        $this->result = array(
            'ok' => 1,
            'data' => $data
        );
    }
}