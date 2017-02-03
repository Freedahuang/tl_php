<?php

class Member_Log extends Ext_Model
{
    public $cls_tbl = 'member_log';
    public $id_member = 0;
    public $name = '';
    public $controller = '';
    public $action = '';
    public $link = '';
    public $ip = '';
    public $date_add;

    public function getFields()
    {
        $this->cls_required = array('name');
        parent::validateFields();

        $fields['id_member'] = intval($this->id_member);
        $fields['name'] = strval($this->name);
        $fields['controller'] = strval($this->controller);
        $fields['action'] = strval($this->action);
        $fields['link'] = strval($this->link);
        $fields['ip'] = strval($this->ip);

        return $fields;
    }

    public static function getTable()
    {
        /* 设置 table 需要显示的参数 */
        $data = array(
            'type'    => strtolower(get_class()),  
            'add'    => 'remove',
            'field'   => array(
                'name'       => array('width' => '125'),
                'controller' => array('width' => '125'),
                'action'      => array('width' => '125'),
                'link'           => array('align' => 'left'),
                'ip'           => array('width' => '125'),
                'date_add'   => array('width' => '140'),
                ),
            );
        return $data;
    }

}