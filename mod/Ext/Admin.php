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
define('_THICKBOX_', '?keepThis=true&amp;TB_iframe=true&amp;width=400&amp;height=500&amp;');

abstract class Ext_Admin extends TL_Controller
{
    /**
     * 当前控制器的动作属性集 由子类覆盖 用以确定 执行动作的权限 格式如下
     *
     * 'list'     => array('name' => '管理文章', 'tab' => '文章管理', 'auth' => true), <br />
     * 'add'      => array('name' => '新增文章', 'tab' => NULL, 'auth' => true), <br />
     * 'edit'     => array('name' => '编辑文章', 'tab' => NULL, 'auth' => true), <br />
     *
     * @var array
     */
    //public $actions = array();
    public $actions = array(
        /* 
        * manage包括新增和编辑操作 有tab表示将出现在tab菜单
        * auth表示动作权限 真 = 表示该动作需要权限 并验证会员权限列表是否有此ID
        */
        'list'      => array('name' => '管理', 'tab' => '管理', 'auth' => true),
        'add'       => array('name' => '新增', 'tab' => NULL, 'auth' => true),
        'edit'      => array('name' => '编辑', 'tab' => NULL, 'auth' => true),
        );

    /**
     * 账户信息 判断用户是否登陆 以及权限等等
     *
     * @var obj
     */
    public $account = NULL;

    /**
     * 控制器权限 判断动作是否需要验证
     *
     * @var boolean
     */
    public $auth = true;
    
    public $cls = '';
    
    public $where = array();

    public $order = array();
    
    public $group = '';
    
    public $submit = true;

    /** 
     * 执行控制器指定动作 dispatch 中的 action 之前的默认动作 preDispatch 
     * 由继承类实现 如再继承 TL_Controller_Front_Admin 进行一些统一处理 如验证权限
     * 然后所有的后台控制器 再继承至此类
     */
    public function preDispatch() {

        // 获取前台预制 cookie 并认证
        /*
        $cookie = new TL_Cookie('admin');
        if ($cookie->getValue('auth') != 'gogogo') {
            //exit;
        }
        */
        if (TL_Tools::getConfigSuffix() == 'online') {
            //echo 'not allowed';
            exit;
        }

        $this->account = Auth_Member::getInstance();

        /* 后台操作要求强制登陆 */
        if ($this->auth && !$this->account->is_logined && $this->_controller_uri != 'auth') {
            TL_Tools::redirect('auth/login');
        }

        if ($this->auth && !empty($this->actions[$this->_action_uri]['auth'])) {
            $this->authAction();
        }

        $this->_view->assign('controller_uri', $this->_controller_uri);
        $this->_view->assign('action_uri', $this->_action_uri);
        $this->_view->assign('account', $this->account);
        
        $this->cls = strtolower(str_replace('_Controller', '', get_class($this)));
        
        $menu = Menu::getMenu($this->account->privileges);
        $this->_view->assign('top_nav', $menu[1]);
        
        if ($this->_action_uri == 'login' || $this->_controller_uri == 'page') {
        } else {
            $member_log = new Member_Log();
            $member_log->id_member = $this->account->id;
            $member_log->name = $this->account->name;
            $member_log->controller = $this->_controller_uri;
            $member_log->action = $this->_action_uri;
            $member_log->link = $_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING'];
            $member_log->ip = TL_Tools::getClientIP();
            $member_log->save();
        }
    }

    private function authAction()
    {
        /* 判断用户是否有执行当前控制器动作的权限 */
        $controller_privilege = $this->account->privileges[$this->_controller_uri];

        if (empty($controller_privilege) || !in_array($this->_action_uri, $controller_privilege)) {
            TL_Tools::redirect('page/tip/option/9001');
        }

        /* 判断当前控制器中哪些 动作tab 应该被载入 */
        $result = array();
        foreach ($this->actions as $key => $item) {
            if ($item['tab'] && in_array($key, $controller_privilege)) {
                $result[$key] = $item;
            }
        }
        $this->_view->assign('actions', $result);
    }
    
