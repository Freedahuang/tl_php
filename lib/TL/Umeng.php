<?php
TL_Loader::loadFile('lib_umeng_sdk_src_Umeng');

class TL_Umeng 
{
    static private $instance = array();
    private $umeng_server = NULL;
    private $_type = '';
    private $_production_mode = "false";
    private $IOS = array('key'=>''
                         ,'secret'=>'');
    private $AndroidTest = array('key'=>''
                             ,'secret'=>'');
    private $Android = array('key'=>''
                             ,'secret'=>'');
    
    /**
     * @param string $type 手机类型    IOS | Android 
     */
    private function __construct($type, $auto_test=true)
    {
        $this->_type = $type;
        $this->_production_mode = TL_Tools::getUmengSendMode();
        if ($this->_production_mode == 'false' && $type == 'Android' && $auto_test) 
        {
            $type = 'AndroidTest';
        }
        $os = $this->$type;
        $this->umeng_server = new Umeng($os['key'], $os['secret']);
    }
    
    static public function getInstance($type, $auto_test=true)
    {
        if (!isset(self::$instance[$type])) 
        {
            self::$instance[$type] = new self($type, $auto_test);
        }
        return self::$instance[$type];
    }
    
    /**
     * 单播
     */
    public function sendUnicast($params, $message)
    {//var_dump($this->_production_mode);
        $fun = 'send'.$this->_type.'Unicast';
        $res = $this->umeng_server->$fun($params, $message, $this->_production_mode);
        
        // 写入日志
        $this->UmengLog($params, $message, $res, __FUNCTION__);
    }
    
    /**
     * 广播
     */
    public function sendBroadcast($params, $message)
    {
        //var_export($params);
        //var_export($message);
        //var_export($this->_production_mode);
        
        $fun = 'send'.$this->_type.'Broadcast';
        $res = $this->umeng_server->$fun($params, $message, $this->_production_mode);
        
        // 写入日志
        $this->UmengLog($params, $message, $res, __FUNCTION__ );
        
        return true;
    }
    
    /**
     * 记录推广操作日志
     * @param array $params
     * @param array $message
     */
    private function UmengLog($params, $message, $res, $fun)
    {
        $path = array(
            'sendBroadcast'    => 'umeng'.DIRECTORY_SEPARATOR.'broadcast'
            ,'sendUnicast'  => 'umeng'.DIRECTORY_SEPARATOR.'unicast'.DIRECTORY_SEPARATOR.date('Y-m',time())
        );
        
        $time = time();
        $date = date('Y-m-d', $time);
        $data = date('Y-m-d H:i:s', $time).'===> ';
        foreach ($params as $key => $val) 
        {
            $data .= $key.' : '.$val.' | ';
        }
        foreach ($message as $key => $val)
        {
            $data .= $key.' : '.$val.' | ';
        }
        
        $data .= 'mode :　'.$this->_production_mode.' | ';
        $data .= 'result : '.$res;
        
        $data .= PHP_EOL;
        
        // win test 
        // TL_Tools::log($data, $date.'.log', 'e:/var/log/',$path[$fun]);
        
        // linux
         TL_Tools::log($data, $date.'.log', $path[$fun]);
    }
}
?>