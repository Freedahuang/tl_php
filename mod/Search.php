<?php

class Search extends Ext_Model_Redis
{
    /**
     * 
     * @param string $str strip punction in string
     */
    public function filterStr($str)
    {
        $__1 = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4','５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9', 'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E','Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J', 'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O','Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T','Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y','Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd','ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i','ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n','ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's', 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x', 'ｙ' => 'y', 'ｚ' => 'z','（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[','】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']','‘' => '[', '\'' => ']', '｛' => '{', '｝' => '}', '《' => '<','》' => '>','％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-','：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.', '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|', '”' => '"', '\'' => '`', '‘' => '`', '｜' => '|', '〃' => '"','　' => ' ');
        $__2 = array('①'=>'1','②'=>'2','③'=>'3','④'=>'4','⑤'=>'5','⑥'=>'6','⑦'=>'7','⑧'=>'8','⑨'=>'9','⑩'=>'10','⑴'=>'1','⑵'=>'2','⑶'=>'3','⑷'=>'4','⑸'=>'5','⑹'=>'6','⑺'=>'7','⑻'=>'8','⑼'=>'9','⑽'=>'10','一'=>'1','二'=>'2','三'=>'3','四'=>'4','五'=>'5','六'=>'6','七'=>'7','八'=>'8','九'=>'9','零'=>'0', '壹'=> '1', '贰'=> '2', '叁' => '3', '肆' => '4', '伍' => '5', '陆' => '6', '柒' => '7', '捌' => '8', '玖' => '9');
        $str = strtr($str, $__1);
        $str = strtr($str, $__2);
        return preg_replace("/[[:punct:]]/",'',strip_tags(html_entity_decode($str,ENT_QUOTES,'UTF-8')));
    }
    private function getKeys($str)
    {
        $str = $this->filterStr($str);
        $str = unicode_encode($str);
        $str = str_replace('&#', ' _u', $str);
        $str = str_replace(';', '', $str);
        $arr = explode(' ', $str);
        $arr = array_filter($arr);
        $res = array();
        foreach ($arr as $v) {
            $res[$this->_key.':'.$v] = 1;
        }
        return array_keys($res);
    }
    
    /**
     * 
     * @param string $str item name
     * @param string $val item id
     * @param string $act zadd/zrem
     */
    public function set($val, $str='')
    {
        $key2val = $this->_key.':'.$val;
        $old = $this->_redis->sMembers($key2val);
        if (!empty($old)) {
            foreach ($old as $key2word) {
                $this->_redis->zRem($key2word, $val);
            }
            $this->_redis->del($key2val);
        }
        
        if (empty($str)) {return;}
        $len = mb_strlen($str);
        $keys = $this->getKeys($str);
        foreach ($keys as $key2word) {
           $this->_redis->zAdd($key2word, $len, $val);
           $this->_redis->sAdd($key2val, $key2word);
        }
    }
    
    /**
     * 
     * @param string $str query name
     */
    public function ids($str, $offset=0, $count=5)
    {
        $key2get = $this->_key.':'.md5($str);
        $keys = $this->getKeys($str);
        $method = 'zinterstore';
        $res = $this->_redis->$method($key2get, $keys);
        if (!$res) {
            $method = 'zunionstore';
            $this->_redis->$method($key2get, $keys);
        }
        return array(
            'method' => $method,
            'result' => $this->_redis->zRangeByScore(
                $key2get, 
                '-inf', 
                '+inf', 
                array(
                    'limit' => array(
                        $offset, 
                        $count
                    )
                ))
            );
    }
}