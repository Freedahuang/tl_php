<?php

class Ext_Model_Tree extends Ext_Model
{
    public $parent_id = 0;
    public $name = '';
    public $path = '';
    public $sort = 0;
    public $active;

    public function getFields()
    {
        $this->cls_required = array('name');
        parent::validateFields();

        $fields['parent_id'] = intval($this->parent_id);
        $fields['name'] = strval($this->name);
        $fields['path'] = strval($this->path);
        $fields['sort'] = intval($this->sort);

        return $fields;
    }

    public static function getForm($obj)
    {
        /* 设置表单参数 */
        $data = array(
            'type'    => strtolower(get_class($obj)), 
            'check'   => true,
            'id'      => $obj->id,
            'field'   => array(
                'name'        => array('type' => 'input', 'value' => $obj->name),
                'parent_id'   => array('type' => 'select', 'value' => $obj->getTree(), 'required' => $obj->parent_id, 'label' => 'parent'),
                'sort'        => array('type' => 'input', 'value' => $obj->sort),
                ),
            ); 
        return $data;
    }

    public function rebuildPath()
    {
        $all = $this->getAllByParams();
        $cls = get_class($this);
        foreach ($all as $k => $v) {
            TL_Tree::updatePath($v['id'], $v['parent_id'], $cls);
        }
    }
}