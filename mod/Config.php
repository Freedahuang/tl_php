<?php

class Config extends Ext_Model
{
    public $cls_tbl = 'config';
    public $cls_identifier = 'name';
    public $name = '';
    public $data = '';
    public $date_upd = '';
    
    public function getFields()
    {
        $this->cls_required = array('name');
        parent::validateFields();

        $fields['name'] = strval($this->name);
        $fields['data'] = strval($this->data);
        
        return $fields;
    }


    public static function getForm($obj)
    {
        $name_type = 'input';
        if ($obj->name) {
            $name_type = 'hidden';
        }
        /* 设置表单参数 */
        $data = array(
                'type'    => strtolower(get_class()),
                'check'   => false,
                'id'      => $obj->name,
                'field'   => array(
                        'name'       => array('type' => $name_type, 'value' => $obj->name, 'required' => true),
                        //'data'       => array('type' => 'textarea', 'value' => $obj->data),
                ),
        );
        if ($obj->name) {
            $data['field']['title'] = array('type' => 'label', 'value' => TL_Tools::getLang($obj->name));
        }
        $data['field']['data'] = array('type' => 'textarea', 'value' => $obj->data);
        return $data;
    }
    
    public static function getTable()
    {
        /* 设置 table 需要显示的参数 */
        $data = array(
                'type'    => strtolower(get_class()),
                //'add'     => 'remove',
                'production_update' => true,    // 生产环境更新
                'field'   => array(
                        'name'    => array('align'=>'left', 'width' => '180'),
                        'brief'    => array('align'=>'left'),
                        'date_upd'    => array('width' => '125'),
                        'active'     => array('title' => 'status', 'option' => 'remove'),
                ),
        );
        return $data;
    }
    
    public function add()
    {
        parent::add();
        return true;
    }
    
    public function parseList($result)
    {
        foreach ($result as $k=>$v)
        {
            $result[$k]['id'] = $v['name'];
            $result[$k]['name'] = TL_Tools::getLang($v['name']).'['.$v['name'].']';
            $result[$k]['brief'] = TL_Tools::truncateStr($v['data'], 140);
            $result[$k]['active'] = 1;
        }
        return $result;
    }
    
    public function pdParams()
    {
        return array('name:LIKE'=>'pd_%');
    }
}