<?php
/**
 * 获取memcache 公共类
 *
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_MongoDB
{
    private static $INSTANCE = array();
    private $_inst = null;
    private $_db;
    private $_collection;
    
    public static function getInstance($type, $suffix='')
    {
        if (empty($suffix)) {$suffix = TL_Tools::getConfigSuffix();}
        $key = $type.'@'.$suffix;
        if (!array_key_exists($key, self::$INSTANCE)) {
            self::$INSTANCE[$key] = null;
            $self = new self($type, $suffix);
            self::$INSTANCE[$key] = $self;
        }
        return self::$INSTANCE[$key];
    }
    
    private function __construct($type, $suffix)
    {
        $filename = _ROOT_DIR_.'config'.DIRECTORY_SEPARATOR.
        'json'.DIRECTORY_SEPARATOR.'mongo'.DIRECTORY_SEPARATOR.
        $suffix;
        $message = '';
        if (file_exists($filename) && extension_loaded('MongoDB')) {
            $config = json_decode(file_get_contents($filename), true);
            if (isset($config[$type])) {
                $host = $config[$type]['host'];
                $name = $config[$type]['name'];
                $conf = sprintf('mongodb://%s',$host);
                $mongo = new MongoDB\Driver\Manager($conf);
                $this->_inst = $mongo;
                $this->_db = $name;
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

    public function selectCollection($tbl) {
        $this->_collection = $tbl;
        return $this;
    }

    public function insert($data) 
    {
        $bulk = new MongoDB\Driver\BulkWrite();
        $ed = 'getInsertedCount';
        $op = 'insert';
        $bulk->$op($data);
        $conn = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $res = $this->_inst->executeBulkWrite($this->_db.'.'.$this->_collection, $bulk, $conn);
        return array('ok'=>$res->$ed());
    }

    public function update($filter, $data)
    {
        $bulk = new MongoDB\Driver\BulkWrite();
        $ed = 'getModifiedCount';
        $op = 'update';
        $bulk->$op($filter, $data);
        $conn = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $res = $this->_inst->executeBulkWrite($this->_db.'.'.$this->_collection, $bulk, $conn);
        return array('ok'=>$res->$ed());
    }

    public function remove($params)
    {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->delete($params);
        $conn = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
        $res = $this->_inst->executeBulkWrite($this->_db.'.'.$this->_collection, $bulk, $conn);
        return array('ok'=>$res->getDeletedCount());
    }

    public function find($filter, $options) 
    {
        $query = new MongoDB\Driver\Query($filter, $options);
        $read = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);
        return $this->_inst->executeQuery($this->_db.'.'.$this->_collection, $query, $read);
    }

}


