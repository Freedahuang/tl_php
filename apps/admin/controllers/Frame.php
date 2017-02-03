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

class Frame_Controller extends Ext_Admin
{

    public function indexAction()
    {
        /* 主页面框架 */
    }

    public function middleAction()
    {
        /* 中部frame用来展开关闭左侧菜单栏 */
    }
}