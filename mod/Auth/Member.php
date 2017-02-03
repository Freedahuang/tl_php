<?php
/**
 * 后台台会员验证类 使用 session 做存储器
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */


/**
 * 后台会员验证类 用户的 登陆/退出 操作 命名空间 admin
 *
 * 获取的时候用 AuthMember::getInstance()->is_logined;<br />
 * 登陆 AuthMember::getInstance()->login($name, $password);<br />
 * 退出 AuthMember::getInstance()->logout();<br />
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class Auth_Member
{
    /** 
     * 用于判断是否登陆的标记
     *
     * @var number
     */
    public $is_logined = 0;

    /** 
     * 用户ID号
     *
     * @var number
     */
    public $id = 0;

    /** 
     * 用户Email账户
     *
     * @var email
     */
    public $name = '';

    /** 
     * 用户权限
     *
     * @var array
     */
    public $privileges = array();

    /** 
     * 用户别名
     *
     * @var string
     */
    public $alias = '';

    /** 
     * 用户所属部门ID
     *
     * @var number
     */
    public $id_department = 0;

    /** 
     * 用户所属权限ID
     *
     * @var number
     */
    public $id_privilege = 0;

    /**
     * 用来存储当前单件模式的实例对象
     *
     * @var obj
     */
    private static $_instance;

    /**
     * 引入的存储器 默认 session
     *
     * @var obj
     */
    private $_backend;

    /**
     * 构造器 设置存储源 默认 session 同时更新类里面的成员属性值 
     * 声明构造器 为私有 防止外部建立实例
     *
     */
    private function __construct()
    {
        //$this->_backend = new Cookie($namespace);
        $this->_backend = new TL_Session('member');
        $this->update();
    }

    /**
     * 获取类的实例对象 该类使用了单件模式 返回对象实例
     * 确保一个用户只有一个实例在运行
     *
     * @return obj
     */
    public static function getInstance()
    {
        if(!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 从存储器中获取各个成员对象的值 并更新
     *
     */
    private function update()
    {
        $this->is_logined       = $this->_backend->getValue('is_logined');
        $this->id               = $this->_backend->getValue('id');
        $this->name             = $this->_backend->getValue('name');
        $this->id_department    = $this->_backend->getValue('id_department');
        $this->id_privilege     = $this->_backend->getValue('id_privilege');
        $this->alias            = $this->_backend->getValue('alias');
        $privileges             = $this->_backend->getValue('privileges');
        $this->privileges       = TL_Tools::convertStringToArray($privileges);
    }

    /**
     * 登陆操作 根据用户名以及密码进行登陆验证操作
     *
     * @param string $name
     * @param string $password
     * @return boolean
     */
    public function login($name, $password)
    {
        $member = new Member(Member::getIdByName($name));
        $result = false;

        if ($member && $member->active && md5($password) == $member->password) {
            /*
            * 登陆成功后 把用户信息存入SESSION 并跳转至其他页面 
            * 以刷新获取变量
            */
            $p = new Privilege($member->id_privilege);
            $this->_backend->setValue('privileges', $p->privileges);
            $this->_backend->setValue('name', $member->name);
            $this->_backend->setValue('alias', $member->alias);
            $this->_backend->setValue('id', $member->id);
            $this->_backend->setValue('id_department', $member->id_department);
            $this->_backend->setValue('id_privilege', $member->id_privilege);
            $this->_backend->setValue('is_logined', 1);

            
            $this->update();
            $result = true;
        }
        return $result;
    }


    /**
     * 退出操作 更新成员属性值为空
     *
     */
    public function logout()
    {
        $this->_backend->setValue('privileges', '');
        $this->_backend->setValue('name', '');
        $this->_backend->setValue('alias', '');
        $this->_backend->setValue('id_department', 0);
        $this->_backend->setValue('id_privilege', 0);
        $this->_backend->setValue('id', 0);
        $this->_backend->setValue('is_logined', 0);

        $this->update();
    }
}

