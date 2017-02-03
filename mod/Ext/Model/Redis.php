<?php

class Ext_Model_Redis
{
    protected $_cls;
    protected $_redis;
    protected $_key;
    
    public function __construct($key)
    {
        $this->_cls = strtolower(get_class($this));
        $this->_redis = TL_Redis::getInstance($this->_cls);
        if (empty($key)) {
            throw new Exception('Empty key for '.$this->_cls);
        }
        $this->_key = $this->_cls.':'.$key;
    }
    
    public function incr($val, $score)
    {
        $res = $this->_redis->zIncrBy($this->_key, $score, $val);
        if ($res == 0) {
            $this->del($val);
        }
        return $res;
    }
    
    public function add($val, $score='')
    {
        if (empty($score)) {
            $score = time();
        }
        return $this->_redis->zAdd($this->_key, $score, $val);
    }
    
    public function del($val)
    {
        return $this->_redis->zRem($this->_key, $val);
    }
    
    public function get($offset=0, $count=5, $key='', $rev=false)
    {
        if (empty($key)) {
            $key = $this->_key;
        }
        $more = array('withscores' => TRUE);
        if ($count > 0) {
            $more['limit'] = array(
                $offset,
                $count
            );
        }
        if ($rev) {
            $res = $this->_redis->zRevRangeByScore($key,
                '+inf',
                '-inf',
                $more
                );
        } else {
            $res = $this->_redis->zRangeByScore($key,
                '-inf',
                '+inf',
                $more
            );
        }
        return $res;
    }
    
    public function count()
    {
        return $this->_redis->zCount(
            $this->_key,
            '-inf',
            '+inf'
            );
    }
    
    public function score($val)
    {
        return $this->_redis->zScore($this->_key, $val);
    }

    public function build($uid)
    {
        $user = new User($uid);
        if ($user->uid) {
            return array(
                'nickname' => $user->nickname,
                'avatar' => $user->avatar,
                'uid' => $user->uid,
                'time' => time()
            );
        } 
        return null;
    }
}