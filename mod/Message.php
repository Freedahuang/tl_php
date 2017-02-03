<?php

class Message extends Ext_Model_Redis
{    
    public function __construct($key)
    {
        parent::__construct($key);
    }
    
    public function add($val, $score='') 
    {
        $res = parent::add($val, $score);
        if ($res) {
            $this->_redis->expire($this->_key, 30 * 24 * 3600);
        }
        return $res;
    }
    
    public function countLimit()
    {
        $key = 'limit:'.$this->_key;
        $res = 0;
        if ($this->_redis->exists($key)) {
            $res = $this->_redis->get($key);
        }
        return $res;
    }
    
    public function updLimit($num=1)
    {
        $key = 'limit:'.$this->_key;
        $num = intval($num);
        if ($num != 0) {
            if ($this->_redis->exists($key)) {
                $num = $this->_redis->incrBy($key, $num);
            } else {
                $this->_redis->set($key, $num);
                $ttl = strtotime(date('Y-m-d 23:59:59'));
                $this->_redis->expireAt($key, $ttl);
            }
        }
        return $num;
    }
    
    // 客户端分段接收，全部接受完毕再提示
    public function get($offset=0, $count=5, $key='', $rev=false)
    {
        $res = array();
        $all = parent::get(0, $count);
        if (!empty($all)) {
            $this->_redis->multi(Redis::MULTI);
            foreach ($all as $k => $v) {
                $this->_redis->zRem($this->_key, $k);
                $data = unserialize($k);
                $data['time'] = $v;
                $res[] = $data;
            }
            $this->_redis->exec();
        }
        return $res;
    }
}