<?php
/**
 * Email 处理类
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

TL_Loader::loadFile('lib_swift_Swift');
TL_Loader::loadFile('lib_swift_Swift_Connection_SMTP');

/**
 * 处理Email 使用 Swift 使用 gmail 时 要开启php的 <b>open_ssl</b> 扩展
 *
 * $data = array(
 *     'subject' => 'Email收到没',
 *     'body'    => '<h1>mei</h1>',
 *     'from'    => '32639567@qq.com',
 *     'alias'   => 'leotan',
 *     'to'      => 'leogdar@msn.com',
 *     );
 * $email = new Email();
 * echo $email->send($data) ? 'ok' : 'fail';
 * 
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Email
{
    public static $smtp_arr = array(
            'gmail.com' => array('smtp'=>'smtp.gmail.com:465', 'secure'=>Swift_Connection_SMTP::ENC_SSL)
    );
    /**
     * 引入的操作对象实例 默认 swift
     *
     * @var obj
     */
    private $_email = NULL;

    /**
     * 构造器 在这里连接邮件服务器 并引用 swift 实例
     *
     */
    public function __construct($smtp, $name, $password)
    {
        list($smtp, $port) = @explode(':', $smtp);
        $secure = array(
            '465' => Swift_Connection_SMTP::ENC_SSL
            );
        try {
            $ssl = isset($secure[$port]) ? $secure[$port] : null;
            $email = new Swift_Connection_SMTP($smtp, $port, $ssl);
            if ($name) {
                $email->setUsername($name);
            }
            if ($password) {
                $email->setPassword($password);
            }
            $this->_email = new Swift($email);
        } catch (Swift_ConnectionException $e) {
            
        }
    }

    /**
     * 邮件发送操作
     *
     * @param array $data
     */
    public function send($data)
    {
        if (!$this->_email)
            return false;

        if (preg_match("/<.*?>/", $data['body']))
            $type = 'text/html';
        else
            $type = 'text/plain';

        $message = new Swift_Message($data['subject'], $data['body'], $type, 'base64');
        if (isset($data['replyto'])) {
            $message->setReplyTo($data['replyto']);
        }
        $address = new Swift_Address($data['from'], $data['alias']);

        $res = false;
        try
        {
            $this->_email->send($message, $data['to'], $address);
            $res = true;
        }
        catch(Swift_Exception $e)
        {
        }
        $this->_email->disconnect();
        return $res;
    }
}

