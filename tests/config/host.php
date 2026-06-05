<?php
/**
 * Minimal host constants for the DumboPHP self-test environment.
 * INST_PATH is normally defined by bootstrap.php before this file is loaded,
 * pointing at tests/ so the framework resolves app/ and migrations/ from here.
 */
defined('INST_PATH')      || define('INST_PATH', dirname(__DIR__) . '/');
defined('APP_ENV')        || define('APP_ENV', 'test');
defined('DEF_CONTROLLER') || define('DEF_CONTROLLER', 'test');
defined('DEF_ACTION')     || define('DEF_ACTION', 'index');
defined('INST_URI')       || define('INST_URI', 'http://localhost/');
defined('SITE_STATUS')    || define('SITE_STATUS', 'LIVE');
defined('LANDING_PAGE')   || define('LANDING_PAGE', 'test/index');
defined('SALT')           || define('SALT', 'test_salt_dumbophp');
defined('USE_ALTER_URL')  || define('USE_ALTER_URL', false);

$GLOBALS['env'] = 'test';
