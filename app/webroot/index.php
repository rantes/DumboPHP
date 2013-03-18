<?php
session_start();
include_once '../../config/host.php';
set_include_path(implode(PATH_SEPARATOR, array(INST_PATH . 'vendors', INST_PATH . 'masturbo', get_include_path(),PEAR_EXTENSION_DIR)));


/**
 * El archivo General_Core.php es el archivo que contiene las funciones generales que heredan las demas
 * clases y tambien se encarga de implementar las funciones creadas en archivos de extension.
 */
include("GeneralCore.php");
 /**
 * Archivo que contiene la clase de Active Records, requerido para poder cargar todos los objetos.
 */
require_once "ActiveRecord.php";

include("Page.php");

require 'Dispatcher.php';

$index = new index();
?>
