<?php
/**
 * 获取memcache 公共类
 *
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_ShortUrl
{
    private $redis;
    
    function __construct()
    {
        $this->redis = TL_Redis::getInstance();
    } 
    
    public function code62($x)
    {
        $show='';
        while($x>0){
            $s=$x % 62;
            if ($s>35){
                $s=chr($s+61);
            }elseif($s>9&&$s<=35){
                $s=chr($s+55);
            }
            $show.=$s;
            $x=floor($x/62);
        }
        return $show;
    }
    
    public function gen($url)
    {
        $url=crc32($url);
        $result=sprintf("%u",$url);
        return $this->code62($result);
    }
    
    public function encode($str)
    {
        $key = $this->gen($str);
        if ($this->redis->set($key, $str)) {
            return $key;
        }
        return null;
    }
    
    public function decode($str)
    {
        return $this->redis->get($str);
    }
    
}


