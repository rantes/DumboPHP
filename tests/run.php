<?php
/**
 * Entry point for the DumboPHP self-test suite.
 *
 *   php tests/run.php                       # run every suite in tests/suites/
 *   php tests/run.php TestActiveRecord ...  # run only the named suites
 *   php tests/run.php --verbose             # show per-assertion progress
 *   php tests/run.php --halt                # stop at the first failure
 *
 * Unlike the stock `dumboTest` binary this does not require XDebug and resolves
 * INST_PATH to tests/ via bootstrap.php, so it runs straight from the repo root.
 */
require_once __DIR__ . '/bootstrap.php';

use DumboPHP\lib\Timothy\testDispatcher;

$verbose = in_array('--verbose', $argv, true);
$halt    = in_array('--halt', $argv, true);

$dir     = INST_PATH . 'suites/';
$logPath = INST_PATH . 'tmp/';
is_dir($logPath) || mkdir($logPath, 0775, true);

$named = array_values(array_filter(array_slice($argv, 1), fn($a) => ($a[0] ?? '-') !== '-'));
if ($named) {
    $tests = $named;
} else {
    $tests = [];
    foreach (glob($dir . '*.php') as $file) {
        $tests[] = basename($file, '.php');
    }
}
sort($tests);

if (empty($tests)) {
    fwrite(STDERR, "No test suites found in {$dir}\n");
    exit(1);
}

$dispatcher = new testDispatcher($tests, $dir, $halt, $verbose, $logPath);
foreach ($tests as $test) {
    $dispatcher->run($test);
}
// testDispatcher::__destruct prints the PASS/FAILED banner and sets the exit code.
