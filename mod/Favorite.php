<?php

class Favorite extends Ext_Model_Redis_Rank
{
    public function build($val)
    {
        return array('info' => $val);
    }
    
}