<?php
/**
* 控制器类 菜单控制器 用来显示 编辑菜单
*
* 控制器类必须放置在制定目录下 如 /application/controller/
* 视图路径默认 /application/view/controller/action.tpl
* 并命名为 indexController.php 文件名
* IndexController 类名
*
* @author Liang Tan <tanjnr@gmail.com>
* @version 1.0
*/


class Menu_Controller extends Ext_Admin_Tree
{
    public $name = '系统菜单';
    
    /* 左侧菜单类别显示动作 */
    public function indexAction()
    {
        $menu = Menu::getMenu($this->account->privileges);
        // if only one http link in group, hide it
        foreach ($menu as $k => $v) {
            $count_active = 0;
            foreach ($v['sub'] as $k2 => $v2) {
                if (!TL_Tools::isHttp($v2['link']) && $v2['active']) {
                    $count_active++;
                }
            }
            if ($count_active == 0) {
                foreach ($v['sub'] as $k2 => $v2) {
                    if (TL_Tools::isHttp($v2['link'])) {
                        $menu[$k]['sub'][$k2]['active'] = 0;
                    }
                }                
            }
        }
        $this->_view->assign('menu', $menu);
    }
}