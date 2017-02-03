<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {eval} function plugin
 *
 * Type:     function<br>
 * Name:     eval<br>
 * Purpose:  evaluate a template variable as a template<br>
 * @link http://smarty.php.net/manual/en/language.function.eval.php {eval}
 *       (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 */
function smarty_function_tpl_exists($params, &$smarty)
{
    $tpl_file   = $smarty->template_dir.DIRECTORY_SEPARATOR.$params['tpl'];
    $result     = file_exists($tpl_file); 

    if (!empty($params['assign'])) {
        $result = $result ? $tpl_file : false;
        $smarty->assign($params['assign'], $result);
    }else {
        return $result;
    }
}

/* vim: set expandtab: */

?>
