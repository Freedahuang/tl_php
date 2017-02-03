<?php

class Message_Admin extends Ext_Model
{
    public $cls_tbl = 'message_admin';
    public $status;         // 0=未读，1=已读，2=回复
    public $uid = '';
    public $content = '';
    public $time;

    public function getFields()
    {
        $this->cls_required = array('uid', 'content');
        parent::validateFields();

        $fields['status'] = intval($this->status);
        $fields['uid'] = strval($this->uid);
        $fields['content'] = strval($this->content);
        $fields['time'] = intval($this->time);

        return $fields;
    }
    
    public static function getStatus()
    {
        return array(
            '0' => TL_Tools::getLang('unread'),
            '1' => TL_Tools::getLang('readed'),
            //'2' => TL_Tools::getLang('replied') 
            );
    }

    public static function getSend($obj)
    {
        /* 设置表单参数 */
        $data = array(
            'type'    => strtolower(get_class()),
            'check'   => false,
            'id'      => $obj->id,
            'field'   => array(
                'content'  => array('type' => 'textarea', 'label' => 'message'),
                'user'	=> array('type' => 'textarea'),
                'empty' => array('type' => 'radio', 'value' => 0, 'label' => 'empty user')
            ),
        );
    
        return $data;
    }
    
    public static function getForm($obj)
    {
        $params = array('uid' => $obj->uid);
        $message_dialog = $obj->getAllByParams($params);
    	foreach ($message_dialog as $v) {
    		if ($v['status'] == 0) {
	    		$self = new self($v['id']);
	    		$self->status = 1;
	    		$self->save();
    		}
    	}

        $user = new User($obj->uid);
        
        /* 设置表单参数 */
        $data = array(
            'type'    => strtolower(get_class()),  
            'check'   => false,
            'id'      => $obj->id,
            'field'   => array(
                'nickname'  => array('type' => 'label', 'value' => $user->nickname),
                'uid'       => array('type' => 'hidden', 'value' => $obj->uid),
                'status'    => array('type' => 'hidden', 'value' => 2),
                'dialog'	=> array('type' => 'dialog', 'value' => $message_dialog),
            	'content'   => array('type' => 'textarea')
                ),
            );

        return $data;
    }

    public static function getTable()
    {
        /* 设置 table 需要显示的参数 */
        $data = array(
            'type'    => strtolower(get_class()),
            'add'     => 'remove',
            'field'   => array(
                'user'      => array('width' => '125'),
                'content'   => array('align' => 'left'),
                'date_add'  => array('width' => '125', 'title' => 'time')
                ),
            );
        return $data;
    }
    
    public function parseList($result)
    {
        foreach ($result as $k => $v) {
            $user = new User($v['uid']);
            $result[$k]['user'] = $user->nickname;
            $result[$k]['date_add'] = date('Y-m-d H:i:s', $v['time']);
        }
        return $result;
        
    }
}