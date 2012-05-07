<?php
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
