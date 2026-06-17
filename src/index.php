<?php
$dir = realpath('./../');
defined('INST_PATH') || define('INST_PATH', dirname($dir).'/');
set_include_path(
    '/etc/dumbophp'.PATH_SEPARATOR.
    '/etc/dumbophp/bin'.PATH_SEPARATOR.
    '/etc/dumbophp/lib'.PATH_SEPARATOR.
    INST_PATH.'vendor'.PATH_SEPARATOR.
    INST_PATH.'vendor/rantes/dumbophp'.PATH_SEPARATOR.
    INST_PATH.'vendor/rantes/dumbophp/bin'.PATH_SEPARATOR.
    INST_PATH.'vendor/rantes/dumbophp/lib'.PATH_SEPARATOR.
    INST_PATH.PATH_SEPARATOR.
    get_include_path().PATH_SEPARATOR.
    PEAR_EXTENSION_DIR.PATH_SEPARATOR.
    '/windows/dumbophp'.PATH_SEPARATOR.
    '/windows/dumbophp/bin'.PATH_SEPARATOR.
    '/windows/dumbophp/lib'.PATH_SEPARATOR.
    '/windows/system32/dumbophp'.PATH_SEPARATOR.
    '/windows/system32/dumbophp/bin'.PATH_SEPARATOR.
    '/windows/system32/dumbophp/lib'.PATH_SEPARATOR.
    INST_PATH.'DumboPHP'
);

require_once 'dumbophp.php';
require_once INST_PATH.'config/host.php';

spl_autoload_register(function($className) {
    $path = explode('\\', $className);
    if ($path[0] === 'DumboPHP'):
        require_once implode('/', array_slice($path, 1)).'.php';
    else:
        for ($i = 0; $i < sizeof($path); $i++):
            $path[$i] = DumboPHP\unCamelize($path[$i]);
        endfor;
        $path = implode('/', $path);
        require_once INST_PATH."{$path}.php";
    endif;
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$index = new DumboPHP\index();
$index->page->display();
