<?php
session_start();
$dir = dirname(__FILE__);
include_once $dir.'/../../config/host.php';
set_include_path(implode(PATH_SEPARATOR, array(INST_PATH . '/vendors', INST_PATH , get_include_path(),PEAR_EXTENSION_DIR, '/etc/dumbophp', '/windows/system32/dumbophp', '/windows/dumbophp', INST_PATH.'/DumboPHP')));
require_once('dumbophp.php');

$index = new index();
?>
