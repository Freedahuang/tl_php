<?php
/**
 * 抽象的业务处理类 主要由业务类继承对数据库表进行操作
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package library
 * @version 1.0
 * @license GNU Lesser General Public License
 */


/**
 * 用于实现一些业务处理的公共方法 如加载对象
 *
 * 注意：
 * 继承类扩展是添加的对应数据库表字段的对象属性必须指定与数据库字段一致的默认值
 * 如NULL值等
 *
 * @package library
 * @author Leo Tan <tanjnr@gmail.com>
 * @version 1.0
 */
class Ext_Model_Hash extends Ext_Model
{
    protected $cls_num_tbl = 8;
    
    public function __construct($identifier=false)
    {
        if (!empty($identifier)) {
            $mod_tbl = Ext_Tools::getMod($identifier, $this->cls_num_tbl);
            $this->cls_tbl .= $mod_tbl;
        }
        parent::__construct($identifier);
    }
    
    public function tblInit()
    {
        list($db, $tbl) = explode('.', $this->cls_tbl);
        $config = $this->getDbConfig('default');
        if (!empty($config)) {
            for ($i=0; $i<$this->cls_num_tbl; $i++) {
                $sql = 'CREATE TABLE '.$this->cls_tbl.$i.' LIKE '.$config['name'].'.example_'.$tbl;
                $this->rawExecute($sql);
            }
        }
    }
}