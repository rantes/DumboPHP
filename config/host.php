<?php
/**
 * Config file.
 *
 * @author Javier Serrano <rantes.javier@gmail.com>
 * @version 1.0
 * @license BSD 3
 * @copyright Javier Serrano <http://www.rantes.info/>
 * @package Core
 * @subpackage Aplication Config
 * @todo Implementaci&oacute;n y uso de los diferentes entornos (development, test, production)
 */
/**
 * Establece el path absoluto de la carpeta en la que est&aacute; el proyecto.
 * Por defecto, el valor es la ruta de DOCUMENT_ROOT
 * @var String|CONSTANT
 */
define('INST_PATH', $_SERVER['DOCUMENT_ROOT'].'/'); // SIEMPRE DEBE LLEVAR SLASH AL FINAL
/**
 * Establece la URL absoluta para la navegaci&oacute;n.
 * Se utiliza para dar rutas absolutas a los elementos linkeados.
 * Por defecto, el valor es HTTP_HOST.
 * @var String|CONSTANT
 */
define( 'INST_URI', 'http://'.$_SERVER['HTTP_HOST'].'/' ); // SIEMPRE DEBE LLEVAR SLASH AL FINAL
// define( 'SECURE_URL', ''); // En caso de necesitarse la definicion de la constante de un secure url
// define('ON_DEVELOP', FALSE); //todo: use of this...
/**
 * Define el estado del sitio, si est&aacute; en vivo o en mantenimiento.
 * Se utiliza para cuando sea necesario mostrar un landing page.
 * Solo puede manejar LIVE o MAINTENANCE.
 * Si no se define, por defecto, es LIVE.
 * @var String|CONSTANT
 */
define('SITE_STATUS','LIVE'); //status of the page: LIVE MAINTENANCE
/**
 * En caso de establecer el sitio en matenimiento, esta es la ruta a renderizar.
 * Siempre de la forma controlador/acci&oacute;n.
 * @var String|CONSTANT
 */
define('LANDING_PAGE','index/landing'); //landing page
/**
 * En caso de establecer el sitio en matenimiento, se puede definir si reemplazar todas las URLs o solo unas en especial.
 * Siempre de la forma controlador/acci&oacute;n,controlador/acci&oacute;n1,controlador1/acci&oacute;n2.
 * @var String|CONSTANT
 */
define('LANDING_REPLACE','ALL'); //instead of these pages, show landing ALL or comma separated | index/index,index/another,index/contact

// rutas
/**
 * Controlador por defecto
 * @var String|CONSTANT string
 */
define('DEF_CONTROLLER', 'console'); //default controller/
/**
 * Acci&oacute;n por defecto.
 * @var String|CONSTANT string
 */
define('DEF_ACTION', 'index'); //default view/
/**
* Define si va a utilizar url alternas en vez del convencional controlador/accion.
* Si el controlador/accion llamado existe, entonces lo ejecuta.
* @var String|CONSTANT boolean
*/
define('USE_ALTER_URL', false);
/**
 * Controlador que se va a encargar de los enrutamientos alternos.
 * @var String|CONSTANT string
 */
define('ALTER_URL_CONTROLLER_ACTION','alter/index');

// configuraciones generales sobre PHP
ini_set('display_errors', 1); // Para mostrar errores, en caso de no querer visualizarlos, cambiar a 0.
ini_set('max_execution_time',0); // Para que el script no finalice por timeout.
ini_set('upload_tmp_dir', INST_PATH.'uploaded'); // Definicion de un path para los uploads temporales.

set_time_limit(0); // Para que el script no finalice por timeout.
error_reporting(E_ALL); // Nivel de reporte de errores, E_ALL por defecto para mostrar absolutamente todo. Para desactivar, usar 0.
?>
