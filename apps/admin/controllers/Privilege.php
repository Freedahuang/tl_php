<?php
/**
 * 控制器类 角色权限控制器
*
* 控制器类必须放置在制定目录下 如 /application/controller/
* 视图路径默认 /application/view/controller/action.tpl
* 并命名为 indexController.php 文件名
* IndexController 类名
*
* @author Liang Tan <tanjnr@gmail.com>
* @version 1.0
*/


class Privilege_Controller extends Ext_Admin
{
    public $name = '权限角色';

    public function preEdit($obj)
    {
        if (TL_Tools::isSubmit()) {
            $obj->privileges = '';
            $controllers     = TL_Tools::safeInput('controllers', 'proto');

            foreach ($controllers as $controller) {
                $actions = TL_Tools::safeInput($controller, 'proto');
                if (!empty($actions)) {
                    $obj->privileges .= $controller.'='.implode(',', $actions).';';
                }
            }
        }
        return $obj;
    }

    public function afterEditSubmit($obj)
    {
        $this->_view->assign('auth_list', Privilege::getAuthList());
        $this->_view->assign('account_privilege', TL_Tools::convertStringToArray($obj->privileges));
    }
}