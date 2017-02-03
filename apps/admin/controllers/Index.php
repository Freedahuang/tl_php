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

class Index_Controller extends Ext_Admin
{

    public function indexAction()
    {
        /* 主页面框架 */
    }

    public function welcomeAction()
    {
        $config = new Config('baidu_tongji');
        if ($config->data) {
            //header('Location: http://tongji.baidu.com/web/welcome/ico?s='.$config->data);
            //exit;
        }
    }

    public function middleAction()
    {
        /* 中部frame用来展开关闭左侧菜单栏 */
    }
    
    public function uploadAction()
    {
        $res = '';
        $str = file_get_contents('php://input');
        $str = explode("\r\n\r\n", $str, 2);
        
        $s3 = TL_S3::getInstance();
        if ($s3->upload('local', $str[1])) {
            $res = $s3->getPath();
        }
        echo $res;exit;
    }
}