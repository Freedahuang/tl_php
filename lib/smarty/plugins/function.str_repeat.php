<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {counter} function plugin
 *
 * 用法 {link controller="index" action="index" option="option"}
 *
 * Type:     function<br>
 * Name:     counter<br>
 * Purpose:  print out a counter value
 * @author Monte Ohrt <monte at ohrt dot com>
 * @link http://smarty.php.net/manual/en/language.function.counter.php {counter}
 *       (Smarty online manual)
 * @param array parameters
 * @param Smarty
 * @return string|null
 */
function smarty_function_str_repeat($params, &$smarty)
{
    if (!isset($params['repeat']) || !isset($params['str']))
        return 'invalid params';
    return str_repeat($params['repeat'], $params['str']);
}

/* vim: set expandtab: */

?>
