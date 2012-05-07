<?php
define('INST_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
define( 'INST_URI', 'http://'.$_SERVER['HTTP_HOST'].'/' );
define( 'SECURE_URL', '');
define('ON_DEVELOP', FALSE); //todo: use of this...


/** routes  **/
define('DEF_CONTROLLER', 'index'); //default controller/
define('DEF_ACTION', 'index'); //default view/

ini_set('display_errors', 1);
ini_set('max_execution_time',0);
ini_set('allow_url_include', 'On');
set_time_limit(0);
error_reporting(E_ALL);
?>
