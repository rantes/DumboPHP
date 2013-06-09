<?php
/**
 * Array functions.
 *
 * Funciones para el manejo de arreglos.
 *
 * @author Javier Serrano
 * @package Core
 * @subpackage Extensions
 * @Version 3.0 November 18 2009
 */
/**
 * Convierte un objeto en arreglo.
 * @deprecated
 * @param array $arr Arreglo de parametros que contiene el objeto a convertir.
 * @param object $obj En caso de ser invocado desde un objeto
 * @return array|NULL
 */
	function toArray(&$arr, &$obj=NULL){
		$arrob=NULL;
		if(isset($obj)){
			if(!strcmp(get_parent_class($obj), 'ActiveRecord')){
				$arrob = $obj->getArray();
			}else{
				$arrob = get_object_vars($obj);
			}
			return $arrob;
		}else{
			return NULL;
		}

	}
?>
