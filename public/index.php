<?php
/**
 * web 主程序入口 
 *
 * @copyright Leo Tan <tanjnr@gmail.com>
 * @author Leo Tan <tanjnr@gmail.com>
 * @package main
 * @version 1.0
 * @license GNU Lesser General Public License
 */
//error_reporting(0);
require '../config/_initialize.php';
/* 
* 运行控制器
*/
TL_Controller::getInstance()->dispatch();