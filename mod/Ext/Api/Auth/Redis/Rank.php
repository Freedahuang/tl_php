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

class Ext_Api_Auth_Redis_Rank extends Ext_Api_Auth_Redis
{
    public function addAction()
    {
        $res = $this->user_class->add($this->target_val);
        $this->result['ok'] = $res;
    }
    
    public function delAction()
    {
        $res = $this->user_class->del($this->target_val);
        $this->result['ok'] = $res;
    }

    public function rankAction()
    {
        $data = $this->user_class->rank(0, 10);
        $this->result = array(
            'ok' => 1,
            'data' => $data
        );
    }
}