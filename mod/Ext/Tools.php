<?php

class Ext_Tools
{    
    public static function safePost($input=null)
    {
        if ($input == null) {
            $input = $_POST;
        }
        $data = array();
        foreach ($input as $k=>$v) {
            if ($k == 'submit') {
                continue;
            }
            $data[$k] = TL_Tools::safeInput($k);
        }
        return $data;
    }

    public static function getSelect($data, $prefix='')
    {
        $result = array();
        foreach ($data as $k=>$v) {
            if ($v) {
                $result[] = array('id'=>$prefix.$k,'name'=>$v,'active'=>1);
            }
        }
        return $result;
    }
    
    public static function getMod($identifier, $x)
    {
        $crc = sprintf('%u', crc32($identifier));
        return fmod($crc, $x);
    }
    
    public static function getCfg()
    {
        $config = new Config();
        $pdList = $config->redis2push();
        $res = array();
        foreach ($pdList as $v) {
            $key = str_replace('pd_', '', $v['id']);
            $res[$key] = $v['brief'];
        }
        return $res;
    }
}