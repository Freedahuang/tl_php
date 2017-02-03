<?php

class Data extends Ext_Model_Hash
{
    public $cls_num_tbl = 64;
    public $cls_dbname = 'tl_data';
    public $cls_tbl = 'data';
    public $cls_identifier = 'hash_id';     // 复合 ID，如 comment+article+id = ca123, not primary key.
    public $hash_id;                        
    public $time_add;                        
    public $uid;              
    public $memo;                           // 当前支持复合 ID 
                                            // ca=comment article 时 memo=审核通过时间戳； 
                                            // ac=argee comment 时 memo=uid 用户点赞记录
    
    public function getFields()
    {
        $this->cls_required = array($this->cls_identifier, 'time_add', 'uid');
        parent::validateFields();

        $fields['hash_id'] = strval($this->hash_id);
        $fields['time_add'] = intval($this->time_add);
        $fields['uid'] = strval($this->uid);
        $fields['memo'] = strval($this->memo);
        
        return $fields;
    }

    public function getAllByParams($params)
    {
        $this->setListLimit($params['limit']);
        $this->setListPage($params['page']);
        $order = array(
            'time_add' => 'DESC'
        );
        $idx = $this->getListByParams(array(
            'hash_id' => $params['hash_id'],
            'memo:<>' => ''
        ), $order, true);
        if (empty($idx)) {return null;}
        
        $keys = array();
        foreach ($idx as $v) {
            $keys[] = array(
                    'uid'  => ['S' => $v['uid']],
                    'time_add' => ['S' => strval($v['time_add'])]
                );
        }

        $comment = new Comment();
        return $comment->getData($keys);
    }
}