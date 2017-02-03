<?php
/**
 * MySQL数据库处理类 使用AdoDb进行封装
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

TL_Loader::loadFile('lib_adodb5_adodb.inc');

/**
 * AdoDbMySQL 框架的数据库操作类.
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Db_Mysql extends TL_Db
{
    /**
     * 数据库连接函数 由父类 Db 继承提供些基本的数据库参数
     *
     * @return resource $this->_link
     */
    public function __construct($config)
    {
        $this->_link = ADONewConnection('mysqli');
        $this->_link->Connect($config['host'], $config['user'], $config['pwd']);  
        $this->_link->SetFetchMode(ADODB_FETCH_ASSOC); 
        /* UTF-8 support */
        $this->_link->Execute("SET NAMES 'utf8'");
    }

    /**
     * 断开与数据库的连接 
     *
     */
    public function __destruct()
    {
        $this->_link->Disconnect();
        $this->_link = null;
    }
    
    /**
     * 获取 query 语句查询结果集 以数组的形式返回 多维数组
     *
     * @param string $query
     * @return array
     */
    public function getResult($query)
    {
        $result = array();
        if ($data = $this->execute($query)) {
            /* 把取到的值放入数组 */
            while (!$data->EOF) {
                $result[] = $data->fields;
                $data->MoveNext();
            }
        }
        return $result;
    }
    /**
     * 获取 query 语句查询结果 以数组的形式返回 一维数组
     *
     * @param string $query
     * @return array
     */
    public function getRow($query)
    {
        $result = $this->getResult($query.' LIMIT 1');
        return !empty($result) ? 
                    $result[0] : 
                    $result;
    }

    /**
     * 获取 最近一个 INSERT 语句执行成功后的插入ID
     * 如果有 则返回值 否则返回false
     *
     * @return mixed number|boolean
     */
    public function getInsertId()
    {
        if ($this->_link) {
            return $this->_link->Insert_ID();
        }
        return 0;
    }

    /**
     * 执行 query 语句 该类其他方法调用此函数执行
     * 成功返回执行的结果 否则返回false
     *
     * @param string $query
     * @return mixed result|boolean
     */
    public function execute($query)
    {
        /* 如果$query语句中 有debug字样(一般在头部注释里) 则输入语句以备查验 */
        if ($this->log_debug && preg_match("/debug/", $query)) {
            $this->logDebug($query);
        }

        $result = $this->_link->Execute($query);
        if (!$result) {
            $this->logError($query);
            throw new TL_Exception(
                "you get a mysql query error! please check log file in dir :".$this->getLogDir()
                );
        }

        $this->_query = $query;
        return $result;
    }
    
    /**
     * 获取 数据库 的错误信息
     *
     * @return string
     */
    public function getError()
    {
        return $this->_link->ErrorMsg();
    }
    
    public function beginTrans()
    {
        $this->_link->StartTrans();
    }

    public function execTrans()
    {
        $this->_link->CompleteTrans();
    }
    
    public function failedTrans()
    {
        return $this->_link->HasFailedTrans();
    }
}
