<?php
/*
* Extensi�n de Formularios
* 
* Este archivo contiene funciones para el manejo de formularios.
* @version 1.0
* @author Javier Serrano.
*/
	
	/**
	 * Metodo publico GetInput($type)
	 *
	 * Este metodo devuelve el input adecuado con respecto al tipo de dato que reciba.
	 * Se utiliza en los scaffolds para generar las vistas de los formularios.
	 * @param string $type Tipo de dato para validar (integer, varchar, text).
	 */	
	function GetInput($type, &$obj=NULL){
		if($obj!=NULL) $type = $type[0];
		$type = strtolower($type);
		if(strpos($type, 'int') !== FALSE or strpos($type, 'integer') !== FALSE or strpos($type, 'varchar') !== FALSE or strpos($type, 'float') !== FALSE):
			return 'text';
		elseif(strpos($type, 'text') !== FALSE):
			return 'textarea';
		endif;
	}
	
	function toOptions(&$arr, &$obj = NULL){
		$arr1 = array();
		$arraux = array();

		if(isset($obj) and is_object($obj) and !isset($arr[0])):
			$arr = $obj->getArray();
		endif;

//		if($obj->counter() > 1):
			foreach($arr as $element){
				$arraux = array_values($element);
				$arr1[$arraux[0]] = $arraux[1];
			}
//		else:
//			$arraux = array_values($arr);
//			if(sizeof($arraux) > 0)
//				$arr1[$arraux[0]] = $arraux[1];
//			else
//				$arr1[0] = '';
//		endif;
		return $arr1;
	}
	
	function checkBoxToInt(&$arr, &$obj = NULL){
		if($arr !== NULL and $arr == 'on') return 1;
		return 0;
	}
?>