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

class Banned_Controller extends Ext_Api_Auth_Redis_Rank
{
    public function addAction()
    {
        $target_user = new User($this->target_val);
        if (empty($target_user->uid)) {
            $this->output(103);
        }
        
        parent::addAction();
        
        if ($this->result['ok'] !== false) {
            $report = TL_Tools::safeInput('report');
            $message = TL_Tools::safeInput('message');
            if (!empty($message)) {
                $message = '因为['.$report.']举报'.$target_user->nickname.'：'.$message;
                $this->sendMessage('admin', 'dialog', $message);
            }
            // banned % 3 ,inavtive user * 24 hours
            if ($this->result['ok'] % 3 == 0) {
                $target_valid = $target_user->valid;
                $date = date('Y-m-d');
                if ($target_valid < $date) {
                    $target_valid = $date;
                }
                $target_user->valid = date('Y-m-d', strtotime($target_valid) + 24 * 3600);
                $target_user->update();
            }
        }
        $this->result['ok'] = 1;
    }
}