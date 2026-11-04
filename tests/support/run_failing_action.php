<?php
/**
 * Proceso hijo de TestUncaughtErrors: ejecuta con Timothy (_runAction) una
 * acción que deja escapar un Error y escribe en STDOUT el código HTTP y si la
 * ejecución siguió. El detalle completo debe llegar por STDERR.
 */
require_once dirname(__DIR__) . '/bootstrap.php';

use DumboPHP\lib\Timothy\dumboTests;

$test = new dumboTests(sys_get_temp_dir() . '/dumbo_uncaught_child.log');
$page = $test->_runAction($argv[1] ?? 'fail/type');
fwrite(STDOUT, 'CONTINUED code=' . http_response_code() . ' failure=' . get_class($page->failure()));
