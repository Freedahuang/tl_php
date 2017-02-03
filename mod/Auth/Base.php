<?php
/**
 * 前台会员验证类 使用 cookie 做存储器
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

/**
 * 前台会员验证类 用户的 登陆/退出 操作 以及 购物车 命名空间 account
 *
 * 获取的时候用 AuthAccount::getInstance()->is_logined;<br />
 * 登陆 AuthAccount::getInstance()->login($name, $password);<br />
 * 退出 AuthAccount::getInstance()->logout();<br />
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class Auth_Base
{
    /** 
     * 用于判断是否登陆的标记
     *
     * @var number
     */
    public $is_logined = 0;
    public $namespace = '';

    /** 
     * 用户ID
     *
     * @var mix
     */
    public $identifier = 'id';
    public $primary = '';
    
    /** 
     * 用户名称
     *
     * @var string
     */
    public $name = '';
    
    /**
     * 引入的存储器 默认 cookie
     *
     * @var obj
     */
    public $_backend;

    function __construct($namespace, $expire=NULL)
    {
        $this->namespace = $namespace;
        $this->_backend = new TL_Cookie($namespace, '', $expire);
        $this->update();
    }

    /**
     * 从存储器中获取各个成员对象的值 并更新
     *
     */
    public function update()
    {
        $this->preUpdate();
        $this->is_logined   = $this->_backend->getValue('is_logined');
        $this->primary      = $this->_backend->getValue('primary');
        $this->name         = $this->_backend->getValue('name');
    }


    /**
     * 登陆操作 根据用户名以及密码进行登陆验证操作 并写入联盟ID值
     *
     * @param string $name
     * @param string $password
     * @return boolean
     */
    public function login($login_name, $login_pwd)
    {
        
        $result = false;
        $cls = TL_Tools::parseHyphenString($this->namespace);
        $item = $cls::authLogin($login_name, $login_pwd);
        if (isset($item['name'])) {
            /*
            * 登陆成功后 把用户信息存入SESSION 并跳转至其他页面 
            * 以刷新获取变量
            */
            $this->preLogin($login_name, $login_pwd);
            $this->_backend->setValue('name', $item['name']);
            $this->_backend->setValue('primary', $item[$this->identifier]);
            $this->_backend->setValue('is_logined', 1);
            
            $this->update();
            $result = true;
        }
        return $result;
    }

    public function preLogin($login_name, $login_pwd){}
    public function preLogout(){}
    public function preUpdate(){}
    
    /**
     * 退出操作 更新成员属性值为空
     *
     */
    public function logout()
    {
        $this->preLogout();
        $this->_backend->setValue('name', '');
        $this->_backend->setValue('primary', '');
        $this->_backend->setValue('is_logined', 0);
        $this->update();
    }

}

