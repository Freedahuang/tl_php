<?php

class Member extends Ext_Model
{
    public $cls_tbl = 'member';
    public $name = '';
    public $password = '';
    public $alias = '';
    public $id_privilege = 0;
    public $id_department = 0;
    public $active;
    public $date_add;
    public $date_upd;

    public function getFields()
    {
        $this->cls_required = array('name', 'password', 'alias');
        parent::validateFields();

        $fields['name'] = strval($this->name);
        $fields['password'] = md5($this->password);
        $fields['alias'] = strval($this->alias);
        $fields['id_privilege'] = intval($this->id_privilege);
        $fields['id_department'] = intval($this->id_department);

        return $fields;
    }

    public static function getForm($obj)
    {
        $department = new Department();
        $privilege = new Privilege();
        /* 设置表单参数 */
        $data = array(
            'type'    => strtolower(get_class()), 
            'check'   => false,
            'id'      => $obj->id,
            'field'   => array(
                'name'          => array('type' => 'input', 'value' => $obj->name, 'required' => true),
                'password'      => array('type' => 'input', 'value' => '', 'required' => true),
                'alias'         => array('type' => 'input', 'value' => $obj->alias),
                'id_privilege'  => array('type' => 'select', 'value' => $privilege->getAllByParams(null), 'required' => $obj->id_privilege, 'label' => 'privilege'),
                'id_department' => array('type' => 'select', 'value' => $department->getTree(), 'required' => $obj->id_department, 'label' => 'department'),
                ),
            );
        return $data;
    }

    public static function getTable()
    {
        /* 设置 table 需要显示的参数 */
        $data = array(
            'type'    => 'member', 
            'field'   => array(
                'name'       => array('width' => '125'),
                'alias'      => array('width' => '125'),
                'privilege'  => array(),
                'department' => array('width' => '125'),
                'active'     => array('title' => 'status'),
                ),
            );
        return $data;
    }

    /* 用于验证登陆 */
    public static function getIdByName($name)
    {
        $where = array('name' => $name);
        $self = new self();
        $result = $self->getRow($where);

        return isset($result['id']) ? 
                    $result['id'] : 
                    0;
    }
    
    public function toString()
    {
        $res = $this->name;
        if ($this->alias) {
            $res = $this->alias;
        }
        return $res;
    }
}