    public function addAction()
    {
        $this->editAction();
    }
    
    public function preEdit($obj){return $obj;}
    public function afterEditSubmit($obj){}
    public function editAction()
    {
        $id = TL_Tools::safeInput('id');
        $obj = new $this->cls($id);
        $obj = $this->preEdit($obj);
        
        if (TL_Tools::isSubmit() && $this->submit) {
            $obj->fromArray($this->parseFormKV(Ext_Tools::safePost())); 
            $this->_view->assign('message', $obj->{$this->_action_uri}());
        }

        $this->afterEditSubmit($obj);
        
        $method = 'get'.ucfirst($this->_action_uri);
        if (!method_exists($this->cls, $method)) {
            $method = 'getForm';
        }
        $call = call_user_func(array($this->cls, $method), $obj);
        $this->_view->getForm($call);
    }
    
    protected function parseFormKV($data)
    {
        if (!isset($data['form_kv'])) {
            return $data;
        }
        foreach ($data['form_kv'] as $v) {
            $data[$v] = array();
            foreach ($data['v_'.$v] as $idx => $val) {
                $key = trim($data['k_'.$v][$idx]);
                $val = trim($val);
                if (!empty($val)) {
                    $data[$v][$key] = $val;
                }
            }
        }
        return $data;
    }
    
    protected function getList($obj){
        $res = $obj->getListByParams($this->where, $this->order, true, $this->group);
        if (empty($res)) {
            $res = array();
        }
        return $res;
    }
    public function preList(){}
    public function afterListSubmit($obj){}
    public function listAction()
    {        
        $option = TL_Tools::safeInput('option', 'alpha', 'toggle');
        $id     = TL_Tools::safeInput('id');
        $this->preList();
        
        if (TL_Tools::isSubmit() && $id && $this->submit) {
            $obj = new $this->cls($id);
            $obj->$option();
            $this->afterListSubmit($obj);
            TL_Tools::redirect();
        }
        
        if (method_exists($this->cls, 'getTable')) {
            $this->_view->getTable(call_user_func(array($this->cls, 'getTable')));
        }

        $obj = new $this->cls();
        $obj->setListPage($this->_list_page);
        
        $q = TL_Tools::safeInput('q');
        if (!empty($q)) {
            $this->where['name:LIKE'] = '%'.$q.'%';
            /*
            if (in_array($this->cls, explode(',', Search::$TYPE))) {
                $offset = ($this->_list_page - 1) * $obj->getListLimit();
                $search = new Search($this->cls);
                $ids = $search->get($q, $offset, $obj->getListLimit());
                if ($ids) {
                    unset($this->where['name:LIKE']);
                    $this->where['id'] = $ids;
                    $this->order['LENGTH(`name`)'] = 'ASC';
                    $obj->setListPage(1);
                }
            }
            */
            $this->_view->assign('q', $q);
        }
                
        if (property_exists($this->cls, 'parent_id') && property_exists($this->cls, 'path')) {
            $this->_view->assign('tree', $obj->getTreeByCache(strtolower($this->cls)));
        } else {
            $this->_view->assign('list', $this->getList($obj));
        }
        
        $this->_view->assign('limit', $obj->getListLimit());
    }
    
    public function delAction()
    {
        $id  = TL_Tools::safeInput('id', 'digit');
        $option = TL_Tools::safeInput('option');
        if (empty($option)) {
            $option = $this->_controller_uri;
        }
        $option = TL_Tools::parseHyphenString($option);
        if (TL_Tools::isSubmit() && $id) {
            $obj = new $option($id);
            $obj->remove();
        }
        TL_Tools::redirect();
    }

    public function pdAction()
    {
        $obj = new $this->cls();
        if ($obj->push2redis(_REDIS_ADMIN_, 'online')) {
            TL_Tools::redirect('page/tip/option/1001');
        } else {
            TL_Tools::redirect('page/tip/option/1002');
        }
    }

    public function error($code) 
    {
        parent::error($code);
        TL_Tools::redirect('page/tip/option/1002');
    }
}