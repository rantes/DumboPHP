<?php
define('_VERSION_', '1.8b-r1');

/**
 * Converts a file to DataURI (for no more request to server)
 * Can recieve a string of the full path file or can accept an array with 2 positions: 'file'=>FULL_PATH_FILE, 'mime'=>MIME_TYPE
 * @param mixed $params [required:full_path_file],[optional:mime_type]
 * @return string
 */
function getDataURI($params = null) {
	if (!empty($params)):
		if(is_string($params)):
			$file = $params;
			$mime = '';
		elseif(is_array($params) and !empty($params['file'])):
			$file = $params['file'];
			$mime = !empty($params['mime']) ? $params['mime'] : null;
		else:
			throw new Exception('Must to give params to get the dataURI');
		endif;
		return 'data: '.(function_exists('mime_content_type') ? mime_content_type($file) : $mime).';base64,'.base64_encode(file_get_contents($file));
	else:
		throw new Exception('Must to give params to get the dataURI');
	endif;
}

//carga de las extensiones instaladas.
$path=dirname(__FILE__).'/ext/';
$directory=dir($path);
$content = array();
while (($file = $directory->read()) != FALSE):
	if($file !="." and $file != ".." and $file != "_notes"):
		if(preg_match("/.php/", $file)):
			include($path.$file);
		endif;
	endif; 
endwhile;
$directory->close();

// these functions are for casting the data from Active Records
function toString($val){
	return (string)$val;
}
function toInteger($val){
	return (integer) $val;
}
function toInt($val){
	return (integer) $val;
}
function toFloat($val){
	return floatval($val);
}
/**
 * Clase Core_General_Class.
 *
 * @author Javier Serrano <rantes.javier@gmail.com>
 * @version 1.0
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Javier Serrano <http://www.rantes.info/>
 * @package Core
 * @extends ArrayObject
 */
 /**
 * Clase Core_General_Class.
 *
 * Clase heredada de la clase ArrayObject y se encarga de cargar todas las funciones que se necesitan en el
 * framework, ya sean propias de cada clase o de esta o de las agregadas por extensiones como plugins.
 *
 * Define el método mágico __call() que se encarga de la carga dinámica de los métodos no definidos en los objetos
 * de tipo controlador o modelo.
 *
 * @access abstract
 */
abstract class Core_General_Class extends ArrayObject{
	/**
	 * método mágico __call()
	 * 
	 * Este método es ejecutado por PHP cuando se accede a un m?todo que no existe.
	 * A través de este método mágico se realiza el mapeo relacional, asumiendo que el llamado
	 * a un método, es el llamado a un modelo realiza la subconsulta según las condiciones de relación y devuelve el 
	 * objeto del modelo requerido.
	 * Es imperativo que las variables {@link $has_many}, {@link $belongs_to}, {@link $has_one}, {@link $has_and_belongs_to}, 
	 * estén correctamente creadas con las tablas a las que tienen su relación.
	 * Tambien define el comportamiento cuando se hace un llamado a un método no definido en la clase, invocando a la función 
	 * definida en las extensiones.
	 * Incluso define el comportamiento del método Find_by_{campo_en_la_tabla}().
	 *
	 * @param string $ClassName Nombre del método al que se invoca.
	 * @param mixed $val Valor pasado por parámetro dentro del método.
	 */	
	
	public function __call($ClassName, $val = NULL){
		$field = Singulars(strtolower($ClassName));
		$classFromCall = Camelize($ClassName);
		if(preg_match('/Find_by_/', $ClassName)):
			$nustring = str_replace("Find_by_", '',$ClassName);
			return $this->Find(array('conditions'=>'`'.$nustring."`='".$val[0]."'"));
		elseif (file_exists(INST_PATH.'app/models/'.$field.'.php')):
			$way = (isset($val[0]))? $val[0] : 'down';
			$foreign = strtolower($field)."_id";
			$prefix = unCamelize(get_class($this));
			if(!class_exists($classFromCall)):
				require_once INST_PATH.'app/models/'.$field.'.php';
			endif;
			$obj1 = new $classFromCall();
			$conditions = "`".$prefix."_id`='".$this->id."'";
			if(get_parent_class($obj1) == 'ActiveRecord'):
				if($classFromCall == get_class($this) and in_array($ClassName,$this->has_many_and_belongs_to)):
					$conditions = ($way=='up')? "id='".$this->{$foreign}."'" : $conditions;
				elseif(in_array($ClassName, $this->belongs_to)):
					$conditions = "id='".$this->{$foreign}."'";
				endif;
				return ($conditions !== NULL)?$obj1->Find(array('conditions'=>$conditions)):$obj1->Niu();
			endif;
			return NULL;
		else:
			return $ClassName($val, $this);
		endif;
	}
}
?>