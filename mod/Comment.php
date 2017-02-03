<?php

class Comment extends Ext_Model_DynamoDB
{    
    public $table = 'comment';
    public $primary = array('uid', 'time_add');
    public $fields = array(   
        'uid'       => 1           
        ,'time_add' => 1
        ,'cate'     => 1
        ,'id'       => 1 
        ,'content'  => 1 
        ,'reply'    => 0            
    );
    
    public function parseList($data)
    {
        foreach ($data as $k => $v) {
            unset($data[$k]['uid']);
        }
        return $data;
    }
    
    public function getAllByParams($params)
    {

        $agree = new Agree($params['hash_id']);
        $idx = $agree->get(($params['page']-1)*$params['limit'], $params['limit']);
        
        $keys = array();
        foreach (array_keys($idx) as $v) {
            $keys[] = array(
                    'uid'  => ['S' => substr($v, 0, strlen($v)-10)],
                    'time_add' => ['S' => substr($v, -10)]
                );
        }
        
        return $this->getData($keys);
    }
    
    public function getData($keys)
    {
        if (!empty($keys)) {
            $res = $this->get(array(
                'comment' => array(
                    'Keys' => $keys
                )
            ));

            if (!$res['ok']) {
                return null;
            }
        }
        
        $all = array();
        foreach ($keys as $v) {
            $key = $v['uid']['S'].$v['time_add']['S'];
            if (!isset($res['data']['comment'][$key])) {
                continue;
            }
            $reply = '';
            if (isset($res['data']['comment'][$key]['reply'])) {
                $reply = $res['data']['comment'][$key]['reply']['S'];
            }
            $user = new User($v['uid']['S']);
            $all[] = array(
              'key' => $key,
              'content' => $res['data']['comment'][$key]['content']['S'],
              'reply' => $reply,
              'time_add' => $v['time_add']['S'],
              'from' => $user->nickname,
              'gender' => $user->gender,
              'uid' => $v['uid']['S']
            );
        }
        return $all;
    }
}