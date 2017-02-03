<?php
/**
 * 角色权限处理 可以用于用户角色 或部门角色等 
 * 动态方法继承自 TL_Model 实现对 Privilege 对象的 增加 删除操作等
 * 静态方法用于获取数据 实现与其他业务对象进行交换等
 */


class Privilege extends Ext_Model
{
    public $cls_tbl = 'privilege';
    public $name = '';
    public $privileges = NULL;
    public $brief = NULL;
    public $sort = 0;
    public $active;

    public function getFields()
    {
        $this->cls_required = array('name');
        parent::validateFields();

        $fields['name']       = strval($this->name);
        $fields['privileges'] = strval($this->privileges);
        $fields['brief']       = strval($this->brief);
        $fields['sort']       = intval($this->sort);

        return $fields;
    }

    public static function getForm($obj)
    {
        /* 设置表单参数 */
        $data = array(
            'type'    => strtolower(get_class()), 
            'check'   => false,
            'id'      => $obj->id,
            'field'   => array(
                'name'        => array('type' => 'input', 'value' => $obj->name),
                'sort'        => array('type' => 'input', 'value' => $obj->sort),
                'brief'       => array('type' => 'textarea', 'value' => $obj->brief),
                ),
            );
        return $data;
    }

    public static function getTable()
    {
        /* 设置 table 需要显示的参数 */
        $data = array(
            'type'    => 'privilege', 
            'field'   => array(
                'name'      => array('width' => '180'),
                'brief'     => array(),
                'active'    => array('title' => 'status'),
                ),
            );
        return $data;
    }

    /*
    * 获取所有需要验证的动作的controller列表
    *
    */
    public static function getAuthList()
    {
        $domain_arr = TL_Tools::getDomainArray();
        $result = array();
        
        /* 从菜单表中获取所有注册的action/controller类型的controller值 */
        foreach (Menu::getController() as $item) {
            $controller = TL_Tools::parseHyphenString($item);

            $module = TL_Tools::safeInput('module', 'alpha', 'admin');
            try {
                TL_Loader::loadFile('apps_'.$module.'_controllers_'.$controller);
            } catch (Exception $e) {
                //var_dump($e->getMessage());
                continue;
            }
            
            $alias = TL_Tools::getLang(strtolower($controller));

            $controller .= '_Controller';
            $instance    = new $controller();

            if (property_exists($instance, 'name')) {
                $alias = $instance->name;
            }

            $result[$item]['alias']   = $alias;
            $result[$item]['actions'] = array();

            foreach ($instance->actions as $action => $property) {
                if ($property['auth']) {
                    $result[$item]['actions'][$action] = $property;
                }
            }
        }
        return $result;
    }
}