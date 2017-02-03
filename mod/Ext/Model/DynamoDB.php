<?php
/**
 * 抽象的业务处理类 主要由业务类继承对数据库表进行操作
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */


if(!defined('_ROOT_DIR_')) {
    exit('Access Denied');
}

/**
 * 用于实现一些业务处理的公共方法 如加载对象
 *
 * 注意：
 * 继承类扩展是添加的对应数据库表字段的对象属性必须指定与数据库字段一致的默认值
 * 如NULL值等
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class Ext_Model_DynamoDB extends Ext_Model
{   
    public static $PUT = "put";
    public static $DEL = "del";
    
    // 确保数据格式有效、统一
    protected $primary = array();  
    protected $table;

    private $_inst;
    private $_list_limit = 30;
    private $_list_page = 1;
    
    public $data = array();
    
    public function __construct()
    {
        $this->_inst = TL_DynamoDB::getInstance($this->table);
        parent::__construct();
    }
    
    public function parseList($data)
    {
        return $data;
    }
    
    /**
     * keys = array('record')
     */
    public function get($keys)
    {
        $res = array('ok' => 1);
        try {
            $all = $this->_inst->get($keys)->get('Responses');
            $data = array();
            foreach ($all as $tbl => $item){
                $obj = new $tbl();
                foreach ($item as $k => $v) {
                    $idx = '';
                    foreach($obj->primary as $_) {
                        $idx .= $v[$_]['S'];
                    }
                    $data[$tbl][$idx] = $v;
                }
            }
            $res['data'] = $data;
        } catch (Exception $e) {
            $res['ok'] = 0;
            $res['errmsg'] = $e->getMessage();
        }
        return $res;
    }
    
    public function query($keys)
    {
        $res = array('ok' => 1);
        try {
            $all = $this->_inst->query($keys)->get('Items');
            $data = array();
            foreach ($all as $item){
                $tmp = array();
                foreach ($item as $k => $v) {
                    $tmp[$k] = array_values($v)[0];
                }
                $data[] = $tmp;
            }
            
            $res['data'] = $this->parseList($data);
        } catch (Exception $e) {
            $res['ok'] = 0;
            $res['errmsg'] = $e->getMessage();
        }
        return $res;
    }
    
    public function fromArray($data)
    {
        $this->data = array_merge($this->data, $data);
    }
    
    private function verifyFields()
    {
        foreach ($this->fields as $k=>$v) {
            if ($v && !isset($this->data[$k])) {
                $err = get_class($this).' -> '.$k.' is empty';
                throw new Exception($err);
            }
        }
    }
    
    public function edit()
    {
        return $this->save();
    }
    
    public function add()
    {
        return $this->save();
    }
    
    public function save()
    {
        $this->verifyFields();
        $res = $this->preEdit(self::$PUT);
        
        if ($res['ok']) {
            //var_dump($this->data);exit;
            try {
                $this->_inst->save($this->data);
            } catch (Exception $e) {
                $res['ok'] = 0;
                $res['errmsg'] = $e->getMessage();
            }
            if ($res['ok']) {
                $this->postEdit(self::$PUT);
            }
        }
        return $res;
    }
    
    public function delete($key)
    {
        return $this->delByParams($key);
    }
    
    public function delByParams($params)
    {
        $res = $this->preEdit(self::$DEL);
        if ($res['ok']) {
            try {
                $this->_inst->delete($params);
            } catch (Exception $e) {
                $res['ok'] = 0;
                $res['errmsg'] = $e->getMessage();
            }
            if ($res['ok']) {
                $this->postEdit(self::$DEL);
            }
        }
        return $res;
    }
    
    public function preEdit()
    {
        return array('ok'=>1, 'errmsg'=>null);
    }
}