<?php
/**
 * Proceso hijo de TestUncaughtErrors: corre con el dispatcher real (testDispatcher) una suite de
 * tests/support/suites/ y deja su salida y su código de salida para inspeccionarlos.
 *   php run_suite.php NombreSuite [--halt]
 */
require_once dirname(__DIR__) . '/bootstrap.php';

use DumboPHP\lib\Timothy\testDispatcher;

spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/suites/' . substr(strrchr('\\' . $class, '\\'), 1) . '.php';
    str_starts_with($class, 'tests\\') && file_exists($file) && require_once $file;
});

$suite = $argv[1] ?? 'FailureAckSuite';
$halt  = in_array('--halt', $argv, true);
$log   = sys_get_temp_dir() . '/';

$dispatcher = new testDispatcher([$suite], __DIR__ . '/suites/', $halt, false, $log);
$dispatcher->run($suite);
