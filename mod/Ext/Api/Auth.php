<?php
/**
 * 控制器类 Factory 方法
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

/**
 * 用于实现一些控制器处理的公共方法，以及加载模板类(view)视图 其他控制器必须继承此类
 *
 * 控制器类必须放置在制定目录下 如 /application/controller/ <br />
 * 视图路径默认 /application/view/controller/action.tpl <br />
 * 并命名为 IndexController.php 文件名 <br />
 * IndexController 类名 <br />
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */

abstract class Ext_Api_Auth extends Ext_Api
{
    protected $user = null;
    
    public function preDispatch() 
    {
        parent::preDispatch();
        $token = TL_Tools::safeInput('token');
        $data = TL_Tools::encrypt($token, 'decode');
        list($device, $uid) = explode(':', $data);
        $this->user = new User($uid); 
        
        if (empty($this->user->uid)) {
            $this->output(402);
        }
        if (!empty($device) && !empty($user->device) && $device != $user->device) {
            $this->output(404);
        }
    }    
    
    /**
     * 
     * @param string $cate enum=[dialog(/reply), comment(/comment_edit), review]
     * @param string $content show on Notifications
     * @param string $from nickname
     * @return int 0=fail, or timestamp
     */
    protected function sendMessage($target_uid, $cate, $content, $time=0)
    {
        $message = new Message($target_uid);
        $from = $this->user->nickname;
        $uid = $this->user->uid;
        $data = compact('cate', 'content', 'from', 'uid');
        if ($time == 0) {
            $time = time();
        }
        $res = $message->add(serialize($data), $time);
        if ($res) {
            $res = $time;
        }
        return $res;
    }
}
