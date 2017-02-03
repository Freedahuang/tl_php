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
 * 构造器指定 file/memcache/redis 类型，如非 file 则默认
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Cache_File
{
    private static $INSTANCE = array();
    private $_path = '';
    
    public static function getInstance($dir='')
    {
        $key = md5($dir);
        if (!array_key_exists($key, self::$INSTANCE)) {
            self::$INSTANCE[$key] = null;
            $self = new self($dir);
            self::$INSTANCE[$key] = $self;
        }
        return self::$INSTANCE[$key];
    }
    
    function __construct($dir='')
    {
        if (empty($dir)) {
            $dir = sys_get_temp_dir();
        }
        $this->_path = TL_FSO::getMultDir($dir, strtolower(get_class($this)));
    }
    
    public function get($key)
    {
        $filename = $this->getFilePath($key);
        if (file_exists($filename)) {
            $res = TL_FSO::getFileContent($filename);
            return $res;
        }
        return false;
    }
    
    public function set($key, $val)
    {
        $filename = $this->getFilePath($key);
        TL_FSO::createFile($filename, $val);
    }
    
    /**
     * walk through file, with basename as date start, extension as limitation
     * check if date start expires limitation, if dose, foreach lines, del it
     * remove repeat item
     * 
     * @param string $key
     * @param string $ttl
     */
    public function expire($key, $ttl)
    {
        $filename = $this->_path.date('Y-m-d').'.'.$ttl;
        $mod = $this->getFileMod($key);
        $val = $mod.DIRECTORY_SEPARATOR.$key.PHP_EOL;
        TL_FSO::createFile($filename, $val, 'ab+');
    }
    
    public function del($key)
    {
        $filename = $this->getFilePath($key);
        if (file_exists($filename)) {
            TL_FSO::deleteFile($filename);
        }
    }
    
    private function getFileMod($key)
    {
        $crc = sprintf('%u', crc32($key));
        return fmod($crc, 128);
    }
    
    private function getFilePath($key)
    {
        $mod = $this->getFileMod($key);
        return TL_FSO::getMultDir($this->_path, $mod).$key;
    }
}


