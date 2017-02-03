<?php

/**
* 页面控制器类 错误页面及其他页面提示等等操作
*
* 控制器类必须放置在制定目录下 如 /application/controller/
* 视图路径默认 /application/view/controller/action.tpl
* 并命名为 indexController.php 文件名
* IndexController 类名
*
* @author Liang Tan <tanjnr@gmail.com>
* @version 1.0
*/

class Page_Controller extends Ext_Admin
{
    public $auth = null;
    
    public function indexAction()
    {
        /* 返回主页 */
        TL_Tools::redirect('index');
    }

    public function tipAction()
    {
        $option = TL_Tools::safeInput('option');
        $more = TL_Tools::safeInput('more');
        $back = TL_Tools::safeInput('back');
        
        header('Content-Type: text/html; charset=utf-8');

        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh"><head>';
        echo '<link href="'._ASSETS_URI_.'/css/global.css" rel="stylesheet" type="text/css" media="all" />';
        echo '<title>'.TL_Tools::getLang($option).'</title>';
        echo '<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" /></head><body><p>&nbsp;</p>
<p>&nbsp;</p>
<p style="text-align:center;">&nbsp;页面跳转中，请稍后。</p>


        </body></html>';

        $alert = TL_Tools::getLang($option);
        if ($more) {
            $alert .= TL_Tools::base64DecodeUrl($more);
        }
        echo '<script language="JavaScript">
        <!--
            alert("'.$alert.'");';
        if (!$back)
            echo '/* 尝试关闭thickbox窗口 如果没有则返回前页 */
                try{self.parent.tb_remove();}catch(e){if(e){self.history.go(-1);}else{}}';
        else
            echo 'document.location.href="'.TL_Tools::base64DecodeUrl($back).'";';
                
        echo '//-->
        </script>';
        
        exit;
    }

    /* 外部链接指向 */
    public function linkAction()
    {
        $option = TL_Tools::safeInput('option');
        $option = TL_Tools::base64DecodeUrl($option);

        $host = TL_Tools::getHost();
        $link = str_replace('{host}', $host, $option);
        $this->_view->assign('option', $link);
    }

    /* 获取广告联盟会员ID 记录下来 后 跳转到指定页面 默认index */
    public function unionAction()
    {
        $name = TL_Tools::safeInput('name', 'alpha');
        $back = TL_Tools::safeInput('back', 'alpha');

        /*
        * 记录ID 至 cookie, 并由客户确认订单时 计入每张订单
        * 后台由操作员最终确认订单完成时 计入用户积分
        */
        $id = Union::getIdByName($name);
        $union = new Union($id);
        if ($union->active)
        {
            $account = AuthAccount::getInstance();
            $account->setShipping('id_union', $id);
        }

        /* 跳转至指定页面 base64_encode 的值 */
        header('Location: '.($back ? TL_Tools::base64DecodeUrl($back) : '/'));
        exit;
    }

    public function showAction()
    {
        $width = TL_Tools::safeInput('width');
        $image = TL_Tools::safeInput('image');
        $image = str_replace("~", "/", $image);
        $this->_view->assign('image', $image);
        $this->_view->assign('width', $width);
    }

    public function logAction()
    {
        $option = TL_Tools::safeInput('option');
        $type = TL_Tools::safeInput('type');
        $id = TL_Tools::safeInput('id', 'digit');
        
        $cls = TL_Tools::parseHyphenString($option);
        $params = array($type => $id);
        $obj = new $cls();
        $list = $obj->getListByParams($params);
        
        $this->_view->assign('option', $option);
        $this->_view->assign('list', $list);
    }
    /* 生成验证码 */
    public function captchaAction()
    {
        $captcha = new TL_Captcha();
        $option = TL_Tools::safeInput('option');
        $captcha->generate($option);
    }

    /* 后台进入欢迎页面 */
    public function welcomeAction()
    {
        //Temp_Backup::clean();
        //TL_Tools::redirect('admin-temp/list');
    }

    public function hourAction()
    {
        $id_sp_service = TL_Tools::safeInput('id_sp_service', 'digit');
        $id_province = TL_Tools::safeInput('id_province', 'digit');
        $this->_view->assign('id_sp_service', $id_sp_service);
        $this->_view->assign('id_province', $id_province);
        
        //设置数据
    }
    
    public function graphAction()
    {
        $id_sp_service = TL_Tools::safeInput('id_sp_service', 'digit');
        $id_province = TL_Tools::safeInput('id_province', 'digit');
        //设置数据
        /*
        $data = array(
          array('01',  10),
          array('02',  8),
          array('03',  14),
          array('06',  25),
          array('08',  30),
          array('11',  45),
          array('15',  60)
        );
        */
        $params = array(
                    'date_add:>=' => date('Y-m-d 00:00:00')
                    // avoiding access working data
                    ,'date_upd:<' => date('Y-m-d H:i:s', time()-30)
                    );
        if (!empty($id_sp_service)) {
            $params['id_sp_service'] = $id_sp_service;   
        }
        if (!empty($id_province)) {
            $params['id_province'] = $id_province;   
        }
        
        $result = Charge_Log::getHourly($params);
        $list = array();
        foreach ($result as $v) {
            $list[$v['hour_add']] = $v;
        }
        /*
        $list = array(
          '01' =>  10,
          '02' =>  8,
          '03'=>   19,
          '06'=>   25,
          '08'=>  30,
          '11'=>   45,
          '15'=>   60
        );
        */
        $max = 10;
        $data = array();
        $hour = intval(date('H'));
        for ($i=0; $i<24; $i++) {
            $h = sprintf('%02d', $i);
            if (isset($list[$i])) {
                $charge_num = $list[$i]['charge_num'];
                $charge_ok = $list[$i]['charge_ok'];
                if ($charge_num > $max) {
                    $max = $charge_num;
                }
                if ($charge_ok > $max) {
                    $max = $charge_ok;
                }
                $data[] = array($h, $charge_num, $charge_ok);
            }
            else if ($i > $hour) {
                $data[] = array($h, null, null);
            }
            else {
                $data[] = array($h, 0, 0);
            }
        }
//var_dump($data);exit;
        $g = new TL_Graph(600, 300);
        $g->setData($data, $max, '计费时段汇总', array('计费次数', '成功次数'));
        $g->draw();
        exit;
    }

}