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

class Follow_Controller extends Ext_Api_Auth_Redis_Rank
{
    public function addAction()
    {
        $user = new User($this->target_val);
        if (empty($user->uid)) {
            $this->output(402);
        }
        if ($this->user->uid == $user->uid) {
            $this->output(401);
        }
        parent::addAction();
    }
}