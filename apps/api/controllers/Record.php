<?php
/**
* 控制器类 
*
* 控制器类必须放置在制定目录下 如 /application/controller/
* 视图路径默认 /application/view/controller/action.tpl
* 并命名为 indexController.php 文件名
* IndexController 类名
*
* @author Liang Tan <tanjnr@gmail.com>
* @version 1.0
*/

class Record_Controller extends Ext_Api_Auth
{
    public function addAction()
    {
        $json = TL_Tools::safeInput('json', 'proto');
        $cate = TL_Tools::safeInput('cate');
        $time_add = TL_Tools::safeInput('time_add');
        $params = compact('cate', 'time_add', 'json');
        $this->result = $this->_add($params);
    }
    
    private function _add($params)
    {
        $record = new Record();
        $format = $record->getCate($params['cate']);
        if (empty($format) || empty($params['time_add']) || empty($params['json'])) {
            $this->output(102);
        }

        try {
            $params['json'] = tl_json_decode($params['json'], true);
        } catch (Exception $e) {
            $this->output(103);
        }
        foreach ($format as $k => $v) {
            if ($v && empty($params['json'][$k])) {
                $this->output(103);
            }
        }
        
        $data = array(
            'uid'  => ['S' => $this->user->uid],
            'time_add' => ['S' => $params['time_add']],
            'cate' => ['S' => $params['cate']],
            'json' => ['S' => json_encode($params['json'])]
        );
        
        $record->fromArray($data);
        return $record->save();
    }
    
    public function syncAction()
    {
        $data = TL_Tools::safeInput('data', 'proto');
        $count = 0;
        foreach ($data as $v) {
            $res = $this->_add($v);
            $count += $res['ok'];
        }
        $this->result = array(
            'ok' => 1,
            'data' => $count
        );
    }
    
    public function getAction()
    {
        //$record = new Record();
        $keys = array(
            'uid'  => array(
                'AttributeValueList' => [['S' => $this->user->uid]],
                'ComparisonOperator' => 'EQ'
            ),
            'time_add'  => array(
                'AttributeValueList' => [['S' => date('YmdHis', time()-180*24*3600)]],
                'ComparisonOperator' => 'GE'
            )
        );
        $this->result['ok'] = 1;
        //$this->result = $record->query($keys);
    }

    public function delAction()
    {
        $time_add = TL_Tools::safeInput('time_add');
        if (empty($time_add)) {
            $this->output(103);
        }
        $data = array(
            'uid'  => ['S' => $this->user->uid],
            'time_add' => ['S' => $time_add]
        );

        $record = new Record();
        $this->result = array(
            'ok' => 1,
            'data' => $record->delete($data)
        );
    }
}