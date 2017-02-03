<?php

/**
* 控制器类 产品属性控制器 用来显示 
*
* 控制器类必须放置在制定目录下 如 /application/controller/
* 视图路径默认 /application/view/controller/action.tpl
* 并命名为 indexController.php 文件名
* IndexController 类名
*
* @author Liang Tan <tanjnr@gmail.com>
* @version 1.0
*/

class Article_Region_Controller extends Ext_Admin
{
    public $name = '文章地区';

    public $actions = array(
        /* 
        * manage包括新增和编辑操作 有tab表示将出现在tab菜单
        * auth表示动作权限 真 = 表示该动作需要权限 并验证用户权限列表是否有此ID
        */
        'add'      => array('name' => '新增文章地区', 'tab' => NULL, 'auth' => false),
        'del'      => array('name' => '删除文章地区', 'tab' => NULL, 'auth' => false),
        );

    public function preEdit($obj) 
    {
        $option = TL_Tools::safeInput('option', 'digit');

        if (!$obj->id) {
            $obj->id_article = $option;
        }
        return $obj;
    }

}