<?php


class Menu extends Ext_Model
{
    public $cls_tbl = 'menu';
    public $name = '';
    public $parent_id = 0;
    public $path = '';
    public $link = '';
    public $sort = 0;
    public $active;

    public function getFields()
    {
        $this->cls_required = array('name');
        parent::validateFields();

        $fields['name'] = strval($this->name);
        $fields['parent_id'] = intval($this->parent_id);
        $fields['path'] = strval($this->path);
        $fields['link'] = strval($this->link);
        $fields['sort'] = intval($this->sort);

        return $fields;
    }

    public static function getForm($obj)
    {
        /* 设置表单参数 */
        $data = array(
            'type'    => strtolower(get_class()), 
            'check'   => true,
            'id'      => $obj->id,
            'field'   => array(
                'name'        => array('type' => 'input', 'value' => $obj->name),
                'link'        => array('type' => 'input', 'value' => $obj->link),
                'parent_id'   => array('type' => 'select', 'value' => $obj->getTree(), 'required' => $obj->parent_id, 'label' => 'parent'),
                'sort'        => array('type' => 'input', 'value' => $obj->sort),
                ),
            );
        return $data;
    }

    /* 
    * 获取菜单列表结构 格式如下
    *
    * 我的工作台 [link|]
    *     收件箱 [link|http://localhost/email/inbox]
    *
    * 电子邮件 [controller|email]
    *     收件箱 tab
    *     发件箱 tab
    *
    * 系统设置 [link|]
    *     系统菜单 [controller|menu]
    *         菜单管理 tab  
    *
    * 原则：
    * controller可以做为一级菜单 并自动附加 tab
    * tab 可以作为 [link=]属性
    *
    * 用户权限格式为
    * array(
    *     'menu' => array('manage', 'add', 'edit'),
    *     .
    *     .
    *     .
    * )
    * 
    * 默认为空是array([0] => array()) count($privilege) == 1;
    * $privilege == NULL 为没有权限限制
    *
    * 20090227 更改为只有 动作或空 支持多级
    *
    */
    public static function getMenu($privilege = NULL, $parent_id = 0)
    {
        $result = array();

        foreach (self::getMenuByParentId($parent_id) as $key => $menu) {
            /* 如果 link = 空 表示可能有下级递归 */
            if (TL_Tools::isHttp($menu['link'])) {
                $result[$key] = $menu;
            }else if (!$menu['link']) {
                $result[$key] = $menu;
                $result[$key]['sub'] = self::getMenu($privilege, $menu['id']);
            }else {
                list($controller, $action) = explode('/', $menu['link']);

                /* 
                * 判断如果需要对权限进行处理
                * 则根据用户相应的权限 设置controller/action的active状态
                * 否则略过
                */
                if ($privilege != NULL && empty($privilege[$controller])) {
                    $menu['active'] = 0;
                }else if ($privilege != NULL && !in_array($action, $privilege[$controller])) {
                    $menu['active'] = 0;
                }
                $result[$key] = $menu;               
            }
        }
        return $result;
    }

    private static function getMenuByParentId($parent_id)
    {
        $where = array('parent_id' => intval($parent_id));
        $order = array('sort' => 'DESC');
        $self  = new self();
        return $self->getResult($where, $order);
    }

    public static function getController()
    {
        $order = array('sort' => 'DESC');
        $self  = new self();
        $items = $self->getResult(null, $order);

        $result = array();
        foreach ($items as $item) {
            if ($item['link'] && !TL_Tools::isHttp($item['link'])) {
                list($result[]) = explode('/', $item['link']);
            }
        }
        return array_unique($result);
    }

}