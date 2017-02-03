<?php

/**
* 控制器类 文章属性控制器 用来显示 
*
* 控制器类必须放置在制定目录下 如 /application/controller/
* 视图路径默认 /application/view/controller/action.tpl
* 并命名为 indexController.php 文件名
* IndexController 类名
*
* @author Liang Tan <tanjnr@gmail.com>
* @version 1.0
*/


class Article_Controller extends Ext_Admin
{
    public $name = '系统文章';
    
    public function preList()
    {
        $id_article_category = TL_Tools::safeInput('id_article_category');
        $option  = TL_Tools::safeInput('option');
        // 移动文章至指定目录
        if ($option == 'move' && $id_article_category){
            $ids_article = trim(TL_Tools::safeInput('ids_article'), ',');
            foreach (explode(',', $ids_article) as $val){
                $o = new Article($val);
                $o->id_article_category = $id_article_category;
                $o->save();
            }
            TL_Tools::redirect();
        }
        
        $article_category = new Article_Category();
        $this->_view->assign('article_category', $article_category->getTree());
        
        if (!empty($id_article_category)) {
            $this->where['id_article_category'] = $id_article_category;
            $this->_view->assign('id_article_category', $id_article_category);
        }

        $this->order = array('date_upd' => 'DESC');
    }

    public function preEdit($obj)
    {
        $storage = new TL_Session('ajax');
        $image = $storage->getValue('upload');
        if (TL_Tools::isSubmit() && !empty($image)) {
            $obj->image = $image;
            $storage->setValue('upload', null);
        }
        $obj->id_member = $this->account->id;
        return $obj;
    }
    
    public function afterEditSubmit($obj)
    {
        $this->_afterEdit($obj);
    }
    
    public function afterListSubmit($obj)
    {
        $this->_afterEdit($obj);
    }
    
    private function _afterEdit($obj)
    {
        if (TL_Tools::isSubmit() && $this->submit && $obj->id) {
            $name = $obj->active ? $obj->name : '';
            $search = new Search('article');
            $search->set($obj->id, $name);

//             $s3 = TL_S3::getInstance();
//             if ($obj->active > 0) {
//                 $dir = _ROOT_DIR_.'public'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR;
//                 $tpl = file_get_contents($dir.'article.htm');
//                 $css = file_get_contents($dir.'article.css');
//                 $tpl = str_replace('=css=', $css, $tpl);
                
//                 $dat = $obj->toArray();
//                 $dat['brief'] = TL_Tools::convertStringToChr($dat['brief']);
//                 foreach (array('name', 'day_on', 'author', 'brief') as $v) {
//                     $tpl = str_replace('='.$v.'=', $dat[$v], $tpl);
//                 }
                
//                 if ($s3->upload('article', $tpl, 'text/html', $obj->link) && empty($obj->link)) {
//                     $obj->link = $s3->getPath();
//                     $obj->save();
//                 }
//             } elseif (!empty($obj->link)) {
//                 $s3->delete($obj->link);
//                 $obj->link = '';
//                 $obj->save();
//             }
        }
    }
}