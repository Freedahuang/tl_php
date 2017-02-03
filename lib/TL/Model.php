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
abstract class TL_Model
{
    protected $cls_name = '';
    /**
     * 引入的数据库操作对象
     *
     * @var obj
     */
    public $cls_db = null;

    /** 
     * 数据表的主键ID  无需在继承类再次定义 对应本类的 $cls_identifier 成员
     *
     * @var number
     */
    public $id = 0;

    /** 
     * 数据表的表名
     *
     * @var string
     */
    protected $cls_tbl = NULL;

    /* 
    *
    * 主键名
    */
    /** 
     * 数据表的主键标识码 默认 id 字段 用于创建本类实例时 自动获取数据表 并生成当前对象
     *
     * 自动处理的字段 date_add, date_upd, active 等 <br />
     * 只需在继承类指定 无需放入getFields()函数进行处理 <br />
     *
     * 如有更改则需在继承类重新指定 <br />
     *
     * @var string
     */
    protected $cls_identifier = 'id';
    
    protected $cls_list_limit = 30;
    protected $cls_list_page = 1;
    
    /** 
     * 非NULL属性字段 = 必需字段 如 oa_member 表里面 name, password
     * 在更新或插入时对其进行检查 防止误操作
     *
     * @var array
     */
    protected $cls_required = array();

    /**
     * Prepare fields for ObjectModel class (add, update)
     * All fields are verified (pSQL, intval...)
     *
     * @return array All object fields
     */
    public function getFields() { return array(); }

    public function preEdit() {}
    public function postEdit() {}
    
    /**
     * 创建对象 已数据库表为单位 如 oa_member 表 有 id, name, password 3个字段
     * 在继承类得到对象及其属性 $member->id, $member->name, $member->password
     *
     * @param integer $id Existing object id in order to load object (optional)
     * @param integer $id_lang Required if object is multilingual (optional)
     */
    public function __construct($identifier = null)
    {
        /**
         * 根据cls_identifier把数据对象实例化
         */
        if (!empty($identifier)) {
            $result = $this->getItemByCache($identifier);
            if (!empty($result)) {
                foreach ($result as $key => $value) {
                    if (property_exists($this, $key)) {
                        //$this->{$key} = stripslashes($value);
                        $this->{$key} = $value;
                    }
                }
                $this->{$this->cls_identifier} = $identifier;
            }
        }
        $this->cls_name = strtolower(get_class($this));
    }

    abstract public function getItemByParams($params);
    
    public function rawExecute($query)
    {
        return $this->cls_db->execute($query);
    }
    
    public function rawGet($query)
    {
        return $this->cls_db->getResult($query);
    }
    
    public function getLastQuery()
    {
        return $this->cls_db->getQuery();
    }
    
    /**
     * Save current object to database (add or update)
     *
     * @return boolean Insertion result
     */
    public function save()
    {
        //var_dump($this->{$this->cls_identifier});exit;
        if ($this->cls_identifier != 'id') {
            throw new Exception('can not save without id or not use for hash table');
        }

        $res = null;
        if (empty($this->{$this->cls_identifier})) {
            $res = $this->{$this->cls_identifier} = $this->insert();
        }
        else if ($this->update()) {
            $res = $this->{$this->cls_identifier};
        }
        return $res;
    }

    /**
     * 新增对象至数据库表 成功返回新增ID 失败返回false
     *
     * @return mixed Insertion result
     */
    public function insert()
    {
        $this->preEdit();
        $fields = $this->fieldsUpdate(TL_DB::$INSERT);
        $res = $this->cls_db->insert($this->cls_tbl, $fields);
        $this->postEdit();
        if ($res !== false) {
            $res = $this->{$this->cls_identifier};
            if ($this->cls_identifier == 'id') {
                $res = $this->cls_db->getInsertId();
            }
        }
        return $res;
    }
    
    public function add()
    {
        $this->{$this->cls_identifier} = $this->insert();
        return $this->{$this->cls_identifier};
    }
    
    public function edit()
    {
        return $this->update();
    }

    /* 如果有相关字段的话 自动更新时间 */
    protected function fieldsUpdate($type = null)
    {
        $fields = $this->getFields();
        foreach ($fields as $k=>$v) {
            if (is_string($v)) {
                $fields[$k] = $v;
            }
        }

        $date = date('Y-m-d H:i:s');
        if (property_exists($this, 'date_add') && empty($fields['date_add']) && $type == TL_DB::$INSERT) {
            $fields['date_add'] = $date;
            $this->date_add = $date;
        }
        if (property_exists($this, 'date_upd')) {
            $fields['date_upd'] = $date;
            $this->date_upd = $date;
        }
        return $fields;
    }

    /**
     * 更新当前对象在数据库的内容
     *
     * @return boolean Update result
     */
    public function update()
    {
        return $this->updByParams();
    }

