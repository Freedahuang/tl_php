<?php
// user_examination_suit
class Seq extends Ext_Model
{
    public $cls_dbname = '';
    public $cls_tbl = 'seq_table';
    public $cls_identifier = 'seq';
    public $seq = 0;

    public function __construct($dbname)
    {
        $this->cls_dbname = $dbname;
        parent::__construct();
    }

    public function getIncrementId()
    {
        $this->rawExecute('update '.$this->cls_dbname.'.seq_table set seq = LAST_INSERT_ID(seq + 1)');
        return $this->getInsertId();
    }
    
    public function getMaxId()
    {
        $res = $this->getRow(null);
        return !$res ? 0 : intval($res['seq']);
    }
}