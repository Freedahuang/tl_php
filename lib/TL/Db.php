<?php
/**
 * DB数据库处理类 该类对数据库公共处理函数进行封装 
 * 并提供一个接口
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
 * 把类申明为抽象，由其他具体类扩展具体操作方法，如 MySQL 类 
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
abstract class TL_Db
{
    protected $_link = null;
    public $log_debug = false;

    public static $INSERT = "INSERT";
    public static $DELETE = "DELETE";
    public static $UPDATE = "UPDATE";

    public static $BEGIN = "BEGIN";
    public static $END = "END";
    public static $COMMIT = "COMMIT";
    public static $ROLLBACK = "ROLLBACK";
    
    protected $_query = '';
    
    private static $INSTANCE = null;

    public static function getInstance($type, $config)
    {
        if (self::$INSTANCE == null) {
            $cls = 'TL_Db_'.ucfirst($type);
            self::$INSTANCE = new $cls($config);
        }
        return self::$INSTANCE;
    }
    
    public function insert($tbl, $vals)
    {
        $result = 0;
        if ($this->op(self::$INSERT, $tbl, $vals)){
            $result = $this->getInsertID();
        }
        return $result;
    }

    public function update($tbl, $vals, $where)
    {
        return $this->op(self::$UPDATE, $tbl, $vals, $where);
    }

    public function del($tbl, $where)
    {
        return $this->op(self::$DELETE, $tbl, null, $where);
    }

    /**
     * sql 执行 插入 删除 更新 操作
     *
     * @param $type|string 操作类型
     * @param $tbl|string 表名称
     * @param $vals|array 值数组
     * @param $where|array 条件数组
     */
    public function op($type, $tbl, $vals = null, $where = null)
    {
        $query = "";
        if ($type == self::$INSERT){
            $query = 'INSERT IGNORE INTO '.$tbl.' (';

            $key_list = '';
            $val_list = '';
            foreach ($vals as $key => $val){
                $key_list .= '`'.$key.'`,';
                $val_list .= "'".$val."',";
            }

            $query .= rtrim($key_list, ',').')';
            $query .= ' VALUES (';
            $query .= rtrim($val_list, ',').')';
        }
        else if ($type == self::$UPDATE){
            $query = 'UPDATE '.$tbl.' SET ';

            foreach ($vals as $key => $val){
                $query .= "`".$key."` = '".$val."',";
            }

            $query = rtrim($query, ',');
        }
        else if ($type == self::$DELETE){
            $query = 'DELETE FROM '.$tbl.' ';
        }

        if ($where){
            $query .= self::parseWhere($where);
        } else if ($type != self::$INSERT) {
            throw new TL_Exception('db op need condition.');
        }
        //print_r($query);exit;
        return $this->execute($query);
    }

    // where 条件数组转化 Array -> String
    public static function parseWhere($where)
    {
        if (!$where){
            return '';
        }
        $result = ' WHERE 1';
        foreach ($where as $key => $val){
            $operator = '=';
            $field = $key;
            if (strpos($key, ':') !== false) {
                list($field, $operator) = explode(':', $key, 2);
            }
            // 如果值是数组
            if (is_array($val)){
                $result .= ' AND `'.$field.'` in ('."'".implode("','", $val)."')";
            }
            else {
                if (is_string($val)) {
                    $val = str_replace("'", "\'", $val);
                }
                // 如果数值中含` 分隔符 表示使用了MYSQL/数据库函数则另外处理
                // 如 $where = array('@FUNC@30:<' => "DATEDIFF('{$today}', `date_add`)");
                // 是否以FUNC::开头
                $func = substr($field, 0, 6);
                if ($func == '@FUNC@'){
                    $result .= " AND ".substr($field, 6)." ".$operator." ".$val;
                }
                else {
                    $result .= ' AND `'.$field.'` '.$operator." '".$val."'";
                }
            }
        }
        //print_r($result);exit;
        //file_put_contents(_UPLOAD_DIR_.'sdsdf.txt', "\n\n".$result, FILE_APPEND);
        return $result;
    }

    public static function parseOrder($order)
    {
        if (!$order){
            return '';
        }
        $result = '';
        foreach ($order as $key => $val){
            // 如果是有()的 表示应用了函数
            $pos = strpos($key, '(');
            if ($pos === false){
                $result .= ' `'.$key.'` '.$val.',';
            }
            else {
                $result .= ' '.$key.' '.$val.',';
            }
        }
        if ($order){
            $result = ' ORDER BY'.rtrim($result, ',').' ';
        }
        return $result;
    }
    
    // query 条件数组转化
    // to-do 用数组传 $where 变量更灵活
    public static function parseSelect($fields, $tbl, $where = null, $order = null, $limit = null, $group = null)
    {
        $query = 'SELECT '.$fields.' FROM '.$tbl.' ';
        $where = self::parseWhere($where);
        $order = self::parseOrder($order);
        $group = $group ? ' GROUP BY '.$group : '';
        $limit = $limit ? ' LIMIT '.$limit : '';

        return $query.$where.$group.$order.$limit;
    }



    /**
     * 函数getResult，执行数据库SELECT查询，返回结果集
     *
     * 函数接受1个参数$query <br />
     * 函数执行SELECT操作 如 Db::getInstance()->getResult("SELECT FROM ..."); <br />
     *
     * 函数执行成功返回数组结果集 如<br />
     * array(<br />
     *   [0] => array('id' => '1', 'name' => 'test'),<br />
     *   [1] => array('id' => '2', 'name' => 'text'),<br />
     * )<br />
     * 失败返回 空数组 array()<br />
     *
     * @param string $query
     * @return array
     */
    abstract public function getResult($query);

    // getResult 的别名
    public function fetchAll($query)
    {
        return $this->getResult($query);
    }

    /**
     * 函数getRow，执行数据库SELECT查询，返回结果
     *
     * 函数接受1个参数$query <br />
     * 函数执行SELECT操作 如 Db::getInstance()->getRow("SELECT FROM ..."); <br />
     *
     * 函数执行成功返回数组结果集 如 <br />
     * array('id' => '1', 'name' => 'test'); <br />
     *
     * 失败返回 空数组 array() <br />
     *
     * @param string $query
     * @return array
     */
    abstract public function getRow($query);

    // getRow 的别名
    public function fetchRow($query)
    {
        return $this->getRow($query);
    }
    
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * 获取最近一次执行INSERT操作的ID
     */
    abstract public function getInsertId();

    /**
     * 函数excuteQuery，执行数据库查询
     *
     * 函数接受1个参数$query  <br />
     * 函数执行所有的query操作 可单独执行 如 Db::getInstance()->excuteQuery("INSERT INTO ...");  <br />
     * 也可以作为继承类其他函数的结果集调用 如 getData 方法等  <br />
     *
     * 函数执行成功返回true或者对 SELECT，SHOW，EXPLAIN 或 DESCRIBE 语句返回一个资源标识符 <br />
     * 失败返回 false  <br />
     *
     * @param string $query
     * @return false|true/resource
     */
    abstract public function execute($query);

    /**
     * Returns the text of the error message from previous database operation
     */
    abstract public function getError();

    /**
     * 写入数据库出错信息
     *
     */
    public function logError($query)
    {
        $error  = "\r\n\r\n#Query: ".$query;
        $error .= "\r\n\r\n#Error: ".wordwrap($this->getError(), 80, "\r\n");
        $error .= "\r\n\r\n#Date: ".date('r');    
        $error .= "\r\n=============================================================";

        self::logFile($error, 'error');
    }

    /**
     * 写入数据库需要Debug的语句
     *
     */
    public function logDebug($query)
    {
        $debug  = "\r\n\r\n#Query: ".$query;
        $debug .= "\r\n\r\n#Date: ".date('r');    
        $debug .= "\r\n=============================================================";

        self::logFile($debug, 'debug');
    }

    public function getLogDir()
    {
        return sys_get_temp_dir().DIRECTORY_SEPARATOR;
    }

    private function logFile($data, $type)
    {
        $file = $this->getLogDir().'db.'.$type.'.log';
        TL_FSO::createFile($file, $data, 'a+');
    }
    
    public function beginTrans(){}
    public function execTrans(){}
    public function failedTrans(){}

}