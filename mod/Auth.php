<?php

class Auth extends Ext_Model_Hash
{
    public $cls_num_tbl = 64;
    public $cls_dbname = 'tl_auth';
    public $cls_tbl = 'auth';
    public $cls_identifier = 'name';         // email/open_id/nickname/phone/...
    public $uid = '';                        // 设备标识，更换设备时需重新登陆，防止数据不同步
    
    public function getFields()
    {
        $this->cls_required = array($this->cls_identifier, 'uid');
        parent::validateFields();

        $fields['name'] = strval($this->name);
        $fields['uid'] = strval($this->uid);
        
        return $fields;
    }
}