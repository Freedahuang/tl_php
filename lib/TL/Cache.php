<?php
/**
 * Cache 处理类 用于缓存数据
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

/**
 * 用于缓存数据 以文件的形式存储 并辅以memcache 做value更改判断
 * 缓存策略，引入 file, redis 类型，有效期 30 天
 * 当 redis 不可用时，默认 file
 * 
 * file 用 date.log 保存创建 key，crontab 每日清理时扫描
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Cache
{
    private static $INSTANCE = null;
    private $_cache;
    
    public static function getInstance()
    {
        if (self::$INSTANCE == null) {
            $self = new self();
            self::$INSTANCE = $self;
        }
        return self::$INSTANCE;
    }
    
    function __construct()
    {
        try {
            $this->_cache = TL_Redis::getInstance();
        } catch (Exception $e) {}
        if ($this->_cache == null) {
            $this->_cache = TL_Cache_File::getInstance();
        }
    }
    
    private function getKey($key)
    {
        return 'cache:'.md5($key);
    }
    
    public function get($key)
    {
        if (defined('_IN_DEV_') && _IN_DEV_) {
            return false;
        }
        $val = $this->_cache->get($this->getKey($key));
        if ($val) {
            $val = unserialize($val);
        }
        return $val;
    }
    
    public function set($key, $val)
    {
        if (defined('_IN_DEV_') && _IN_DEV_) {
            return false;
        }
        $key = $this->getKey($key);
        $this->_cache->set($key, serialize($val));
    }

    public function ttl($key, $ttl)
    {
        $this->_cache->expire($key, (int)$ttl);
    }
    
    public function del($key)
    {
        if (defined('_IN_DEV_') && _IN_DEV_) {
            return false;
        }
        $key = $this->getKey($key);
        $this->_cache->del($key);
    }
}


