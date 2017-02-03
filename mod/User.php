<?php

class User extends Ext_Model_Hash
{
    private $cls_redis;
    private $cls_key;
    public $cls_num_tbl = 32;
    public $cls_dbname = 'tl_user';
    public $cls_tbl = 'user';
    public $cls_identifier = 'uid'; // 用户ID 
    public $uid = '';
    public $device = '';            // 设备标识，更换设备时需重新登陆，防止数据不同步
    public $pass = '';              // 密码
    public $nickname = '';          // 昵称 
    public $brief = '';             // 简介
    public $realname = '';          // 姓名
    public $email = '';             // 姓名
    public $area = '';              // 地市
    public $phone = '';             // 电话 关联 auth
    public $height = 0;             // 身高 cm
    public $weight = '';            // 体重 kg
    public $gender = 0;             // 0女1男
    public $birth = '';             // 生日
    public $avatar = '';            // 头像url
    public $labor = '';             
    public $date_add = '';          // 创建时间
    public $date_upd = '';          // 修改时间
    public $valid = 0;              
    public $vip = 0;                // 加V，级别
    
    public function __construct($identifier=false)
    {
        parent::__construct($identifier);

        $this->cls_redis = TL_Redis::getInstance($this->cls_name);
        $this->cls_key = $this->cls_name.':latest';
    }
    
    public function getFields()
    {
        $this->cls_required = array($this->cls_identifier);
        parent::validateFields();

        $fields['uid'] = strval($this->uid);
        $fields['device'] = strval($this->device);
        $fields['pass'] = strval($this->pass);  
        $fields['nickname'] = strval($this->nickname);
        $fields['brief'] = strval($this->brief);
        $fields['realname'] = strval($this->realname);
        $fields['email'] = strval($this->email);
        $fields['area'] = strval($this->area);
        $fields['phone'] = strval($this->phone);
        $fields['height'] = intval($this->height);
        $fields['weight'] = strval($this->weight);
        $fields['gender'] = intval($this->gender);
        $fields['birth'] = strval($this->birth);
        $fields['avatar'] = strval($this->avatar);
        $fields['labor'] = intval($this->labor);
        $fields['date_add'] = strval($this->date_add);
        $fields['date_upd'] = strval($this->date_upd);
        $fields['valid'] = strval($this->valid);
        $fields['vip'] = intval($this->vip);
        
        return $fields;
    }

    public static function getForm($obj)
    {        
        /* 设置表单参数 */
        $data = array(
            'type'    => strtolower(get_class()), 
            'check'   => false,
            'id'      => $obj->uid,
            'field'   => array(
                'uid'   => array('type' => 'label', 'value' => $obj->uid),
                'valid'     => array('type' => 'date', 'value' => $obj->valid),
                ),
            ); 
        return $data;
    }

    public static function getTable()
    {
        /* 设置 table 需要显示的参数 */
        $data = array(
            'type'    => strtolower(get_class()), 
            'add'     => 'remove',
            //'edit'     => 'remove',
            'field'   => array(
                'uid'       => array('width' => '100', 'customizable' => true),
                'nickname'  => array('align' => 'left'),
                'email'     => array('width' => '160'),
                'height'    => array('width' => '60'),
                'weight'    => array('width' => '60'),
                'birth'     => array('width' => '80'),
                'date_upd'  => array('width' => '140'),
                'valid'     => array('width' => '100'),
            ),
            );
        return $data;
    }   
    
//     public function parseList($result)
//     {
//         foreach ($result as $k => $v) {
//             $valid = !$v['valid'] ? 'inactive' : 'active';
//             if ($v['valid'] > 1) {
//                 $valid = date('Y-m-d', $v['valid']);
//             }
//             $result[$k]['status'] = $valid;
//         }
//         return $result;
//     }
    
    public function addLatest()
    {
        if ($this->{$this->cls_identifier}) {
            $this->cls_redis->lPush($this->cls_key, $this->{$this->cls_identifier});
            $this->cls_redis->ltrim($this->cls_key, 0, $this->cls_num_tbl * 10);
        }
    }
    
    public function getListByParams($params=array(), $order=null, $limit=true, $group=null)
    {
        // $params != null 时，启动搜索模式
        $uid = '';
        if (isset($params['auth'])) {
            
        } else if (isset($params['uid'])) {
            $uid = $params['uid'];
        }
        
        if ($uid) {
            return array();
        }
        
        // or lrange 模式
        $start = ($this->cls_list_page-1) * $this->cls_list_limit;
        $limit = $start + $this->cls_list_limit - 1;
        $ids = array();
        
        switch ($order) {
            case 'follow':
            case 'banned':
                $cls = new $order($this->uid);
                $ids = $cls->rank($start, $this->cls_list_limit);
                break;
            default:
                $ids = $this->cls_redis->lrange($this->cls_key, $start, $limit);
                $ids = array_flip($ids);
        }
        
        $res = array();
        foreach ($ids as $k => $v) {
            $user = new User($k);
            if ($order) {
                $user->memo .= $order.'='.$v;
            }
            $res[] = $user->toArray();
        }
        return $res;
    }
    
    private function keyCaptcha($k)
    {
        return $this->cls_name.':captcha:'.md5($k);
    }
    
    public function getCaptcha($email)
    {
        $key = $this->keyCaptcha($email);
        $res = intval($this->cls_redis->get($key));
        $mod = $res%10;
        if ($mod >= 3) {
            return '';
        }
        $rnd = rand(10000, 99999).($mod+1);
        $this->cls_redis->set($key, $rnd);
        if ($mod == 0) {
            $this->cls_redis->expire($key, 24*3600);
        }
        return $rnd;
    }
    
    private function genUid()
    {
        do {
            list($micro, $sec) = explode(' ', microtime());
            $rnd = intval($micro * 1000);
            $uid = TL_Tools::num2str($sec).TL_Tools::num2str($rnd);
            $user = new User($uid);
        } while ($user->uid);
        
        return $uid;
    }
    
    private function addUser($user)
    {
        $res = '';
        $user->valid = date('Y-m-d');
        if ($user->insert()) {
            $user->addLatest();
            $res = $user->uid;
        }
        return $res;
    }
    
    public function chkCaptcha($device, $email, $pass)
    {
        $res = '';
        $key = $this->keyCaptcha($email);
        $val = intval($this->cls_redis->get($key));
        // app有“获取”动作，且正确的，更新数据库 pass
        if ($val == $pass) {
            // add/update user info
            $auth = new Auth($email);
            if ($auth->uid) {
                $user = new User($auth->uid);
                $user->pass = sha1($pass);
                $user->device = $device;
                if ($user->uid) {
                    if ($user->update()) {
                        $res = $user->uid;
                    }
                } else {
                    $user->uid = $auth->uid;
                    $user->email = $email;
                    $res = $this->addUser($user);
                }
            } else {
                $auth->name = $email;
                $auth->uid = $this->genUid();
                if ($auth->insert()) {
                    $user = new User($auth->uid);
                    $user->uid = $auth->uid;
                    $user->pass = sha1($pass);
                    $user->device = $device;
                    $user->email = $email;
                    $res = $this->addUser($user);
                }
            }
            if (!empty($res)) {
                $this->cls_redis->del($key);
            }
        } else {
            // 没"获取"动作，只验证数据库pass
            // check user 
            $auth = new Auth($email);
            if ($auth->uid) {
                $user = new User($auth->uid);
                if (sha1($pass) == $user->pass) {
                    $user->device = $device;
                    if ($user->update()) {
                        $res = $user->uid;
                    }
                }
            }
        }
        return $res;
    }
}