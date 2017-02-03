<?php
define('_IN_DEV_', true);
require '../../config/_initialize.php';

$message = new Message('admin');
$limit = 1000;

do {
    $all = $message->get($limit);
    foreach ($all as $v) {
        $message_admin = new Message_Admin();
        $message_admin->fromArray($v);
        $message_admin->insert();
    }
} while (count($all) == $limit);

