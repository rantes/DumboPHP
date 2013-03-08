<?php
session_start();
xhprof_enable();
include_once '../../config/host.php';
set_include_path(implode(PATH_SEPARATOR, array(INST_PATH . 'vendors', INST_PATH . 'masturbo', get_include_path(),PEAR_EXTENSION_DIR)));


/**
 * El archivo General_Core.php es el archivo que contiene las funciones generales que heredan las demas
 * clases y tambien se encarga de implementar las funciones creadas en archivos de extension.
 */
include("GeneralCore.php");
 /**
 * Archivo {@link ActiveRecord.php}
 *
 * Archivo que contiene la clase de Active Records, requerido para poder cargar todos los objetos.
 */
require_once "ActiveRecord.php";

include("Page.php");

require 'Dispatcher.php';

$index = new index();
$xhprof_data = xhprof_disable();

//
// Saving the XHProf run
// using the default implementation of iXHProfRuns.
//
include_once "/home/rantes/sites/xhprof/xhprof_lib/utils/xhprof_lib.php";
include_once "/home/rantes/sites/xhprof/xhprof_lib/utils/xhprof_runs.php";

$xhprof_runs = new XHProfRuns_Default();

// Save the run under a namespace "xhprof_foo".
//
// **NOTE**:
// By default save_run() will automatically generate a unique
// run id for you. [You can override that behavior by passing
// a run id (optional arg) to the save_run() method instead.]
//
$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");

echo "---------------\n".
		"Assuming you have set up the http based UI for \n".
		"XHProf at some address, you can view run at \n".
		"http://localhost/xhprof/xhprof_html/index.php?run=$run_id&source=xhprof_foo\n".
		"---------------\n";
?>