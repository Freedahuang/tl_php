<?php

/**
* 控制器类 区域控制器 用来显示 编辑区域
*
* 控制器类必须放置在制定目录下 如 /application/controller/
* 视图路径默认 /application/view/controller/action.tpl
* 并命名为 indexController.php 文件名
* IndexController 类名
*
* @author Liang Tan <tanjnr@gmail.com>
* @version 1.0
*/


class Ext_Admin_Tree extends Ext_Admin
{    
    public function afterListSubmit($obj)
    {
        $this->delCache($obj);
    }
    
    private function delCache($obj)
    {
        $key = strtolower($this->cls);
        $obj->delCache($key);
    }

    public function preEdit($obj)
    {
        $id         = TL_Tools::safeInput('id', 'digit');
        $parent_id  = TL_Tools::safeInput('parent_id', 'digit');
        $pos = false;
        if ($id) {
            $current = new $this->cls($id);
            $parent = new $this->cls($parent_id);
            if ($current->path && $parent->path) {
                $pos = strpos($parent->path, $current->path);
            }
        }
         
        if (($id && $parent_id == $id) || $pos !== false) {
            $this->submit = false;
        }
        return $obj;
    }
    
    public function preList()
    {
        $this->_custom_tpl = 'tree';
    }
    
    public function afterEditSubmit($obj)
    {
        if (TL_Tools::isSubmit() && $this->submit) {
            TL_Tree::updatePath($obj->id, $obj->parent_id, $this->cls);
            $this->delCache($obj);
        }
    }
}