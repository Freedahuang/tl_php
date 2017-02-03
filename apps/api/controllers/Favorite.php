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

class Favorite_Controller extends Ext_Api_Auth_Redis_Rank
{
    public function preDispatch()
    {
        parent::preDispatch();
        $type = TL_Tools::safeInput('type');
        if (!in_array($type, $this->types)) {
            $this->output(102);
        }
        $this->user_class->setKey($type);
    }
}