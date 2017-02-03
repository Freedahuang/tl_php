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
class Ext_Model_Mongo
{   
    public static $INSERT = "insert";
    public static $DELETE = "delete";
    public static $UPDATE = "update";
    
    // 确保数据格式有效、统一
    protected $fields = array();  
    protected $dbname;
    protected $table;

    private $_inst;
    private $_list_limit = 30;
    private $_list_page = 1;
    
    public $data = array();
    
    public function __construct($id=null)
    {
        if (extension_loaded('Mongo')) {
            $mongo = TL_Mongo::getInstance($this->dbname);
        } else if (extension_loaded('MongoDB')) {
            $mongo = TL_MongoDB::getInstance($this->dbname);
        } else {
            throw new Exception('Mongo module not installed.');
        }
        $this->_inst = $mongo->selectCollection($this->table);
        if ($id) {
            $params = array('_id'=>$this->docId($id));
            $this->data = $this->getItemByParams($params);
        }
    }
    
    public function docId($id=null)
    {
        if (extension_loaded('Mongo')) {
            $id = new MongoId($id);
        } else if (extension_loaded('MongoDB')) {
            $id = new MongoDB\BSON\ObjectID($id);
        } 
        return $id;
    }
    
    public function getItemByParams($params)
    {
        $res = $this->getAllByParams($params, array(), 0, 1);
        return count($res) == 1 ? $res[0] : array();
    }
    
    // public function getListByParams($params=array(), $order=array())
    // {
    //     $start = ($this->_list_page - 1) * $this->_list_limit;
    //     if (empty($order)) {
    //         $order = array('_id'=>-1);
    //     }

    //     return $this->getAllByParams($params, $order, $start, $this->_list_limit);
    // }

    // public function getAllByParams($params=array(), $order=array(), $skip=0, $limit=0)
    // {
    //     $res = array();
    //     if (extension_loaded('Mongo')) {
    //         $cursor = $this->_inst->find($params)->sort($order)->skip($skip);
    //         if ($limit) {
    //             $cursor = $cursor->limit($limit);
    //         }
    //         foreach (iterator_to_array($cursor) as $k=>$v) {
    //             $v['_id'] = $k;
    //             $res[] = $v;
    //         }
    //     } else {
    //         $options = array('skip'=>$skip);
    //         if ($order) {
    //             $options['order'] = $order;
    //         }
    //         if ($limit) {
    //             $options['limit'] = $limit;
    //         }
    //         $cursor = $this->_inst->find($params, $options);
    //         foreach ($cursor as $v) {
    //             $vars = get_object_vars($v);
    //             $vars['_id'] = (string)$v->_id;
    //             $res[] = $vars;
    //         }
    //     }

    //     return $res;
    // }
    
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
        $op = self::$INSERT;
        if (isset($this->data['_id'])) {
            $this->data['_id'] = $this->docId($this->data['_id']);
            $op = self::$UPDATE;
        } else {
            $this->data['_id'] = $this->docId();
        }

        $res = $this->preEdit($op);
        if ($res['ok']) {
            try {
                if ($op == self::$INSERT) {
                    $res = $this->_inst->$op($this->data);
                } else {
                    $res = $this->_inst->$op(array('_id'=>$this->data['_id']), array('$set'=>$this->data));
                }
            } catch (Exception $e) {
                $res['errmsg'] = var_export($e, true);
            }
            if ($res['ok']) {
                $this->postEdit($op);
                $res['data'] = (string)$this->data['_id'];
            }
        }
        return $res;
    }
    
    public function getId()
    {
        if (isset($this->data['_id'])) {
            return (string)$this->data['_id'];
        }
        return null;
    }
    
    public function remove()
    {
        return $this->delete($this->data['_id']);
    }
    
    public function delete($id)
    {
        return $this->delByParams(array('_id'=>$this->docId($id)));
    }
    
    public function delByParams($params)
    {
        $res = $this->preEdit(self::$DELETE);
        if ($res['ok']) {
            $res = $this->_inst->remove($params);
            if ($res['ok']) {
                $this->postEdit(self::$DELETE);
            }
        }
        return $res;
    }
    
    protected function preEdit($op)
    {
        return array('ok'=>1, 'errmsg'=>null);
    }
    
    protected function postEdit($op)
    {
        
    }
    
    public function setListPage($page)
    {
        $this->_list_page = $page;
    }
    
    public function setListLimit($limit)
    {
        $this->_list_limit = $limit;
    }
    
    public function getListLimit()
    {
        return $this->_list_limit;
    }
}