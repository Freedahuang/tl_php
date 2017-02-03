<?php
TL_Loader::loadFile('lib_aws_aws-autoloader');

class TL_S3 {
    
    static private $instance = array();
    private $s3 = NULL;
    private $bucket = '';
    private $path = '';
    
    private function __construct($bucket) {
        $sdk = new Aws\Sdk([
            'version'     => 'latest',
            'region'      => AWS_REGION,
            'scheme'      => 'http',
            'debug'       => false,
            'credentials' => [
                'key'    => AWS_ACCESS_ID,
                'secret' => AWS_ACCESS_KEY
            ]
        ]);
        $this->s3 = $sdk->createS3();
        $this->bucket = $bucket;
    }
    
    static function getInstance($bucket=AWS_BUCKET) {
        if (!isset(self::$instance[$bucket])) {
            self::$instance[$bucket] = new self($bucket);
        }
        return self::$instance[$bucket];
    }
    
    public function getStrMime($str)
    {
        $f = finfo_open();
        $res = finfo_buffer($f, $str, FILEINFO_MIME_TYPE);
        finfo_close($f);
        return $res;
    }
    
    /**
     * 复制到oss
     * 
     * @param $type 目录
     * @param $file 文件名（小于90），字串流（大于90）
     */
    public function upload($type, $file, $mime='', $uri='') {
        $mime_allow = array(
                'image/jpeg' => 'jpg'
                ,'image/gif' => 'gif'
                ,'image/png' => 'png'
                ,'text/html' => 'htm' // html match oss mime-type
        );

        $params = array();
        if (strlen($file) < 90 && file_exists($file)) {
            $mime = empty($mime) ? mime_content_type($file) : $mime;
            $params['SourceFile'] = $file;
        } else {
            $mime = empty($mime) ? $this->getStrMime($file) : $mime;
            $params['Body'] = $file;
        }
        
        if (!in_array($mime, array_keys($mime_allow))) {
            return false;    
        }
        $path = $type.'/'.date('Ym').'/'.uniqid().'.'.$mime_allow[$mime];
        $path = $this->_getKey($uri, $path);
        try {
            $params['Bucket'] = $this->bucket;
            $params['Key'] = $path;
            $params['ContentType'] = $mime;
            $params['ACL'] = 'public-read';
            $resp = $this->s3->putObject($params)->toArray();
            $this->path = $resp['ObjectURL'];
            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    private function _getKey($uri, $res='')
    {
        if (!empty($uri)) {
            $_arr = explode($this->bucket.'/', $uri);
            if (count($_arr) > 1) {
                $res = $_arr[1];
            }
        }
        return $res;
    }
    
    /**
     * 删除oss对象
     *
     */
    public function delete($uri) {
        $key = $this->_getKey($uri);
        if (!empty($key)) {
            $resp = $this->s3->deleteObject(array(
                'Bucket' => $this->bucket,
                'Key'    => $key
            ));
            return true;
        }
        return false;
    }
    
    public function get($file)
    {
        $result = $this->s3->getObject([
            'Bucket' => $this->bucket,
            'Key'    => $file
        ]);
        return $result;
    }
    
    public function getPath()
    {
        return $this->path;
    }
}