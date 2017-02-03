<?php
/**
 * 获取memcache 公共类
 *
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Mongo
{
    private static $INSTANCE = array();
    private $_inst = null;
    
    public static function getInstance($type, $suffix='')
    {
        if (empty($suffix)) {$suffix = TL_Tools::getConfigSuffix();}
        $key = $type.'@'.$suffix;
        if (!array_key_exists($key, self::$INSTANCE)) {
            self::$INSTANCE[$key] = null;
            $self = new self($type, $suffix);
            self::$INSTANCE[$key] = $self->getInst();
        }
        return self::$INSTANCE[$key];
    }
    
    private function __construct($type, $suffix)
    {
        $filename = _ROOT_DIR_.'config'.DIRECTORY_SEPARATOR.
        'json'.DIRECTORY_SEPARATOR.'mongo'.DIRECTORY_SEPARATOR.
        $suffix;
        $message = '';
        if (file_exists($filename) && extension_loaded('Mongo')) {
            $config = json_decode(file_get_contents($filename), true);
            if (isset($config[$type])) {
                $host = $config[$type]['host'];
                $name = $config[$type]['name'];
                $rs = $config[$type]['rs'];
                $conf = sprintf('mongodb://%s/?replicaSet=%s',$host,$rs);
                $mongo = new MongoClient($conf);
                $this->_inst = $mongo->selectDB($name);
            } else {
                $message = 'Fail to find <'.$type.'> on configure file.';
            }
        } else {
            $message = 'No configure file or redis module support.';
        }
        if (!empty($message)) {
            $this->_inst = null;
            $message = 'Mongo '.$type.'@'.$suffix.': '.$message;
            if ($suffix != 'online') {
                throw new Exception($message);
            } else {
                error_log($message);
            }
        }
    }
    
    public function getInst()
    {
        return $this->_inst;
    }
}


