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

class User_Controller extends Ext_Api_Auth
{
    private $allowed = array(
                            'labor',
                            'gender',
                            'birth',
                            'height',
                            'weight',
                            'area'
                        );
    
    public function setAction()
    {
        $count = 0;
        foreach ($this->allowed as $v) {
            $input = TL_Tools::safeInput($v);
            if ($input !== null) {
                $this->user->$v = $input;
                $count++;
                if ($v == 'weight') {
                    // TODO: add weight log
                }
            }
        }
        if ($count > 0 && $this->user->update()) {
            $this->result['data'] = $this->getUserData();
            $this->result['ok'] = 1;
        } else {
            $this->output(102);
        }
    }
    
    private function getUserData()
    {
        $data = array('nickname' => $this->user->nickname);
        foreach ($this->allowed as $v) {
            $data[$v] = $this->user->$v;
        }
        return $data;
    }
    
    public function getAction()
    {
        $this->result['data'] = $this->getUserData();
        $this->result['ok'] = 1;
    }
    
    public function avatarAction()
    {
        $this->log('====>>>> avatarAction start');
        $this->ossUpload('avatar');
        if ($this->data != null) {
            if (!empty($this->account->avatar)) {
                $this->ossDelete($this->account->avatar);;
            }
            $this->account->avatar = $this->data;
            $this->account->update();
        }
        $this->log('====>>>> avatarAction end');
    }
    
    /*
     * 绑定昵称、手机等
     */
    public function bindAction()
    {
        // email && phone need verify before bind
        $bind = array(
          //'email' => array('reg' => 'email', 'min' => 5, 'max' => 64),
          'nickname' => array('reg' => 'zys_', 'min' => 2, 'max' => 12),
          //'phone' => array('reg' => 'digit', 'min' => 11, 'max' => 11)
        );
        $type = TL_Tools::safeInput('type');
        if (!isset($bind[$type]) ) {
            $this->output(102);
        }
        // nickname unable to unbind, as comments using nickname to identify user
        if ($type == 'nickname' && !empty($this->user->$type)) {
            $this->output(401);
        }

        $value = trim(TL_Tools::safeInput('value', $bind[$type]['reg']));
        $len = mb_strlen($value, 'UTF-8');
        if (empty($value) || $len < $bind[$type]['min'] || $len > $bind[$type]['max']) {
            $this->output(104);
        }
        
        $auth = new Auth($value);
        if (!empty($auth->uid)) {
            $this->output(406);
        }
        
        $auth->name = $value;
        $auth->uid = $this->user->uid;
        $ok = $auth->insert();
        if ($ok) {
            if (!empty($this->user->$type)) {
                $old = new Auth($this->user->$type);
                $old->remove();
            }
            $this->user->$type = $value;
            $ok = $this->user->update();
        }
        $this->result['ok'] = $ok;
    }
    
    
    public function commentAction()
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
        $data = new Data($hash_id);
        $params = array(
            'hash_id' => $hash_id,
            'limit' => $limit,
            'page' => $page
        );
        $all = $data->getAllByCache($params, 3600);
        
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
    
    public function agreeAction()
    {
        $time_add = TL_Tools::safeInput('time_add', 'digit');
        $id = TL_Tools::safeInput('id', 'digit');
        $uid = TL_Tools::safeInput('uid');
        
        $hash_id = 'ac'.$id;
        $params = array(
            'hash_id' => $hash_id,
            'uid' => $uid,
            'time_add' => $time_add,
            'memo' => $this->user->uid 
        );
        $data = new Data($hash_id);
        $item = $data->getItemByParams($params);
        
        $res = 0;
        if (empty($item)) {
            $data->fromArray($params);
            if ($data->insert()) {
                $res = 1;
            }
        } else {
            if($data->delByParams($params)) {
                $res = -1;
            }
        }
        
        if ($res != 0) {
            $agree = new Agree('ca'.$id);
            $agree->incr($uid.$time_add, $res);
        }
        
        $this->result = array(
            'ok' => 1,
            'data' => array(
                'key' => $uid.$time_add,
                'val' => $res
            )
        );
    }
    
}
