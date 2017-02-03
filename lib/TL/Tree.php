<?php
/**
 * 树形类 处理树形数据结构问题
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class TL_Tree
{    
    // 过滤数组
    public static function filterGroup($data)
    {
        $result = array();
        foreach ($data as $item){
            // 如果当前条目为失效 或没有子条目(当前已经被包含在上级子条目)
            // 则直接返回
            if (!$item['active']){
                continue;
            }
    
            $key = $item['pinyin'];
            $result[$key] = self::filterData($item);
        }
        return $result;
    }
    
    // 过滤数据
    public static function filterData($item)
    {
        $key = $item['pinyin'];
        $result[$key] = array(
                'name' => TL_Tools::unicode_encode($item['name']),
        );
        if (count($item['sub'])){
            $sub = self::filterGroup($item['sub']);
            $result[$key]['sub'] = $sub;
        }
        return $result[$key];
    }
    
    public static function updatePath($id, $parent_id, $cls)
    {
        $parent_path = '';
        if (!empty($parent_id)) {
            $parent = new $cls($parent_id);
            if (empty($parent->path)) {
                $parent_path = $parent->id.'.';
            } else {
                $parent_path = $parent->path;
            }
        }
        $obj = new $cls($id);
        $obj->path = $parent_path.$obj->id.'.';
        $obj->save();
        return $obj->path;
    }

}