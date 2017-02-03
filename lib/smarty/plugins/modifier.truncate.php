<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @param boolean
 * @return string
 */
function smarty_modifier_truncate($string, $cutlen = 120, $endtag = '...',
                                  $break_words = false, $middle = false, $coding = 'utf-8')
{
    if (mb_strlen($string) > $cutlen) {
        $keeplen = intval($cutlen/2-1);
        $head = TL_Tools::truncateStr($string, $keeplen);
        $tail = TL_Tools::reverseStr(TL_Tools::truncateStr(TL_Tools::reverseStr($string), $keeplen));
        return $head.$endtag.$tail;
    } else {
        return $string;
    }
}

?>
