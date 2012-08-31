<?php
define('INST_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
define( 'INST_URI', 'http://'.$_SERVER['HTTP_HOST'].'/' );
define( 'SECURE_URL', '');
define('ON_DEVELOP', FALSE); //todo: use of this...

define('SITE_STATUS','LIVE'); //status of the page: LIVE MAINTENANCE
define('LANDING_PAGE','index/landing'); //landing page
define('LANDING_REPLACE','ALL'); //instead of these pages, show landing ALL or comma separated | index/index,index/another,index/contact

/** routes  **/
define('DEF_CONTROLLER', 'console'); //default controller/
define('DEF_ACTION', 'index'); //default view/


ini_set('display_errors', 1);
ini_set('max_execution_time',0);
ini_set('allow_url_include', 'On');
ini_set('upload_tmp_dir', INST_PATH.'uploaded');

set_time_limit(0);
error_reporting(E_ALL);
?>
