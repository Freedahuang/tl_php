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

class Config_Controller extends Ext_Admin
{
    public $name = '系统参数';
 
    public $actions = array(
        /* 
        * manage包括新增和编辑操作 有tab表示将出现在tab菜单
        * auth表示动作权限 真 = 表示该动作需要权限 并验证系统参数权限列表是否有此ID
        */
        'list'      => array('name' => '管理系统参数', 'tab' => '系统参数管理', 'auth' => true),
        //'mail'      => array('name' => '管理邮件帐户', 'tab' => '邮件帐户管理', 'auth' => true),
        //'corp'      => array('name' => '管理推荐商家', 'tab' => '推荐商家管理', 'auth' => true),
        );

    public function afterEditSubmit($obj)
    {
        if (TL_Tools::isSubmit() && $obj->name == 'ss_filter_words') {
//             $time = time();
//             $curl = new TL_Curl();
//             $res = $curl->fetch('http://119.29.81.84:8849/update/'.$time.'/'.md5($time.'13760272067'), $obj->data);
        }
    }
    
    /* 菜单管理 需要永久保存的 单独用 CacheFile 类处理 */
    public function list2Action()
    {
        $option = TL_Tools::safeInput('option');
        
        $config = new Config();
        $list = $config->getAllByParams();

        if (TL_Tools::isSubmit() && $option) {
            $result = TL_Tools::safeInput($option, 'proto');
            TL_Config::setConfig($option, $result);
            //Config::setVal($option, $result);
        }
        
        $data = array(
            'company_notify' => TL_Config::getConfig('company_notify'),
            'company_bank_info' => TL_Config::getConfig('company_bank_info'),
            'input_email_admin' => TL_Config::getConfig('input_email_admin'),
            'input_key_admin' => TL_Config::getConfig('input_key_admin'),
        );
/*
        $data = Config::getVal(NULL);
*/
        foreach ($list as $v) {
            $data[$v['name']] = $v['data'];
        }
        $this->_view->assign('list', $data);
    }

    public function mailAction()
    {
        $option = TL_Tools::safeInput('option');

        if (TL_Tools::isSubmit() && $option) {
            $email = TL_Tools::safeInput('email');
            $pwd   = TL_Tools::safeInput('pwd');
            $result = array();
            foreach ($email as $key => $item){
                if ($key > 0 && $email[$key] && $pwd[$key]){
                    $result[$email[$key]] = array(
                        'pwd' => $pwd[$key],
                        'runing' => 0,
                        );
                }
            }

            TL_Config::setConfig($option, $result);
            //Config::setVal($option, $result, 'email');
        }

        $mail_server = TL_Config::getConfig('mail_server');
        //$mail_server = Config::getVal('mail_server', 'email');
        $this->_view->assign('mail_server', $mail_server);
    }

    public function corpAction()
    {
        $option = TL_Tools::safeInput('option');

        if (TL_Tools::isSubmit() && $option) {
            $corp_name = TL_Tools::safeInput('corp_name');
            $corp_link = TL_Tools::safeInput('corp_link');
            $corp_day = TL_Tools::safeInput('corp_day');
            $result = array();
            foreach ($corp_name as $key => $item){
                if ($key > 0 && $corp_name[$key] && $corp_link[$key]){
                    $result[] = array(
                        'name' => $corp_name[$key],
                        'link' => $corp_link[$key],
                        'day' => $corp_day[$key],
                        );
                }
            }

            TL_Config::setConfig($option, $result);
            //Config::setVal($option, $result, 'corp');
        }

        $corp_index = TL_Config::getConfig('corp_index');
        //$corp_index = Config::getVal('corp_index', 'corp');
        $this->_view->assign('corp_index', $corp_index);
    }


    public function adAction()
    {
        $this->_view->assign('bigad1', TL_Config::getValue('bigad1'));
        $this->_view->assign('bigad2', TL_Config::getValue('bigad2'));
        $this->_view->assign('bigad3', TL_Config::getValue('bigad3'));
    }

    public function uploadAction()
    {
        $option = TL_Tools::safeInput('option');

        if (TL_Tools::isSubmit()) {
            /* 处理图片 */
            $storage = new TL_Session('ad');
            $image   = $storage->getValue('temp');
            $link    = TL_Tools::safeInput('link');
            $desc    = TL_Tools::safeInput('desc');
            $result  = TL_Config::getValue($option);
            
            if ($link) {
                $result['link'] = $link;
                TL_Config::setValue($option, $result);
            }

            if ($desc) {
                $result['desc'] = $desc;
                TL_Config::setValue($option, $result);
            }

            /* 
            * 如果有则把图片从临时目录移到新目录
            * 并且计入数据库
            */
            if ($image) {
                /* 移除旧图片 */
                TL_FSO::deleteFile(_UPLOAD_DIR_.'ad'.DIRECTORY_SEPARATOR.$result['img']);
                
                $attachment = new TL_Attachment('ad');
                $attachment->moveTempFile();
                $result['img'] = $image['new_name'].$image['ext_name'];
                TL_Config::setValue($option, $result);
            }
            
            $message = array('execute ok! the item you submited is ', $option);
        }
        
        $this->_view->assign($option, TL_Config::getValue($option));
        $this->_view->assign('message', $message);
        $this->_view->assign('option', $option);
    }

}