<?php

/**
* 控制器类 部门控制器 用来显示 编辑部门
*
* 控制器类必须放置在制定目录下 如 /application/controller/
* 视图路径默认 /application/view/controller/action.tpl
* 并命名为 indexController.php 文件名
* IndexController 类名
*
* @author Liang Tan <tanjnr@gmail.com>
* @version 1.0
*/

class Message_Admin_Controller extends Ext_Admin
{
    public $name = '系统通知';
    
    public function preList()
    {
        $message_status = Ext_Tools::getSelect(Message_Admin::getStatus());
        $this->_view->assign('message_status', $message_status);
        
        $status_selected = TL_Tools::safeInput('status_selected', 'digit');
        $this->_view->assign('status_selected', $status_selected);
        $this->where['status'] = $status_selected;
        $this->group = 'uid';
        // SELECT * FROM tl_php.message_admin ma WHERE id=(select max(id) from tl_php.message_admin where uid=ma.uid) AND `status` <> '0' GROUP BY `uid` ORDER BY `id` asc LIMIT 0,30 
    }
    
    public function sendAction()
    {
        $this->editAction();
    }
    
    public function preEdit($obj)
    {
        if (TL_Tools::isSubmit() && $this->submit) {
            $content = TL_Tools::safeInput('content');
            if (empty($content)) {
                TL_Tools::redirect('page/tip/option/1002');
            }
            $time = time();
            if ($this->_action_uri == 'send') {
                $user = TL_Tools::safeInput('user');
                $list = TL_Tools::strToArray($user, '\n');
                foreach ($list as $k => $v) {
                    if ($this->sendMessage($content, $time, $k)) {
                        $new = new Message_Admin();
                        $new->status = 2;
                        $new->content = $content;
                        $new->uid = $k;
                        $new->time = $time;
                        $new->insert();
                    }
                }
                $this->submit = false;
                $this->_view->assign('message', count($list));
            } else {
                if (!$this->sendMessage($content, $time, $obj->uid)) {
                    $this->submit = false;
                }
                $this->_action_uri = 'add';
                $obj->status = 2;
                $obj->time = $time;
            }
        }
        return $obj;
    }
    
    private function sendMessage($content, $time, $uid)
    {
        $data = array(
            'cate' => 'dialog',
            'content' => $content,
            'from' => '系统客服',
            'uid' => 'admin'
        );
        $message = new Message($uid);
        $pack = serialize($data);
        return $message->add($pack, $time);
    }
}