<?php

class Article_Region extends Ext_Model
{
    public $cls_tbl = 'article_region';
    public $name = '';
    public $id_article = 0;
    public $brief = '';     // region info: 7,153
    public $date_add;
    public $date_upd;

    public function getFields()
    {
        $this->cls_required = array('id_article', 'name');
        parent::validateFields();

        $fields['name'] = strval($this->name);
        $fields['id_article'] = intval($this->id_article);
        $fields['brief'] = strval($this->brief);

        return $fields;
    }

    public static function getForm($obj)
    {
        $region = new Region();
        $res = $region->getCascade2();
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
                //'name'       => array('type' => 'file', 'value' => $obj->name, 'required' => true, 'label' => 'local'),
                'name'      => array('type' => 'cascade', 'value' => json_encode($res), 'required' => $obj->name, 'label' => 'region'),
                'id_article'    => array('type' => 'hidden', 'value' => $obj->id_article)
                )
            );
        return $data;
    }

    public function parseList($result)
    {
        foreach ($result as $key => $item) {
            $_arr = explode(',', $item['name']);
            $brief = '';
            foreach ($_arr as $k2 => $v2) {
                $region = new Region($v2);
                $brief .= $region->name;
            }
            $result[$key]['brief'] = $brief;
        }
        //print_r($result);exit;
        return $result;
    }
}