<?php
/**
 * 获取memcache 公共类
 *
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Redis
{
    private static $INSTANCE = array();
    private $_inst = null;
    
    public static function getInstance($type='default', $suffix='')
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
        'json'.DIRECTORY_SEPARATOR.'redis'.DIRECTORY_SEPARATOR.
        $suffix;
        $message = '';
        if (file_exists($filename) && extension_loaded('Redis')) {
            $config = json_decode(file_get_contents($filename), true);
            if (isset($config[$type])) {
                $host = $config[$type]['host'];
                $port = $config[$type]['port'];
                $auth = $config[$type]['auth'];
                $this->_inst = new Redis();
                if ($this->_inst->connect($host, $port, 2.5)) {
                    if (!empty($auth) && !$this->_inst->auth($auth)) {
                        $message = 'Fail to auth, using <'.$auth.'>';
                    }
                } else {
                    $message = 'Fail to connect, make configure <right> and redis service <started>.';
                }
            } else {
                $message = 'Fail to find <'.$type.'> on configure file.';
            }
        } else {
            $message = 'No configure file or redis module support.';
        }
        if (!empty($message)) {
            $this->_inst = null;
            $message = 'Redis '.$type.'@'.$suffix.': '.$message;
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


