<?php
/**
 * 抽象的业务处理类 主要由业务类继承对数据库表进行操作
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */


if(!defined('_ROOT_DIR_')) {
    exit('Access Denied');
}

//TL_Loader::loadFile('config_define.db');

/**
 * 用于实现一些业务处理的公共方法 如加载对象
 *
 * 注意：
 * 继承类扩展是添加的对应数据库表字段的对象属性必须指定与数据库字段一致的默认值
 * 如NULL值等
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class Ext_Model extends TL_Model
{
    protected $cls_dbname = 'default';
    protected $cls_dbtype = 'mysql';
    protected $cls_cache; 
    
    /* 扩展 Model 以期使用数据库 */
    public function __construct($identifier=null)
    {
        $message = 'Db configure file not exists or fail to find <'.$this->cls_dbname.'> on configure file.';
        $config = $this->getDbConfig($this->cls_dbname);
        if (!empty($config)) {
            $this->cls_db = TL_Db::getInstance($this->cls_dbtype, $config);
            $this->cls_tbl = $config['name'].'.'.$this->cls_tbl;
        } 
        if ($this->cls_db == null) {
            $message = 'Db '.$this->cls_dbtype.'@'.$this->cls_dbname.': '.$message;
            if ($suffix != 'online') {
                throw new Exception($message);
            } else {
                error_log($message);
            }
        }
        
        $this->cls_cache = TL_Cache::getInstance();
        
        parent::__construct($identifier);
    }
    
    protected function getDbConfig($type)
    {
        $filename = _ROOT_DIR_.'config'.DIRECTORY_SEPARATOR.
        'json'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.
        TL_Tools::getConfigSuffix();
        $message = '';
        if (file_exists($filename)) {
            $config = json_decode(file_get_contents($filename), true);
            if (isset($config[$type])) {
                return $config[$type];
            }
        }
        return array();
    }

    public function getItemByCache($identifier)
    {
        if (empty($identifier)) {
            return false;
        }
        // related to postEdit
        $func = 'getItemByParams';
        $params = array($this->cls_identifier=>$identifier);
        return $this->getByCache($func, $params);
    }
    
    public function getTreeByCache()
    {
        return $this->getByCache('getTree');
    }
    
    protected function getByCache($func, $params=null, $ttl=0)
    {
        $key = $this->cls_tbl.':'.md5(serialize($params));
        $val = $this->cls_cache->get($key);
        if (!$val) {
            $val = $this->$func($params);
            $this->cls_cache->set($key, $val);
            if ($ttl > 0) {
                $this->cls_cache->ttl($key, $ttl);
            }
        }
        return $val;
    }

    public function getItemByParams($params)
    {
        $res = $this->_getListByParams($params, null, '1');
        return isset($res[0])? $res[0]:null;
    }

    public function getFieldByParams($field, $params)
    {
        $result = $this->getItemByParams($params);
        return isset($result[$field]) ?
                    $result[$field] : null;
    }

    public function getAllByParams($params)
    {
        return $this->getListByParams($params, null, null);
    }

    public function parseList($result){return $result;}

    // this could be overwrite, may affect internal use.
    public function getListByParams($params=array(), $order=null, $limit=true, $group=null)
    {
        return $this->_getListByParams($params, $order, $limit, $group);
    }
    
    // this used by getItemByParams
    private function _getListByParams($params=array(), $order=null, $limit=true, $group=null)
    {
        if ($limit === true) {
            $start = ($this->cls_list_page-1) * $this->cls_list_limit;
            $limit = $start.','.$this->cls_list_limit;
        }

        $result = $this->getResult($params, $order, $limit, $group);
        foreach ($result as $k => $v) {
            foreach ($v as $kk => $vv) {
                // !in_array avoiding fetch data recursively
                if (substr($kk, 0, 3) == 'id_' && !in_array($kk, array_keys($params))) {
                    $field = substr($kk, 3);
                    $table = TL_Tools::parseHyphenString($field);
                    $vv > 0 || $vv = null;
                    $obj = new $table($vv);
                    $result[$k][$field] = $obj->toString();
                }
            }
        }
        $result = $this->parseList($result);
        return $result;
    }
    
    public function getAllByCache($params, $ttl)
    {
        $func = 'getAllByParams';
        return $this->getByCache($func, $params, $ttl);
    }
    
    public function delCache($key)
    {
        $key = $this->cls_tbl.':'.md5(serialize($key));
        $this->cls_cache->del($key);
    }

    public function postEdit()
    {
        if (!empty($this->{$this->cls_identifier})) {
            $key = $this->{$this->cls_identifier};
            $this->delCache($key);
            $this->push2redis(_REDIS_ADMIN_);
        }
    }
    
    public function redis2push()
    {
        $redis = TL_Redis::getInstance(_REDIS_ADMIN_);
        $val = $redis->get('pd:'.$this->cls_name);
        return unserialize($val);
    }
    
    public function push2redis($type, $suffix='')
    {
        if (method_exists($this, 'pdParams')) {
            $all = $this->getAllByParams($this->pdParams());
            $redis = TL_Redis::getInstance($type, $suffix);
            $redis->set('pd:'.$this->cls_name, serialize($all));
            return true;
        }
        return false;
    }

    // 从数组中导入类属性
    public function fromArray($data)
    {
        foreach ($this as $k=>$v) {
            if (isset($data[$k])) {
                $this->$k = $data[$k];
            }
        }
    }

    /**
     * 
     * @param string $fields specified output
     * @return array
     */
    public function toArray($fields=false)
    {
        // 排除保留字段
        $result = array();
        foreach ($this as $k=>$v) {
            if (substr($k, 0, 4) == 'cls_') {
                continue;
            }
            if ($k == 'id' && $this->cls_identifier != 'id') {
                continue;
            }
            if ($fields == false || in_array($k, $fields)) {
                $result[$k] = $v;
            }
        }
        return $result;
    }
    
    public function apiOut()
    {
        return $this->toArray();
    }

//     public function selfNotify($company_id, $data, $notify)
//     {
//         $company = new Company($company_id);
//         if (isset($company->extra_info['mail'])) {
//             $p = $company->extra_info['mail']['password'];
//             $s = $company->extra_info['mail']['smtp'];
//             $u = $company->extra_info['mail']['user'];
//             $this->notify($notify, $data, $s, $u, $p);
//         } else {
//             $this->notify($notify, $data);
//         }
//     }

    public function notify($to, $subject, $body)
    {
        $cfg = Ext_Tools::getCfg();
        $email = new TL_Email($cfg['email_smtp'], $cfg['email_user'], $cfg['email_pass']);
        $data['from'] = $cfg['email_user'];
        $data['replyto'] = $cfg['email_user'];
        $data['alias'] = '';
        $data['subject'] = $subject;
        $data['body'] = $body;
        $data['to'] = $to;

        return $email->send($data);
    }

}