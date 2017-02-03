<?php
class Region extends Ext_Model_Tree
{
    public $cls_tbl = 'region';
    public $pinyin = '';
    public $type = '';

    public function getFields()
    {
        $fields = parent::getFields();

        $fields['pinyin'] = strval($this->pinyin);
        $fields['type'] = strval($this->type);
        
        return $fields;
    }

    public static function getForm($obj)
    {
        $data = parent::getForm($obj);
        $data['field']['pinyin'] = array('type' => 'input', 'value' => $obj->pinyin, 'label' => 'pinyin');
        $data['field']['type'] = array('type' => 'select', 'value' => $obj->getRegion(), 'required' => $obj->type);
        return $data;
    }

    public function getRegion()
    {
        $str = 'p=province/autonomous;t=territory;m=metropolis/state;c=district/county';
        $region = array();
        foreach (TL_Tools::strToArray($str) as $key => $val){
            $region[] = array('id' => $key, 'name' => TL_Tools::getLang($val), 'active' => true);
        }
        return $region;
    }

    public function getCascade2()
    {
        $tree = $this->getTreeByCache('region');
        $res = array();
        foreach ($tree as $province) {
            $citys = array();
            foreach ($province['sub'] as $city) {
                // $area = array();
                // foreach ($city['sub'] as $item) {
                //     $area[$item['name']] = $province['id'].','.$city['id'].','.$item['id'];
                // }
                // $citys[$city['name']] = $area;
                $citys[$city['name']] = $province['id'].','.$city['id'];
            }
            $res[$province['name']] = $citys;
        }
        return $res;
    }
}