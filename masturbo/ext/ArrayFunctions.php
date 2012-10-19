<?php
	function CountArray(&$arr, &$obj=NULL){
		$num = 0;
		if(isset($arr[0])) $num = count($arr[0]);
		return $num;
	}
	
	function toArray(&$arr, &$obj=NULL){
		$arrob=NULL;
		if(isset($obj)):
			if(!strcmp(get_parent_class($obj), 'ActiveRecord')):
				$arrob = $obj->getArray();
			else:
				$arrob = get_object_vars($obj);
			endif;
			return $arrob;
		else:
			return NULL;
		endif;
		
	}
	
	function DelNulls(&$arr, &$obj=NULL){
		$newArr = array();
		foreach($arr[0] as $key => $value){
			if(!in_array($key, $arr[1]) or ($value != NULL or $value != '' or isset($value))):
				$newArr[$key] = $value;
			endif;
		}
		return $newArr;
	}
	
	function first(&$arr, &$obj=NULL){
		if($obj !== NULL and get_parent_class($obj)=='ActiveRecord'):
			foreach($obj as $min){
				return $min;
			}
		endif;
	}
?>
