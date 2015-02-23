<?php
/**
 * Migrations Helpers.
 *
 * Funciones auxiliares para trabajar las migraciones.
 *
 * @author Javier Serrano
 * @package Core
 * @subpackage Extensions
 * @Version 3.0 November 18 2009
 */
/**
 * Se encarga de redireccionar la accion a la migracion es decir, segun el comportamiento en la consola, ejecuta la accion solicitada.
 * @param string $params
 * @param string $obj
 */
	function MigrationsActions($params = NULL, &$obj = NULL){
		$migrationsSelected = array();
		if(isset($_POST['migrations'])):
			$path=INST_PATH.'migrations/';
			foreach($_POST['migrations'] as $field => $value){
				if($value === 'on'):
					$Obj = 'create_'.Singulars($field);
					$migrationsSelected[Camelize($Obj)] = $field;
				elseif(preg_match('[:selected]', $field)):
					$all = false;
				endif;
			}
		endif;
//		if(isset($all) and !$all and isset($migrationsSelected)):
			$var = $migrationsSelected;
//		endif;
		
		$action = NULL;
		if(isset($_POST['migrations']['reset:all']) or isset($_POST['migrations']['reset:selected'])):
			$action = 'Reset';
		elseif(isset($_POST['migrations']['up:all']) or isset($_POST['migrations']['up:selected'])):
			$action = 'up';
		elseif(isset($_POST['migrations']['down:all']) or isset($_POST['migrations']['down:selected'])):
			$action = 'down';
		endif;
		
		if(isset($var) and isset($action)):
			foreach($var as $Obj => $Tbl){
				$file = $path.'create_'.$Tbl.'.php';
				require_once($file);
				$objaux = new $Obj();
				$objaux->{$action}();
			}
		endif;
	}
?>