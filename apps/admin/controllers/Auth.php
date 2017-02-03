<?php

/**
* 控制器类 登陆验证 退出等等操作
*
* 控制器类必须放置在制定目录下 如 /application/controller/
* 视图路径默认 /application/view/controller/action.tpl
* 并命名为 indexController.php 文件名
* IndexController 类名
*
* @author Liang Tan <tanjnr@gmail.com>
* @version 1.0
*/

class Auth_Controller extends Ext_Admin
{
    public $auth = false;
    
    public function indexAction()
    {
        /* 返回主页 */
        TL_Tools::redirect('auth/login');
    }

    public function loginAction()
    {
        $name     = TL_Tools::safeInput('name', 'alpha');
        $password = TL_Tools::safeInput('password', 'proto');
        $code     = TL_Tools::safeInput('captcha', 'proto');
        $message  = '';

        if ($this->account->is_logined) {
            TL_Tools::redirect('index');
        }
        
        if (TL_Tools::isSubmit() && $name && $password) {
            $captcha = new TL_Captcha();

            if (!$captcha->auth($code)) {
                $message = 'invalid captcha code';
            }else if ($this->account->login($name, $password)) {
                TL_Tools::redirect('index'); 
            }else {
                $message = 'login failed, please check your account info!';
            }

            //TL_Tools::redirect();
        }

        $this->_view->assign('message', $message);
    }

    public function logoutAction()
    {
        $this->account->logout();
        $this->indexAction();
    }
}