    /**
     * 删除当前对象在数据库的内容
     *
     * @return boolean Deletion result
     */
    public function remove()
    {
        $res = $this->delete($this->{$this->cls_identifier});
        return $res;
    }

    /**
     * Delete current object from database
     *
     * @return boolean Deletion result
     */
    public function delete($id)
    {
        $res = $this->delByParams(array($this->cls_identifier => $id));
        return $res;
    }
    
    public function delByParams($params)
    {
        $this->preEdit();
        $res = $this->cls_db->del($this->cls_tbl, $params);
        $this->postEdit();
        return $res;
    }
    
    public function updByParams($params=null, $fields=null)
    {
        $this->preEdit();
        if (empty($fields)) {
            $fields = $this->fieldsUpdate();
        }
        if (empty($params)) {
            $params = array($this->cls_identifier => $this->{$this->cls_identifier});
        }
        
        $res = $this->cls_db->update($this->cls_tbl, $fields, 
            $params);
        $this->postEdit();
        return $res !== false ? true : false;
    }    

    /**
     * 激活或禁止当前记录
     *
     * @return boolean 
     */
    public function toggle($field = 'active')
    {
        if (!property_exists($this, $field))
            return false;
        
        $value = intval(!$this->{$field});
        $this->{$field} = $value;
        $fields = array(
            $field => $value
            );
        
        $res =  $this->updByParams(null, $fields);
        return $res;
    }
    
    public function getInsertId()
    {
        return $this->cls_db->getInsertId();
    }

    /* 
    * 验证必需自段不能为空 出错时 抛出异常
    * 如对 oa_member 表进行新增操作时 $member->name 不能为空
    * 
    */
    public function validateFields($halt = true)
    {
        foreach ($this->cls_required as $field){
            if (empty($this->{$field})){
                if ($halt) {
                    throw new Exception(get_class($this).' -> '.$field.' is empty');
                }
                return false;
            }
        }
    }
    
    public function toString()
    {
        $res = get_class($this);
        if (property_exists($this, 'name')) {
            $res = $this->name;
        }
        return $res;
    }
    
    public function setListPage($page)
    {
        $this->cls_list_page = $page;
    }
    
    public function setListLimit($limit)
    {
        $this->cls_list_limit = $limit;
    }
    
    public function getListLimit()
    {
        return $this->cls_list_limit;
    }
    
    protected function getSelect($fields, $where = null, $order = null, $limit = null, $group = null)
    {
        if ($order == null) {
            if (property_exists(get_class($this), 'sort')) {
                $order['sort'] = 'desc';
            } else if ($this->cls_identifier == 'id') {
                $order['id'] = 'desc';
            }
        }
        $query = TL_DB::parseSelect($fields, $this->cls_tbl, $where, $order, $limit, $group);
        
        return $this->rawGet($query);
    }

    public function getResult($where = null, $order = null, $limit = null, $group = null)
    {
        return $this->getSelect('*', $where, $order, $limit, $group);
    }

    public function getRow($where)
    {
        $result = $this->getResult($where, null, '1');
        return !empty($result) ? 
                    $result[0] : 
                    $result;
    }

    public function getTree($parent_id = 0, $level = 0)
    {
        $result = array();
        $where = array('parent_id' => intval($parent_id));

        foreach ($this->getResult($where) as $key => $item) {
            /* 如果是最上级部门 重置level */
            $level = !$item['parent_id'] ? 0 : $level;
            $result[$key] = $item;
            $result[$key]['level'] = $level;
            /* $level + 1 生成下级level 原有level保持不变 */
            $result[$key]['sub'] = $this->getTree($item['id'], $level + 1);
        }
        return $result;
    }
    
    public function beginTrans()
    {
        $this->cls_db->beginTrans();
    }
    
    public function execTrans()
    {
        $this->cls_db->execTrans();
    }
    
    /*
     * filter for search purpos
    */
    public function filterData($all, $filter=array())
    {
        $start = ($this->cls_list_page-1) * $this->cls_list_limit;
        $limit = $this->cls_list_limit;
    
        $res = array();
        $count_all = 0;
        foreach ($all as $val) {
            $count_filter = 0;
            foreach ($filter as $k => $v) {
                if (is_array($v) && in_array($val[$k], $v)) {
                    $count_filter++;
                }
                else {
                    list($k, $op) = @explode(':', $k, 2);
                    $boolean = $val[$k] == $v;
                    switch ($op) {
                        case '>':
                            $boolean = $val[$k] > $v;
                            break;
                        case '<':
                            $boolean = $val[$k] < $v;
                            break;
                        case '>=':
                            $boolean = $val[$k] >= $v;
                            break;
                        case '<=':
                            $boolean = $val[$k] <= $v;
                            break;
                    }
                    if ($boolean) {
                        $count_filter++;
                    }
                }
            }
            if ($count_filter == count($filter) && $count_all >= $start) {
                $res[] = $val;
            }
            if ($limit && count($res) == $limit) {
                break;
            }
            $count_all++;
        }
        return $res;
    }
    
}


