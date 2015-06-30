<?php
session_start();
$dir = dirname(__FILE__);
include_once $dir.'/../../config/host.php';

include_once(INST_PATH.'/dumbophp.php');

$index = new index();
?>
