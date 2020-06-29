<?php
session_start();
$dir = realpath('../../');
require_once "{$dir}/config/host.php";

set_include_path(
    "{$dir}/vendors".PATH_SEPARATOR.
    "{$dir}/".PATH_SEPARATOR.
    get_include_path().PATH_SEPARATOR.
    PEAR_EXTENSION_DIR.PATH_SEPARATOR.
    '/etc/dumbophp'.PATH_SEPARATOR.
    '/windows/system32/dumbophp'.PATH_SEPARATOR.
    '/windows/dumbophp'.PATH_SEPARATOR.
    "{$dir}/DumboPHP"
);
require_once('dumbophp.php');


$index = new index();
?>
