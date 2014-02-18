<?php
/**
 * Funciones para la gestion de elementos JSON.
 *
 * @author Javier Serrano
 * @package Core
 * @subpackage Extensions
 * @Version 3.0 November 18 2009
 */
/**
 * Convierte un objeto ActiveRecord a JSON.
 * @param array $arr
 * @param string $obj
 * @return string
 */
	function toJSON(&$arr, &$obj=NULL){
		$arr = $obj->getArray();
		$jsonString = "{";
		foreach($arr as $index => $element){
			
			if(is_array($element)):
				//$jsonString .= "{";
				foreach($element as $key => $value){
					$jsonString .= '"'.$key.'":"'.$value.'",';
				}
				$jsonString = substr($jsonString, 0, -1);
				$jsonString .= "},{";
			else:
				$jsonString .= '"'.$index.'":"'.$element.'",';
			endif;
		}
		$jsonString = substr($jsonString, 0, (substr($jsonString, -1,1) == ',')?-1:-3);
		$jsonString .= "}";
		return $jsonString;
	}
	/**
	 * Convierte un dataset de tipo ActiveRecord a un arreglo JSON.
	 * @param unknown $arr
	 * @param string $obj
	 * @return string
	 */
	function toJSArray(&$arr, &$obj=NULL){
		$arr = $obj->getArray();
		$String = "[";
		foreach($arr as $index => $element){
			foreach($element as $key => $value){
				$String .= "\"$value\",";
			}
		}
		$String = substr($String, 0, -1);
		$String .= "]";
		return $String;
	}

?>
