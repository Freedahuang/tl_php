<?php

class Ext_Model_Redis_Rank extends Ext_Model_Redis
{
    private $_rank;
    
    public function __construct($key)
    {
        parent::__construct($key);
        $this->_rank = 'rank:'.$this->_cls;
    }
    
    public function setKey($type)
    {
        $this->_rank .= ':'.$type;
        $this->_key .= ':'.$type;
    }
    
    public function add($val, $score='')
    {
        if (parent::add($val, $score)) {
            return $this->_redis->zIncrBy($this->_rank, 1, $val);
        }
        return false;
    }
    
    public function del($val)
    {
        if (parent::del($val)) {
            return $this->_redis->zIncrBy($this->_rank, -1, $val);
        }
        return false;
    }
    
    public function rank($offset, $count)
    {
        return $this->get($offset, $count, $this->_rank, true);
    }
    
    public function get($offset=0, $count=5, $key='', $rev=false)
    {
        $res = array();
        $all = parent::get($offset, $count, $key, $rev);
        foreach ($all as $k => $v) {
            $data = $this->build($k);
            $data['time'] = $v;
            if ($data) {
                $res[] = $data;
            }
        }
        return $res;
    }
}