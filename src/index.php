<?php
session_start();
$dir = realpath('./../');
defined('INST_PATH') || define('INST_PATH', dirname($dir).'/');
set_include_path(
    INST_PATH.'vendor/rantes/dumbophp'.PATH_SEPARATOR.
    INST_PATH.'vendor'.PATH_SEPARATOR.
    INST_PATH.PATH_SEPARATOR.
    get_include_path().PATH_SEPARATOR.
    PEAR_EXTENSION_DIR.PATH_SEPARATOR.
    '/etc/dumbophp'.PATH_SEPARATOR.
    '/windows/system32/dumbophp'.PATH_SEPARATOR.
    '/windows/dumbophp'.PATH_SEPARATOR.
    INST_PATH.'DumboPHP'
);

require_once 'dumbophp.php';
require_once INST_PATH.'config/host.php';

$index = new index();
?>
