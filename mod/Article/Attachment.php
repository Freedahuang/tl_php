<?php

class Article_Attachment extends Ext_Model
{
    public $cls_tbl = 'article_attachment';
    public $name = '';
    public $id_article = 0;
    public $brief = '';
    public $date_add;
    public $date_upd;

    public function getFields()
    {
        $this->cls_required = array('name', 'id_article', 'brief');
        parent::validateFields();

        $fields['name'] = strval($this->name);
        $fields['id_article'] = intval($this->id_article);
        $fields['brief'] = strval($this->brief);

        return $fields;
    }

    public static function getForm($obj)
    {
        /* 设置表单参数 */
        $data = array(
            'type'    => strtolower(get_class()), 
            'check'   => false,
            'id'      => $obj->id,
            'field'   => array(
                 /*  
                 * 'required' => false 表示使用缩略图 TL_Config::getThumbFormat() 
                 * true 表示使用原图
                 */ 
                'name'       => array('type' => 'file', 'value' => $obj->name, 'required' => true, 'label' => 'local'),
                'brief'      => array('type' => 'input', 'value' => $obj->brief, 'required' => true, 'label' => 'description'),
                )
            );
        return $data;
    }
}