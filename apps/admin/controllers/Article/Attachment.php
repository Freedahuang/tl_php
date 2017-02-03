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

class Article_Attachment_Controller extends Ext_Admin
{
    public $name = '文章附件管理';
    public $auth = true;

    public $actions = array(
        /* 
        * manage包括新增和编辑操作 有tab表示将出现在tab菜单
        * auth表示动作权限 真 = 表示该动作需要权限 并验证用户权限列表是否有此ID
        */
        'add'      => array('name' => '新增文章附件', 'tab' => NULL, 'auth' => false),
        'edit'     => array('name' => '编辑文章附件', 'tab' => NULL, 'auth' => false),
        'del'      => array('name' => '删除文章附件', 'tab' => NULL, 'auth' => false),
        );

    public function addAction()
    {
        // 提交处理
        $this->save();
    }

    /* 编辑动作 */
    public function editAction()
    {
        $this->save();
    }

    private function save()
    {
        $id     = TL_Tools::safeInput('id', 'digit');
        $obj    = new Article_Attachment($id);
        $option = TL_Tools::safeInput('option', 'digit');

        if (!$obj->id) {
            $obj->id_article = $option;
        }
        $message = '';

        if (TL_Tools::isSubmit() && $obj->id_article)
        {
            /* 处理图片 */
            $storage = new TL_Session('ajax');
            $image   = $storage->getValue('upload');

            /* 
            * 如果有则把图片从临时目录移到新目录
            * 并且计入数据库
            */
            if ($image) {
                /*
                $attachment = new TL_Attachment('local');
                $attachment->moveTempFile();
                $name = $image['new_name'].$image['ext_name'];
                */
                $obj->name = $image;
                $storage->setValue('upload', null);
            }

            $obj->brief  = TL_Tools::safeInput('brief');
            
            $obj->save() ? 
                $message = array('execute ok! the item you submited is ', $obj->name) :
                $message = 'execute failed! please contact administrator!';
        }

        $this->_view->getForm(Article_Attachment::getForm($obj));    
        $this->_view->assign('message', $message);    
        $this->_view->assign('option', $option);    
    }

    public function delAction()
    {
        $id  = TL_Tools::safeInput('id', 'digit');
        $obj = new Article_Attachment($id);
        $obj->remove();
        TL_Tools::redirect();
    }

    public function uploadAction()
    {
        $tmp = 'attachment'.DIRECTORY_SEPARATOR.date("Ym");
        //print_r($upload_dir);exit;
        $upload_dir = TL_FSO::getMultDir(_UPLOAD_DIR_, $tmp);

        $obj        = new TL_Attachment('attachment');
        $attachment = $obj->upload($_FILES['uploaded_file'], $upload_dir);
        $image      = $attachment ? _UPLOAD_URI_.'attachment/'.date("Ym").'/'.$attachment['new_name'].$attachment['ext_name'] : '';

        $this->_view->assign('image', $image);    
    }

}