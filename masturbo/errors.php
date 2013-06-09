<?php
/**
 * Gestionador de errores en las transacciones de ActiveRecord.
 * @author Javier Serrano
 * @package Core
 * @subpackage ActiveRecord
 * @version 1.0
 */
/**
 * Definicion del gestionador de errores.
 * @author Javier Serrano
 * @package Core
 * @subpackage ActiveRecord
 */
class Errors{
	/**
	 * Define si se encuentran errores en la transaccion.
	 * @var boolean
	 */
	private $actived = FALSE;
	/**
	 * Contiene los mensajes de error.
	 * @var array
	 */
	private $messages = array();
	/**
	 * Contabiliza el numero de errores.
	 * @var integer
	 */
	private $counter = 0;
	/**
	 * Establece cada uno de los errores.
	 * @param array $params
	 * @throws Exception
	 */
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
	/**
	 * Metodo magico que establece la cadena respresentada del objeto de errores encontrados en la transaccion.
	 * @return string
	 */
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
	/**
	 * Comprueba si existe errores.
	 * @return boolean
	 */
	public function isActived(){
		return $this->actived;
	}
	/**
	 * Obtiene los codigos de error.
	 * @return array
	 */
	public function errCodes(){
		$errorsCodes = array();
		foreach($this->messages as $field => $messages){
			foreach($messages as $message){
				$errorCodes[] = $message['code'];
			}
		}
		return $errorCodes;
	}
	/**
	 * Verifica si existe un codigo de erro especifico.
	 * @param string|integer $code
	 * @return boolean
	 */
	public function hasErrorCode($code = NULL){
		return in_array($code, $this->errCodes());
	}
}
?>
