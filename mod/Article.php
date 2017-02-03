<?php

class Article extends Ext_Model
{
    public $cls_tbl = 'article';
    public $id_article_category = 0;
    public $article_category;           // extra field for toArray()
    public $id_member = 0;
    public $uid = '';
    public $name = '';
    public $author = '';
    public $image = '';
    public $link = '';
    public $brief = '';
    public $day_on = '';
    public $comment_switch = 1;             // 评论开关，0=关闭评论，1=开放评论
    public $date_add;
    public $date_upd;
    public $active = 0;

    public function getFields()
    {
        $this->cls_required = array('name', 'id_article_category');
        parent::validateFields();

        $fields['id_article_category'] = intval($this->id_article_category);
        $fields['id_member'] = intval($this->id_member);
        $fields['uid'] = strval($this->uid);
        $fields['name'] = strval($this->name);
        $fields['author'] = strval($this->author);
        $fields['image'] = strval($this->image);
        $fields['link'] = strval($this->link);
        $fields['brief'] = strval($this->brief);
        $fields['day_on'] = strval($this->day_on);
        $fields['comment_switch'] = intval($this->comment_switch);

        return $fields;
    }

    public static function getForm($obj)
    {
        $ac = new Article_Category();
        if (empty($obj->day_on)) {
            $obj->day_on = date('Y-m-d H:i:s');
        }

        /* 设置表单参数 */
        $data = array(
            'type'    => strtolower(get_class()),  
            'check'   => false,
            'id'      => $obj->id,
            'field'   => array(
                'name'        => array('type' => 'input', 'value' => $obj->name),
                'author'      => array('type' => 'input', 'value' => $obj->author),
                'id_article_category' => array('type' => 'select', 'value' => $ac->getTree(), 'required' => $obj->id_article_category, 'label' => 'category'),
                'article_category'    => array('type' => 'hidden'),
                'link'        => array('type' => 'input', 'value' => $obj->link),
                'image'       => array('type' => 'file', 'value' => TL_Tools::base64EncodeUrl($obj->image)),
                'day_on'      => array('type' => 'time', 'value' => $obj->day_on),
                'comment_switch'  => array('type' => 'radio', 'value' => $obj->comment_switch),
                'brief'       => array('type' => 'tinymce', 'value' => $obj->brief),
                //'attachment'  => array('type' => 'label', 'value' => $attachment, 'label' => 'attachment'),
                ),
            );

        return $data;
    }

    public static function getTable()
    {
        /* 设置 table 需要显示的参数 */
        $data = array(
            'type'    => 'article', 
            'nopop'   => true,
            'field'   => array(
                'name'      => array('align' => 'left'),
                //'preview'   => array('width' => '80', 'title' => 'author'),
                'article_category'  => array('width' => '80'),
                'day_on'    => array('width' => '125'),
                'active'    => array('title' => 'status'),
                ),
            );
        return $data;
    }
    
    public function parseList($result)
    {
        $aa = new Article_Attachment();
        $ar = new Article_Region();
        foreach ($result as $key => $item) {
            $where = array('id_article' => $item['id']);
            $attachment = $aa->getListByParams($where);
            $result[$key]['attachment'] = $attachment;
            $region = $ar->getListByParams($where);
            $result[$key]['region'] = $region;
        }
        //print_r($result);exit;
        return $result;
    }
    
    public function outFields()
    {
        return array('id', 'name', 'date_upd', 'image', 'author', 'brief', 'link', 'article_category');
    }

    public function getAllByParams($params)
    {
        $where_date_up = '';
        if (isset($params['date_upd']) && !empty($params['date_upd'])) {
            $where_date_up = "AND a.date_upd > '$params[date_upd]'";
        }
        $sql = "SELECT a.id, a.name, a.date_upd, a.image FROM $this->cls_tbl a LEFT JOIN ".$this->cls_tbl."_region ar ON a.id = ar.id_article WHERE (ar.name = '$params[area]' OR ar.name is NULL) AND a.day_on <= '$params[day_on]' $where_date_up AND a.active = 1 ORDER BY a.date_upd DESC LIMIT 3";
        return $this->rawGet($sql);
    }

    public function apiOut()
    {
        return $this->toArray($this->outFields());
    }
}