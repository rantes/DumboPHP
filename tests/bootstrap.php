<?php
/**
 * Bootstrap for the DumboPHP self-test environment.
 *
 * INST_PATH points at tests/ so the framework finds app/models, app/controllers,
 * app/views and migrations/ under this directory (the framework hardcodes those
 * paths relative to INST_PATH).
 *
 * Loading order matters:
 *   1. INST_PATH must exist before dumbophp.php is parsed (class property
 *      defaults such as Config::$file reference it at declaration time).
 *   2. host.php defines APP_ENV before the Connection is built.
 *   3. DB is created last, once the framework classes are available.
 */
defined('INST_PATH') || define('INST_PATH', __DIR__ . '/');
$GLOBALS['env'] = 'test';

set_include_path(
    dirname(__DIR__) . PATH_SEPARATOR .
    dirname(__DIR__) . '/bin' . PATH_SEPARATOR .
    dirname(__DIR__) . '/lib' . PATH_SEPARATOR .
    get_include_path()
);

require_once 'dumbophp.php';
require_once INST_PATH . 'config/host.php';

spl_autoload_register(function (string $class): void {
    $parts = explode('\\', $class);
    $root  = $parts[0];
    $file  = null;

    if ($root === 'DumboPHP') {
        // DumboPHP\lib\db_drivers\sqlite -> lib/db_drivers/sqlite.php (via include_path)
        require_once implode('/', array_slice($parts, 1)) . '.php';
        return;
    }

    if ($root === 'App') {
        // App\Models\User -> app/models/user.php
        $segments = array_map('DumboPHP\\unCamelize', $parts);
        $file     = INST_PATH . implode('/', $segments) . '.php';
    } elseif ($root === 'Migrations') {
        // Migrations\CreateUsers -> migrations/CreateUsers.php (fallback: snake_case)
        // Migrations\Seeds       -> seeds/Seeds.php
        $short = end($parts);
        $file  = INST_PATH . 'migrations/' . $short . '.php';
        file_exists($file) || ($file = INST_PATH . 'migrations/' . DumboPHP\unCamelize($short) . '.php');
        file_exists($file) || ($file = INST_PATH . 'seeds/' . $short . '.php');
    } elseif ($root === 'tests') {
        // tests\TestActiveRecord -> suites/TestActiveRecord.php
        $file = INST_PATH . 'suites/' . end($parts) . '.php';
    }

    $file && file_exists($file) && require_once $file;
});

defined('DB') || define('DB', new DumboPHP\Connection());
