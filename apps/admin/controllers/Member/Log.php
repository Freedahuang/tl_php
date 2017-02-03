<?php

/**
* 控制器类 参数控制器 用来设置系统参数 
*
* 控制器类必须放置在制定目录下 如 /application/controller/
* 视图路径默认 /application/view/controller/action.tpl
* 并命名为 indexController.php 文件名
* IndexController 类名
*
* @author Liang Tan <tanjnr@gmail.com>
* @version 1.0
*/

class Member_Log_Controller extends Ext_Admin
{
    public $name = '操作日志';
 
    public $actions = array(
        /* 
        * manage包括新增和编辑操作 有tab表示将出现在tab菜单
        * auth表示动作权限 真 = 表示该动作需要权限 并验证系统参数权限列表是否有此ID
        */
        'list'      => array('name' => '查看操作日志', 'tab' => '操作日志查看', 'auth' => true),
        );
}