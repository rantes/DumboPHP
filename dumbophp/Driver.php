<?php
/**
 * Clase heredada de la clase PDO que se encarga de toda la conexion a las bases de datos, cualquiera que sea, y las consultas a ellas.
 *
 * @author Javier Serrano
 * @package Core
 * @subpackage ActiveRecord
 * @Version 3.0 November 18 2009
 */
/**
 * Controlador de la conexion a la base de datos, segun el archivo de configuracion ubicado en /config/db.ini
 * @author Javier Serrano
 * @package Core
 * @subpackage ActiveRecord
 * @extends PDO
 */
class Driver extends PDO
{
	/**
	* Metodo constructor.
	* @param string $file (Default) String que contiene la ruta del archivo de configuracion de la base de datos.
	*/
	function __construct($file = 'config/db_settings.ini')
	{
		if (!$settings = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');

		$dns = $settings['database']['driver'] .
		((!empty($settings['database']['host'])) ? (':host=' . $settings['database']['host']) : '') .
		((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '') .
		';dbname=' . $settings['database']['schema'] .
		((!empty($settings['database']['dialect'])) ? (';dialect=' . $settings['database']['dialect']) : '') .
		((!empty($settings['database']['charset'])) ? (';charset=' . $settings['database']['charset']) : '');
		parent::__construct($dns, $settings['database']['username'], $settings['database']['password'],array(PDO::ATTR_PERSISTENT => true));
	}
}
?>