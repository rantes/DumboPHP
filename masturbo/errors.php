<?php

class Errors{
	private $actived = FALSE;
	private $messages = array();
	private $counter = 0;
	
	function add($params = NULL){
		if($params === NULL or !is_array($params)):
			throw new Exception("Must to give an array with the params to add.");
		else:
			if(isset($params['field']) and isset($params['message'])):
				$this->messages[$params['field']][] = array('message'=>$params['message'], 'code'=>isset($params['code'])?$params['code']:'');
				$this->counter++;
				$this->actived = TRUE;
			else:
				throw new Exception("Must to give an array with the params to add.");
			endif;
		endif;
	}
	
	function __toString(){
		$strmes = '';
		foreach($this->messages as $field => $messages){
			$strmes .= "Error on $field field: \n";
			foreach($messages as $message){
				$strmes .= "\t".$message['message']."\n";
			}
		}
		return $strmes;
	}
	
	public function isActived(){
		return $this->actived;
	}

	public function errCodes(){
		$errorsCodes = array();
		foreach($this->messages as $field => $messages){
			foreach($messages as $message){
				$errorCodes[] = $message['code'];
			}
		}
		return $errorCodes;
	}
	public function hasErrorCode($code = NULL){
		return in_array($code, $this->errCodes());
	}
}
?>
