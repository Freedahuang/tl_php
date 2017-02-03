<?php
TL_Loader::loadFile('lib_aws_aws-autoloader');

class TL_DynamoDB {
    
    static private $instance = array();
    private $obj = NULL;
    private $table = '';
    
    private function __construct($table) {
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
        $this->obj = $sdk->createDynamoDb();
        $this->table = $table;
    }
    
    static function getInstance($bucket=S3_BUCKET) {
        if (!isset(self::$instance[$bucket])) {
            self::$instance[$bucket] = new self($bucket);
        }
        return self::$instance[$bucket];
    }
    
    public function save($data)
    {
        $params = [
            'TableName' => $this->table,
            'Item' => $data
        ];
        //var_dump($params);exit;
        $this->obj->putItem($params);
    }
    
    /**
     * 删除对象
     *
     */
    public function delete($data) 
    {
        $params = [
            'TableName' => $this->table,
            'Key' => $data
        ];
        //var_dump($params);exit;
        $this->obj->deleteItem($params);
    }
    
    public function get($keys)
    {
        $params = [
            'RequestItems' => $keys
        ];
        //var_dump($params);exit;
        return $this->obj->batchGetItem($params);
    }
    
    public function query($keys)
    {
        $params = [
            'TableName' => $this->table,
            'KeyConditions' => $keys
        ];
        //var_dump($params);exit;
        return $this->obj->query($params);
    }
}