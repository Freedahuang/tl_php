<?php
/**
 * 视图类，引用Smraty做输出 用法与Db类相同
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */

/**
 * 视图抽象类 给所有的视图操作提供个接口
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
abstract class TL_View
{
    /**
     * 用来存储当前单件模式的实例对象
     *
     * @var obj
     */
    private static $_instance;

    /**
     * 获取类的实例对象 该类使用了单件模式 返回对象实例
     * 确保一个用户只有一个实例在运行
     *
     * @return obj
     */
    public static function getInstance()
    {
        if(!isset(self::$_instance))
            self::$_instance = new TL_Smarty();

        return self::$_instance;
    }

    /**
     * 赋值给视图
     */
    abstract public function assign($key, $value = null);

    /**
     * 输出视图
     */
    abstract public function display($tpl);

    /**
     * 根据传入的参数 引用 并设置视图表单组件的显示格式
     * 
     * @param array $data
     * @return array
     */
    public static function getFormFields($data)
    {
        /* 
        * 在控制器里把表单的参数传递给视图
        * 参数结构 示例
        * $data = array(
        *     'type'    => 'department', 
        *     'check'   => true,
        *     'id'      => $department->id,
        *     'field'   => array(
        *         'name'        => array('type' => 'input', 'value' => $department->name, 'requried' => true),
        *         'phone'       => array('type' => 'input', 'value' => $department->phone),
        *         'desc'        => array('type' => 'textarea', 'value' => $department->desc),
        *         'parent_id'   => array('type' => 'select', 'value' => 'department', 'label' => 'which department'),
        *         'order'       => array('type' => 'input', 'value' => $department->order),
        *         ),
        *     );
        *
        * 对应 smarty widget 示例
        * {include file="widget/form_input.tpl" label='department phone' id='phone' val=$department->phone}
        */
        foreach ($data['field'] as $field => $item)
        {
            $label = isset($item['label']) ? $item['label'] : $field;
            if (substr($label, 0, 3) == 'id_') {
                $label = substr($label, 3);
            }
            $data['fields'][] = array(
                'type'      => $item['type'], 
                'label'     => $label,
                'id'        => $field, 
                'value'     => isset($item['value']) ? $item['value'] : null, 
                'required'  => (isset($item['required']) ? $item['required'] : false),
                );
            $data['tpl'] = str_replace('_', '/', $data['type']);
        }
        return $data;
    }

    /**
     * 根据传入的参数 引用 并设置视图 table 组件的显示格式
     * 
     * @param array $data
     * @return array
     */
    public static function getTableFields($data)
    {
        /* 
        * 在控制器里把表格的参数传递给视图
        * 参数结构 示例
        * $data = array(
        *     'type'    => 'product', 
        *     'field'   => array(
        *         'name'      => array('title' => 'product name', 'width' ='80'),
        *         'category'  => array('title' => 'product category', 'width' ='80'),
        *         'desc'      => array('title' => 'product desc'),
        *         'active'    => array('title' => 'status'),
        *         ),
        *     );
        *
        * 对应 smarty widget 示例
        * {include file="widget/form_input.tpl" label='department phone' id='phone' val=$department->phone}
        */
        foreach ($data['field'] as $field => $item )
        {
            if (!isset($item['title'])){$item['title']=$field;}
            $data['head'][] = $item;
            $data['fields'][] = $field;
        }
        $data['tpl'] = str_replace('_', '/', $data['type']);
        return $data;
    }

    abstract public function debug();
}

