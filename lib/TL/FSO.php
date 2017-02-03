<?php
/**
 * 服务器端文件、目录处理类
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

/**
 * 处理文件、目录 新增 删除 等等
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_FSO
{
    /**
     * 删除目录及其子目录 示例如 FSO::deleteDir(_WRITABLE_DIR_.'cache');
     *
     * @param string $dir Directory name
     * @param boolean $recursive 判断是否删除子目录
     */
    public static function deleteDir($dir, $recursive = true)
    {
        $files = self::findAll($dir);
        foreach ($files as $file)
            if (is_dir($file) && $recursive == true)
                self::deleteDir($file, $recursive);
            else
                self::deleteFile($file);

        if ($recursive == true)
            rmdir($dir);

        return;
    }

    /**
     * 创建目录
     *
     * @param string $dir Directory name
     */
    public static function createDir($dir)
    {
        clearstatcache();
        if (file_exists($dir))
            return true;

        return mkdir($dir);
    }

    /**
     * 删除指定目录下所有文件
     *
     * @param string $dir Directory name
     */
    public static function emptyDir($dir)
    {
        self::deleteDir($dir, false);
    }

    /**
     * 查找 当前目录下 文件名开头与指定字符串$key = md5码 相同的文件
     * 供 cache 类调用
     *
     * @param string $dir Directory name
     */
    public static function findAll($dir, $key = NULL)
    {
        clearstatcache();
        if (!is_dir($dir))
            return array();

        $result = array();
        foreach (scandir($dir) as $filename)
        {
            if ($filename != '.' && $filename != '..' && preg_match("/{$key}/", $filename))
            {
                $result[] = realpath($dir.DIRECTORY_SEPARATOR.$filename);
            }
        }
        return $result;
    }

    /**
     * 调用本类的 findAll 函数查找指定目录下所有文件
     *
     * @param string $dir Directory name
     * @param string $key 是否与键名md5码 相同
     * @param string $type is_file|is_dir
     */
    public static function findFile($dir, $key = NULL, $type = 'is_file')
    {
        $result = array();
        $files = self::findAll($dir, $key);
        foreach ($files as $file)
        {
            if ($type($file))
                $result[] = $file;
        }
        return $result;
    }
    /* 
     * 删除文件
     *
     * 用于 AttachmentOperation 和 Cache 类操作
     */
    public static function deleteFile($file)
    {
        @fclose($file);
        @unlink($file);
    }

    /**
     * 获取文件扩展名
     *
     * @param string $filename
     * @return string
     */
    public static function getFileExtension($filename)
    {
        $pathinfo = pathinfo($filename);
        return '.'.$pathinfo['extension'];
    }

    /*
     * 创建文件
     *
     * @param string $file 文件名
     * @param string $data 数据流
     * @return boolean
     */
    public static function createFile($file, $data, $mode='wb+')
    {
        $fp = fopen($file, $mode);
        if ($fp)
        {
            fwrite($fp, $data);
            fclose($fp);
            return true;
        }
        return false;
    }

    /*
     * 获取文件内容
     *
     * @param string $file 文件名
     * @return mixed data|false
     */
    public static function getFileContent($file)
    {
        $fp = @fopen($file, 'rb+');
        if ($fp)
        {
            $data = fread($fp, filesize($file));
            fclose($fp);
            return $data;
        }
        return false;
    }

    /*
     * 输出不用缓存的文件头 用于 captcha 类等 不需要多数据进行缓存的操作
     *
     */
    public static function headerNoCache()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    /*
     * 输出提示下载的文件头
     *
     */
    public static function OutputDownloadHeader($filename)
    {
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/force-download');
        header('Content-Type: application/download');
        header('Content-Disposition: attachment;filename='.iconv('UTF-8', 'GBK', $filename));
        header('Content-Transfer-Encoding: binary ');
    }

    
    
    /*
     * 输出 xls 格式文件 模拟输出xls文件的方法 优点，
     * 如果没有安装Excel，将文件名改成htm，可以用浏览器直接查看。
     * 安装有Excel的话，将其打开，再另存为真正意义上的xls文件。
     *
     * $data = array(
     *      'filename'  => 'veecl5_2008_12_report',
     *      'title'     => '2008年12月份收益报表详情',
     *      'header'    => array('下单时间', '订单号', '总金额', '折扣金额', '已收款', '订单状态'),
     *      'cells'     => array(
     *                      array('2009-02-25', 'I2009022500001', '360', '5', '355', '订单完成'),
     *                      array('2009-02-25', 'I2009022500001', '360', '5', '355', '订单取消'),
     *                      ),
     *      'summary'   => array('total_invoice' => 10, 'valid_invoice' => 10, 'money' => 100, 'rate' => 0.05, 'commission' => 10),
     *      );
     */
    public static function xlsReport($data)
    {
        $result = "<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n";
        $result .= "<title>{$data['title']}</title></head>\r\n";
        $result .= "<table width=\"100%\" border=\"1\">\r\n";
        $result .= "<tr><td colspan=".count($data['header'])." align=\"center\"><b>{$data['title']}</b></td></tr>\r\n";
        
        $result .= '<tr>';
        foreach ($data['header'] as $item)
            $result .= '<th>'.$item.'</th>';
        $result .= "</tr>\r\n";

        foreach ($data['cells'] as $cell)
        {
            $result .= '<tr>';
            foreach ($cell as $item)
                $result .= '<td align="center">'.$item.'</td>';
            $result .= "</tr>\r\n";
        }

        if ($data['summary']) {
            $result .= '<tr>';
            foreach ($data['summary'] as $item)
                $result .= '<td align="center">'.$item.'</td>';
            $result .= "</tr>\r\n";
        }
        
        if ($data['brief']) {
            $result .= "<tr><td colspan=".count($data['header'])." align=\"left\">{$data['brief']}</td></tr>\r\n";
        }

        $result .= "</table>\r\n";

        self::OutputDownloadHeader($data['filename'].'.xls');

        echo $result;
        exit;
    }

    /**
     * 把url的数据模拟POST提交 并获取返回数据
     *
     * @return string
     */
    public static function post($url, $data = null, $time_out = "60") {
        $urlarr     = parse_url($url);
        $errno      = '';
        $errstr     = '';
        $transports = '';
        $query      = '';

        if (is_array($data)){
            foreach ($data as $key => $item) {
                $query .= $key.'='.$item.'&';
            }
        } else if (!empty($data)) {
            $query = $data;
        }

        if($urlarr["scheme"] == "https") {
            $transports = "ssl://";
            $urlarr["port"] = "443";
        }else {
            $transports = "tcp://";
            if (!isset($urlarr["port"])) {
                $urlarr["port"] = "80";
            }
        }

        $fp = @fsockopen($transports.$urlarr['host'], $urlarr['port'], $errno, $errstr, $time_out);
        if(!$fp) {
            die("ERROR: $errno - $errstr<br />\n");
        } else {
            $header = "POST ".$urlarr['path'].(isset($urlarr['query']) ? ('?'.$urlarr['query']) : '')." HTTP/1.1\r\n";  
            $header .= "Host: ".$urlarr["host"]."\r\n";
            $header .= "Referer: ".$url."\r\n";
            $header .= "Content-type: application/x-www-form-urlencoded\r\n";
            $header .= "Content-length: ".strlen($query)."\r\n";
            $header .= "Connection: close\r\n\r\n";
            $header .= $query."\r\n\r\n";
            fwrite($fp, $header);

            $line = '';
            while(!feof($fp)) {
                $line .= @fgets($fp, 1024);
            }
            fclose($fp);
            
            if ($line) {  
                $body = stristr($line, "\r\n\r\n");  
                $body =substr($body, 4, strlen($body));  
                $line = $body;  
            }  
            return $line;
        }
        return false;
    }

    /* 异步通知 */
    public static function synPost($url, $query='')
    {
        $result = NULL;
        $param = parse_url($url);
        if($param["scheme"] == "https") {
            $transports = "ssl://";
            $param["port"] = "443";
        } else {
            $transports = "tcp://";
            $param["port"] = "80";
        }
        
        if  (empty($query)) {
            $query = $param["query"];
        }
        
        $fp = stream_socket_client($transports.$param['host'].':'.$param['port'], $errno, $errstr, 180);
        if ($fp) {
           fputs($fp, "POST ".$param["path"]." HTTP/1.1\r\n");
           fputs($fp, "Host: ".$param["host"]."\r\n");
           fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
           fputs($fp, "Content-length: ".strlen($query)."\r\n");
           fputs($fp, "Connection: close\r\n\r\n");
           fputs($fp, $query."\r\n\r\n");
           
           /*
           while (!feof($fp)) {
               $result = fgets($fp, 1024);
           }*/
           fclose($fp);
           //self::createFile(_UPLOAD_DIR_.'notify.txt', date("Y-m-d H:i:s").'-'.$result.'-'.$param['path']."\r\n");
        }
        return $result;
    }

    /**
     * 根据 传入的参数生成多级目录
     *
     * @param string $dir|根目录如 _UPLOAD_DIR_
     * @param string $value|多级目录字符串如 'cache'.DIRECTORY_SEPARATOR.date("Ym")
     * @return string $path
     */
    public static function getMultDir($dir, $value)
    {
        $dir = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        foreach (explode(DIRECTORY_SEPARATOR, $value) as $item) {
            $dir .= $item.DIRECTORY_SEPARATOR;
            if (!self::createDir($dir)) {
                throw new Exception('cache dir set failed: '.$dir);
            }
        }
        return $dir;
    }


    public static function isPic($filename)
    {
        $image = array('.jpg', '.png', '.gif');
        $ext   = self::getFileExtension($filename);
        return in_array(strtolower($ext), $image);
    }
}
