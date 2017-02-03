<?php

class Department extends Ext_Model
{
    public $cls_tbl = 'department';
    public $name = '';
    public $parent_id = 0;
    public $path = '';
    public $phone = '';
    public $brief = '';
    public $sort = 0;
    public $active;

    public function getFields()
    {
        $this->cls_required = array('name');
        parent::validateFields();

        $fields['name'] = strval($this->name);
        $fields['parent_id'] = intval($this->parent_id);
        $fields['path'] = strval($this->path);
        $fields['phone'] = strval($this->phone);
        $fields['brief'] = strval($this->brief);
        $fields['sort'] = intval($this->sort);

        return $fields;
    }

    public static function getForm($obj)
    {
        /* 设置表单参数 */
        $data = array(
            'type'    => strtolower(get_class()), 
            'check'   => true,
            'id'      => $obj->id,
            'field'   => array(
                'name'        => array('type' => 'input', 'value' => $obj->name),
                'phone'       => array('type' => 'input', 'value' => $obj->phone),
                'parent_id'   => array('type' => 'select', 'value' => $obj->getTree(), 'required' => $obj->parent_id, 'label' => 'parent'),
                'sort'        => array('type' => 'input', 'value' => $obj->sort),
                'brief'       => array('type' => 'textarea', 'value' => $obj->brief),
                ),
            );
        return $data;
    }
}