<?php
defined('INST_PATH') || define('INST_PATH', realpath('.').'/');
set_include_path(
    '/etc/dumbophp'.PATH_SEPARATOR.
    INST_PATH.'vendor'.PATH_SEPARATOR.
    INST_PATH.'vendor/rantes/dumbophp'.PATH_SEPARATOR.
    INST_PATH.PATH_SEPARATOR.
    get_include_path().PATH_SEPARATOR.
    PEAR_EXTENSION_DIR.PATH_SEPARATOR.
    '/windows/system32/dumbophp'.PATH_SEPARATOR.
    '/windows/dumbophp'.PATH_SEPARATOR.
    INST_PATH.'DumboPHP'
);
if (file_exists(INST_PATH.'config/host.php')) require_once INST_PATH.'config/host.php';
require_once 'dumbophp.php';
require_once 'lib/Timothy/dumboTests.php';

defined('XDEBUG_FILTER_CODE_COVERAGE') or define('XDEBUG_FILTER_CODE_COVERAGE',0b100000000);
defined('XDEBUG_PATH_INCLUDE') or define('XDEBUG_PATH_INCLUDE',0b000000001);
defined('XDEBUG_PATH_EXCLUDE') or define('XDEBUG_PATH_EXCLUDE',0b000000010);
defined('XDEBUG_CC_UNUSED') or define('XDEBUG_CC_UNUSED',0b000000001);
defined('XDEBUG_CC_DEAD_CODE') or define('XDEBUG_CC_DEAD_CODE',0b000000010);
defined('XDEBUG_TRACE_HTML') or define('XDEBUG_TRACE_HTML',0b000000100);

xdebug_set_filter(
    XDEBUG_FILTER_CODE_COVERAGE,
    XDEBUG_PATH_INCLUDE,
    [
        INST_PATH . 'app' . DIRECTORY_SEPARATOR . 'controllers',
        INST_PATH . 'app' . DIRECTORY_SEPARATOR . 'models',
        INST_PATH . 'app' . DIRECTORY_SEPARATOR . 'helpers'
    ]
);
