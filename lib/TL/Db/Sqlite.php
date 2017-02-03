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

TL_Loader::loadFile('classes_adodb5_adodb.inc');

/**
 * AdoDbMySQL 框架的数据库操作类.
 *
 $query = array(
"
CREATE TABLE StudentInfo
(
ID INTEGER PRIMARY KEY AUTOINCREMENT,
Name varchar ( 10 ) ,
Address varchar ( 15 )
)
",

"
INSERT INTO StudentInfo
(
Name,
Address
)
VALUES
(
 '乔峰' ,
 '丐帮'
)
",

);

$db = TL_Db::getSqlite(_ROOT_DIR_.'test.db');

foreach ($query as $item) {
    $db->executeQuery($item);
}


print_r($db->getResult("
SELECT * FROM StudentInfo
"));
exit;
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Db_Sqlite extends TL_Db
{
    public function __construct($db)
    {
        $this->_link = &ADONewConnection('pdo');
        $this->_link->Connect('sqlite:'.$db); 
        $this->_link->SetFetchMode(ADODB_FETCH_ASSOC); 
    }

    /**
     * 断开与数据库的连接 
     *
     */
    public function __destruct()
    {
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
        return !empty($result) ? $result[0] : $result;
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
        return false;
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
            throw new TL_Exception("you get a mysql query error! please check log file!");
        }

        $this->_query = $query;
        return $result;
    }

    public function beginTransaction()
    {
        $this->_link->BeginTrans(); 
    }

    public function commit()
    {
        $this->_link->CommitTrans(); 
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

}
