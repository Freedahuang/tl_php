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

class Comment_Controller extends Ext_Api_Auth
{
    public function timeAction()
    {
        $this->getComment('Data');
    }
    
    public function agreeAction()
    {
        $this->getComment('Comment');
    }
    
    private function getComment($src)
    {
        $limit = TL_Tools::safeInput('limit', 'digit');
        if ($limit < 1 || $limit > 30) {
            $this->output(103);
        }
        $page = TL_Tools::safeInput('page', 'digit');
        if ($page < 1) {
            $page = 1;
        }
        $id = TL_Tools::safeInput('id', 'digit');

        $hash_id = 'ca'.$id;
        $data = new $src($hash_id);
        $params = array(
            'hash_id' => $hash_id,
            'limit' => $limit,
            'page' => $page
        );
        $all = $data->getAllByCache($params, 300);
        
        // add dynamic agree number to cache comment
        $user_agree = array();
        $d2 = new Data('ac'.$id);
        $res = $d2->getListByParams(array(
            'hash_id' => 'ac'.$id,
            'memo' => $this->user->uid
        ), null, null);
        if (!empty($res)) {
            foreach ($res as $v) {
                $user_agree[] = $v['uid'].$v['time_add'];
            }
        }
        
        $agree = new Agree($hash_id);
        foreach ($all as $k => $v) {
            $all[$k]['agree_number'] = intval($agree->score($v['key']));
            $all[$k]['agree_done'] = in_array($v['key'], $user_agree);
        }

        $this->result = array(
            'ok' => 1,
            'data' => $all
        );        
    }
    
}
