<?php
/**
* Extension de Formularios
* 
* Este archivo contiene funciones para el manejo de formularios.
* @version 1.0
* @author Javier Serrano.
* @package Core
* @subpackage Extensions
* @Version 3.0 November 18 2009
*/

	/**
	 * Metodo publico GetInput($type)
	 *
	 * Este metodo devuelve el input adecuado con respecto al tipo de dato que reciba.
	 * Se utiliza en los scaffolds para generar las vistas de los formularios.
	 * @param string $type Tipo de dato para validar (integer, varchar, text).
	 * @param object $obj En caso de ser invocado desde un objeto
	 * @return string
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
	/**
	 * Convierte un arreglo de 2 posiciones en un arreglo de set: arreglo[pos1] = pos2. Se utiliza por lo general con pos1='id'
	 * para organizar los registros para utilizarlos en selects.
	 * @param array $arr Set de par&aacute;metros
	 * @param object $obj En caso de ser invocado desde un objeto
	 * @return array
	 */
	function toOptions(&$arr, &$obj = NULL){
		$arr1 = array();
		$arraux = array();

		if(isset($obj) and is_object($obj)):
			$arr = $obj->getArray();
		endif;

			foreach($arr as $mainkey => $element):
				$arraux = array();
				foreach($element as $key => $value):
					$arraux[] = (string)$value;
				endforeach;
				$arr1[$arraux[0]] = $arraux[1];
			endforeach;
		return $arr1;
	}
	/**
	 * Convierte el dato enviado de un checkbox en entero.
	 * @param array $arr Set de par&aacute;metros
	 * @param object $obj En caso de ser invocado desde un objeto
	 * @return integer
	 */
	function checkBoxToInt(&$arr, &$obj = NULL){
		if($arr !== NULL and $arr == 'on') return 1;
		return 0;
	}
	/**
	 * Inicializa un formulario en el formato adecuado para transacciones con +turbo
	 * @param array $arr Set de par&aacute;metros
	 * @param object $obj En caso de ser invocado desde un objeto
	 * @throws Exception En caso de no ser invocado desde un objeto {@link ActiveRecord}
	 * @return string
	 */
	function form_for(&$arr, &$obj = null){
		$string = '<form';
		$arobj = null;
		$method = 'post';
		$action = '#';
		$name = '';
		$id='';
		$html = '';

		if(!empty($obj) and get_parent_class($obj) == 'ActiveRecord'){
			$arobj = $obj;
			$params = $arr[0];
		}elseif(isset($arr[0]) and (get_parent_class($arr[0][0]) == 'ActiveRecord' or get_parent_class($arr[0]) == 'ActiveRecord' ) and $obj==null){
			$arobj = $arr[0][0];
			$params = $arr[0];
		}else{
			throw new Exception("Form for must be used for a model object.");
			return null;
		}
		$name = singulars(strtolower($arobj->_TableName()));
		$action = !empty($params['action'])? $params['action'] : INST_URI.strtolower($arobj->_TableName());
		if(!empty($params['html']) and is_array($params['html'])){
			foreach($params['html'] as $element => $value){
				$html .= $element.'="'.$value.'" ';
			}
		}
		$html = trim($html);
		if(strlen($html)>0) $html = ' '.$html;
		$string .= ' method="'.$method.'" action="'.$action.'" name="'.$name.'"'.$html.'>';

		return $string;
	}
	/**
	 * Finaliza un formulario con la etiqueta </form>. Utilizada en conjunto con @link form_for().
	 * @return string
	 */
	function end_form_for(){
		return '</form>';
	}

	/**
	 * Establece un campo en el formulario segun el tipo de datos
	 * @todo implementar para checkbox, radiobutton
	 * @todo implementar validaciones
	 * @param array $params Los parametros adicionales como los elementos html.
	 * @param ActiveRecord $obj
	 * @throws Exception segun si no se usa un objeto active record o no se brinda el campo
	 */
	function input_for($params, &$obj=null){
		if(!empty($obj) and get_parent_class($obj) == 'ActiveRecord'):
			$params = $params[0];
			$stringi = '<input';
			$stringt = '<textarea';
			$strings = '<select';
			$name = '';
			$html = '';
			$type = '';
			$input = '';
			if(!empty($params[0]) or isset($params['field'])):
				// getting the field to treatment
				$field = isset($params['field'])?$params['field']:$params[0];
				// getting the name
				if(empty($params['name'])):
					$name = singulars(strtolower($obj->_TableName())).'['.$field.']';
				else:
					$name = $params['name'];
				endif;
				//getting the html attributes
				if(!empty($params['html']) and is_array($params['html'])):
					foreach($params['html'] as $element => $value):
						$html .= $element.'="'.$value.'" ';
					endforeach;
				endif;
				$html = trim($html);
				if(strlen($html)>0):
					$html = ' '.$html;
				endif;
				// getting the type of field for input
				if(!empty($params['type']) and is_string($params['type'])):
					$type = $params['type'];
				else:
					$nattype = $obj->_nativeType($field);
					switch($nattype):
						case 'INTEGER':
						case 'STRING':
						case 'INT':
						case 'VAR_CHAR':
						case 'VARCHAR':
						case 'FLOAT':
						case 'VAR_STRING';
							$type = 'text';
						break;
						case 'BLOB':
						case 'TEXT':
							$type = 'textarea';
						break;
					endswitch;
				endif;
				// setting the type of the input
				switch ($type):
					case 'text':
					case 'hidden':
						$input = $stringi.' type="'.$type.'" name="'.$name.'"'.$html.' value="'.$obj->{$field}.'" />';
					break;
					case 'textarea':
						$input = $stringt.' type="'.$type.'" name="'.$name.'"'.$html.'>'.$obj->{$field}.'</textarea>';
					break;
					case 'select':
						$first = !empty($params['first']) ? '<option value="">'.$params['first'].'</option>' : '';
						$cont = '';

						foreach($params['list'] as $value => $option):
							$default = '';
							if($obj->{$field} == $value) $default = 'selected="selected"';
							$cont .= '<option value="'.$value.'"'.$default.'>'.$option.'</option>'.PHP_EOL;
						endforeach;
						$input = $strings.' name="'.$name.'"'.$html.'>'.$cont.'</select>';
					break;
				endswitch;
			else:
				throw new Exception("Must to give the field to input.");
				return null;
			endif;
		else:
			throw new Exception("Input for must to be an instance of an Active Record object.");
			return null;
		endif;
		return $input;
	}
?>