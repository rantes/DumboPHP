<?php
define('ENVIRONMENT', 'development'); // Change environment once uploaded to prod or another one.

$environment = new stdClass();

$environment->production = new stdClass();
$environment->development = new stdClass();

$environment->development->instPath = dirname(dirname(__FILE__).'/'); //change this line
$environment->development->instUri = 'http://localhost'; //change this line
$environment->development->displayErrors = 1;
$environment->development->errorReporting = E_ALL;
$environment->development->analytics= null;

$environment->production->instPath = dirname(dirname(__FILE__).'/'); //change this line
$environment->production->instUri = 'http://localhost'; //change this line
$environment->production->displayErrors = 0;
$environment->production->errorReporting = 0;
$environment->production->analytics = null;

define('INST_PATH', $environment->{ENVIRONMENT}->instPath.'/');
define( 'INST_URI', $environment->{ENVIRONMENT}->instUri.'/' );
define('SITE_STATUS','LIVE');
define('LANDING_PAGE','index/landing');
define('LANDING_REPLACE','ALL');

define('DEF_CONTROLLER', 'index');
define('DEF_ACTION', 'index');
define('USE_ALTER_URL', true);
define('ALTER_URL_CONTROLLER_ACTION','index/router');

ini_set('display_errors', $environment->{ENVIRONMENT}->displayErrors);
error_reporting($environment->{ENVIRONMENT}->errorReporting);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('upload_tmp_dir', INST_PATH.'uploaded');

define('SALT', '8c4fb7bf681156b52fea93442c7dffc9'); // Always change this string.
?>
