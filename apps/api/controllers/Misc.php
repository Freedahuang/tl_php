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

class Misc_Controller extends Ext_Api
{
    public function searchAction()
    {
        $cate = TL_Tools::safeInput('cate');
        $value = TL_Tools::safeInput('value');
        if (!in_array($cate, $this->cates)) {
            $this->output(102);
        }
        if (empty($value)) {
            $this->output(103);
        }
        $search = new Search($cate);
        $ids = $search->ids($value, 0, 15);
        $res = array();
        foreach ($ids['result'] as $v) {
            $obj = new $cate($v);
            $res[] = $obj->apiOut();
        }
        $method = array(
            'zinterstore' => '1',
            'zunionstore' => '0'
        );
        $this->result = array(
            'ok' => $method[$ids['method']],
            'data' => $res
        );
    }

    public function areaAction()
    {
        $region = new Region();
        $data = $region->getCascade2();
        
        $this->result = array(
            'ok' => 1,
            'data' => $data
        );
    }

    public function captchaAction()
    {
        $email = TL_Tools::safeInput('user', 'email');
        $ok = 0;
        $info = array(
            '获取失败，服务器故障，或发送次数超限，请稍后再试',
            '验证码已发送至您的邮箱，请注意查收'
        );
        if (!empty($email)) {
            $user = new User();
            $pass = $user->getCaptcha($email);
//             $ok = 1;
//             $info[1] = $pass;
            if (!empty($pass)) {
                $subject = $pass.'是您本次登录的验证码';
                $body = '<p>首次于北京时间：<em>'.date('Y-m-d H:i:s', time()+24*3600).'</em> 前使用有效。</p><p>登录成功后，长期有效。系统邮件，请勿回复。</p>';
                $ok = intval($user->notify($email, $subject, $body));
            }
        }
        $this->result = array(
            'ok' => $ok,
            'data' => $info[$ok]
        );
    }
    
    public function verifyAction()
    {
        $email = TL_Tools::safeInput('user', 'email');
        $pass = TL_Tools::safeInput('pass', 'proto');
        if (!empty($email) && !empty($pass)) {
            $user = new User();
            $device = time();
            $uid = $user->chkCaptcha($device, $email, $pass);
            if (!empty($uid)) {
                $token = TL_Tools::encrypt($device.':'.$uid, 'encode');
                $this->result = array(
                    'ok' => 1,
                    'data' => $token
                );
            }
        }
    }
}
