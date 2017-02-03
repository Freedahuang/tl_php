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

class Ajax_Controller extends Ext_Admin
{
    public $auth = false;

    /* 
    * iframe的异步上传
    * ==============================================================
    */
    public function uploadAction()
    {
        /* 
        * 上传的类型color=颜色/材质 image=图片 file=文件 
        * 存入对应的session命名空间中
        */
        $option   = TL_Tools::safeInput('option');
        $no_thumb = TL_Tools::safeInput('no_thumb');
        //$attachment = new TL_Attachment($option);
        $message = '';
        $storage = new TL_Session('ajax');
        
        /*
        * 初始的图片 外部需传入编码后的路径 自身上传的则无需编码
        */
        $image = TL_Tools::safeInput('image', 'proto');
        //$image = $image ? TL_Tools::base64DecodeUrl($image) : $attachment->getPrevious();
        $image = $image ? TL_Tools::base64DecodeUrl($image) : $storage->getValue('upload');
        
        if (TL_Tools::isSubmit() && !empty($_FILES['upload_file'])) {
            /*
            $thumb = $attachment->imageUpload($_FILES['upload_file'], ($no_thumb ? false : true));
            $image = $thumb ? (TL_FSO::isPic($thumb['tmp_name']) ? $thumb['tmp_name'] : $thumb['old_name']) : '';
            if (!$image)
                $message = 'attachment size wrong!';
                */
            
            $oss = TL_S3::getInstance();
            if ($oss->upload($option, $_FILES['upload_file']['tmp_name'], $_FILES['upload_file']['type'])) {
                $image = $oss->getPath();
                $storage->setValue('upload', $image);
            }
        }

        $this->_view->assign('message', $message);    
        $this->_view->assign('option', $option);
        $this->_view->assign('no_thumb', $no_thumb);
        $this->_view->assign('image', $image);
    }
}