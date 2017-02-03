<?php
/**
 * 通知 处理类 使用Email SMS等方式
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

/**
 * 处理通知 使用 Email SMS 等方式
 * 
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Notify
{
    /**
     * 引入的操作对象实例 如 Email SMS等
     *
     * @var obj
     */
    private $_notify = NULL;

    /**
     * 构造器 在这里引入Email
     *
     */
    public function __construct($obj)
    {
        $this->_notify = $obj;
    }

    /**
     * 发送操作
     *
     * @param array $data
     */
    public function send($data)
    {

        $_data = array(
            'subject' => $data['subject'],
            'body'    => $data['body'],
            'from'    => $data['from'],
            'alias'   => $data['alias'],
            'to'      => $data['to'],
            );

        return $this->_notify->send($_data);
    }
}

