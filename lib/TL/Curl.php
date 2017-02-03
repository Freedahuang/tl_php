<?php
/**
 * 用 curl 函数模拟浏览器 抓取网页内容 
 * 必要时使用代理
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */
if(!defined('_ROOT_DIR_')) {
    exit('TL_DB Access Denied');
}

/**
 * 抓取网页 
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */

class TL_Curl
{
    private $timeout = 5;
    private $cookie = '';
    private $ua = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36';
    private $ca = '';
    private $ct = 'application/x-www-form-urlencoded';
    private $px = '';

    public function setTimeout($second)
    {
        $this->timeout = $second;
    }
    
    public function setCookie($cookie)
    {
        $this->cookie = $cookie;
    }
    
    public function setUA($ua)
    {
        $this->ua = $ua;
    }
    
    public function setCA($ca)
    {
        $this->ca = $ca;
    }
    
    public function setCT($ct)
    {
        $this->ct = $ct;
    }
    
    public function setPX($px)
    {
        $this->px = $px;
    }
    
    public function exec($url, $method='GET', $data='')
    {
        // step 1: curl init
        $ch = curl_init();
        if ($this->px) {
            curl_setopt($ch, CURLOPT_PROXY, $this->px);
        }
        if ($this->timeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);       
        curl_setopt($ch, CURLOPT_USERAGENT, $this->ua);
        curl_setopt($ch, CURLOPT_HEADER, 1);  

        // step 2: handle method
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        $method = strtolower($method);
        if (method_exists($this, $method)) {
            if (is_array($data)) {
                $data = http_build_query($data);
            }
            $url = $this->$method($ch, $url, $data);
        }

        // step 3: handle scheme
        $arr = parse_url($url);
        $ref = $arr['scheme'].'://'.$arr['host'];
        curl_setopt($ch, CURLOPT_REFERER, $ref);
        
        if ($arr['scheme'] == 'https') {
            if (empty($this->ca)) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名
            } else {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书
                curl_setopt($ch, CURLOPT_CAINFO, $this->ca); // CA根证书（用来验证的网站证书是否是CA颁布）
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配
            }
        }
        
        // step 4: handle cookie
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($this->cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        }
        $res = curl_exec($ch);  
        
        // step 5: parse result
        $head_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        $body = '';
        if ($code == 200) {
            $body = substr($res, $head_size);
        } else {
            error_log('curl fetch content fail with url:'.$url);
            $err = curl_errno($ch);
            if ($err == 0) {
                error_log('code:'.$code);
                error_log('body:'.substr($res, $head_size));
            } else {
                error_log('err:'.$err);
                error_log('errmsg:'.$errmsg);
            }
        }
        curl_close ($ch);
        return $body;
    }

    public function fetch($url, $data='')
    {
        $method = 'POST';
        if (empty($data)) {
            $method = 'GET';
        }
        return $this->exec($url, $method, $data);
    }

    private function post(&$ch, $url, $data)
    {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: '.$this->ct.';charset=utf-8',
                'Content-Length: ' . strlen($data)
        ));
        // restore ct for next exec
        $this->setCT('application/x-www-form-urlencoded');
        return $url;
    }
    
    private function put(&$ch, $url, $data)
    {
        return $this->post($ch, $url, $data);
    }
    
    private function get(&$ch, $url, $data)
    {
        if (!empty($data)) {
            $url .= '?'.$data;
        }
        return $url;
    }
    
}
