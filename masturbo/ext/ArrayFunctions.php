<?php
/**
 * Convierte un objeto en arreglo.
 * @deprecated
 * @param string $type Tipo de dato para validar (integer, varchar, text).
 * @param object $obj En caso de ser invocado desde un objeto
 * @return multitype:|NULL
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
