<?php
/**
 * Migraciones
 *
 * @author Javier Serrano <rantes.javier@gmail.com>
 * @version 1.0
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Javier Serrano <http://www.rantes.info/>
 * @package Core
 * @extends Core_General_Class
 */
require_once('GeneralCore.php');
require_once('ActiveRecord.php');

/**
* Clase NewAr.
*
* Clase heredada de {@link ActiveRecord} y solo se usa para implementacion de las funciones de active records.
*/
class NewAr extends ActiveRecord{}
/**
 *
 * Clase Migrations.
 *
 * Clase heredada de la clase Core_General_Class y se encarga de las funciones de las migraciones como lo son
 * {@link Up()}, {@link Down()} y {@link Reset()}
 *
 * @author Javier Serrano
 * @package Core
 * @Version 3.0 November 18 2009
 */
abstract class Migrations extends Core_General_Class{
	/**
	* Atributo protegido $create_table;
	*
	* Este atributo es un arreglo que contiene los datos necesarios para la creacion de la tabla que luego
	* se define en el archivo de migracion.
	* @var array $create_table
	*/
	//protected $create_table = array();

	/**
	* Metodo constructor.
	*/
	function __construct(){}
	/**
	* Metodo destructor.
	*/
	function __destruct(){}
	/**
	* Metodo abstracto Up().
	*
	* Este metodo se define luego en la clase migraci&oacute;n y sirve para la creacion de la tabla en la base de datos.
	*/
	public function up(){}
	/**
	* Metodo abstracto Down().
	*
	* Este metodo se define luego en la clase migraci&oacute;n y sirve para la eliminacion de la tabla en la base de datos.
	*/
	public function down(){}
	/**
	* Metodo publico Reset().
	*
	* Este metodo sirve para la eliminaci&oacute;n y creacion de la tabla en la base de datos.
	*/
	function Reset(){
		$this->down();
		$this->up();

	}
	//Creaciones de tablas

	/**
	* Metodo protegido Create_Table($table=NULL).
	*
	* Este metodo se encarga de crear la tabla con el parametro $table que contiene el nombre de la tabla y los
	* campos con sus respectivos atributos.
	* @param array $table Arreglo que contiene el nombre de la tabla, campos y atributos.
	*/
	protected function Create_Table($table = NULL){
		defined('AUTO_AUDITS') or define('AUTO_AUDITS', true);
		if($table !== NULL):

			$tablName = $table['Table'];
			$query = "CREATE TABLE `".$tablName."` (";
			$presentedId = false;
			foreach($table as $key => $Field){
				if(strcmp($key, 'Table') != 0){
					if($Field['type'] == 'VARCHAR' and empty($Field['limit'])) $Field['limit'] = 250;
					if(in_array('id', $Field)) $presentedId = true;
					$query .= (!empty($Field['field']) and !empty($Field['type']))? "`".$Field['field']."` ".$Field['type'] : NULL;
					$query .= (!empty($Field['limit']))? " (".$Field['limit'].")" : NULL;
					$query .= ($Field['type'] == 'VARCHAR' or $Field['type'] == 'TEXT' or $Field['type'] == 'LONGTEXT')?' CHARACTER SET utf8 COLLATE utf8_general_ci':NULL;
					$query .= (!empty($Field['null']))? " NOT NULL" : NULL;
					$query .= (!empty($Field['default']))? " DEFAULT '".$Field['default']."'" : NULL;
					$query .= (!empty($Field['comments']))? " COMMENT '".$Field['comment']."'" : NULL;
					$query .= " ,";
				}
			}
			if(!$presentedId){
				$query .= "`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
			}
			if(AUTO_AUDITS){
				$query .= "created_at INT NOT NULL ,";
				$query .= "updated_at INT NOT NULL ";
			}
			$query .= ")CHARACTER SET utf8 COLLATE utf8_general_ci;";
			$Ar = new NewAr();
			$Ar->Connect();
			$Ar->driver->exec($query);
			$Ar->WriteSchema($tablName);
			$Ar->WriteModel($tablName);

		endif;
	}
	/**
	*
	* Este metodo se encarga de eliminar la tabla con el parametro $table que contiene el nombre de la tabla.
	* @param string $table Cadena de texto que contiene el nombre de la tabla.
	*/
	protected function Drop_Table($table){
		$query = "DROP TABLE `".$table."`";
		$Ar = new NewAr();
		$Ar->Connect();
		$Ar->driver->exec($query);
	}
	//Alteraciones sobre las tablas

	/**
	* Este metodo se encarga de alterar la tabla con el parametro $columns que contiene el nombre de la tabla y el campo
	* con sus respectivos atributos.
	* @param array $columns Arreglo que contiene el nombre de la tabla, campo y atributos.
	*/
	protected function Add_Column($columns = NULL){
		if($columns !== NULL or !is_array($columns)):
			//$tablName = $columns['Table'];
			if(!isset($columns['length']) and strcmp($columns['type'], 'integer') != 0) $columns['length'] = '255';
			$query = "ALTER TABLE `".$columns['Table']."` ADD `".$columns['field']."` ".strtoupper($columns['type']);
			$query .= (isset($columns['length']) and $columns['length'] != '')? "(".$columns['length'].")" : NULL;
			$query .= (isset($columns['null']) and $columns['null'] != '')? " ".strtoupper($columns['null']) : NULL;
			$query .= (isset($columns['default']) and $columns['default'] != '')? " DEFAULT '".$columns['defualt']."'" : NULL;
//			foreach($columns as $key => $Field){
//				if(strcmp($key, 'Table') != 0):
//					$query .= (isset($Field['field']) and isset($Field['type']))? $Field['field']." ".$Field['type'] : NULL;
//					$query .= (isset($Field['limit']))? " (".$Field['limit'].")" : NULL;
//					$query .= (isset($Field['null']) and $Field['null'])? " NOT NULL" : NULL;
//					$query .= " ,";
//				endif;
//			}
//			$query .= "created_at INT NOT NULL ,";
//			$query .= "updated_at INT NOT NULL ";
//			$query .= ")";

			$Ar = new NewAr();
			$Ar->Connect();
			$Ar->driver->exec($query);

//			$Ar->WriteSchema($tablName);
//			$Ar->WriteModel($tablName);
		else:
			throw new Exception('Cannot add a column with '.gettype($columns).'.');
		endif;
	}
	/**
	* Metodo protegido Remove_Column($column=NULL).
	*
	* Este metodo se encarga de eliminar la columna con el parametro $column que contiene el nombre de la tabla y la columna.
	* @param array $column Arreglo de 2 posiciones contiene el nombre de la tabla y el nombre de la columna.
	*/
	protected function Remove_Column($column=NULL){
		$query = "ALTER TABLE `".$column[0]."` DROP `".$column[1]."`";
		$Ar = new NewAr();
		$Ar->Connect();
		$Ar->driver->exec($query);
	}
}
?>