<?php
	/**
	 * Crea una etiqueta html para la inclusi&oacute;n de una imagen
	 *
	 * @param string|array $params los parametros de construccion que puede ser un string con la ruta de la imagen o un array de opciones.
	 */
	function image_tag($params, &$obj=NULL){
		$rute = 'images/';
		$params = ($obj === NULL)? $params : $params[0];
		if(is_array($params)):

			if(isset($params['image'])):
				if(isset($params['rute'])):
					if($params['rute'] == 'absolute'):
						$rute = INST_URI.$rute;
					else:
						$rute = '/'.$rute;
					endif;
				else:
					$rute = '/'.$rute;
				endif;
				$params['image'] = $params['image'];
				$html_options = '';
				if(isset($params['html'])):
					foreach($params['html'] as $attr => $value):
						$html_options .= " $attr=\"$value\"";
					endforeach;
				endif;

				if(isset($params['alt'])) $html_options .= ' alt="'.$params['alt'].'"';
				if(isset($params['border'])) $html_options .= ' border="'.$params['border'].'"';
				return '<img src="'.INST_URI.'images/'.$params['image'].'" '.$html_options.' />';
			endif;
		elseif(is_string($params)):
			$image = $params;
			return '<img src="'.INST_URI.'images/'.$image.'" />';
		endif;
	}
	function stylesheet_link_tag($params, &$obj=NULL){
		$css = NULL;
		if(!is_array($params) and is_string($params)) $css = $params;
		elseif(isset($params[0]) and sizeof($params) === 1) $css = $params[0];
		elseif(isset($params['css'])) $css = $params['css'];
		if($css === NULL):
			throw new Exception('Must specify a css file');
		elseif(!file_exists(INST_PATH.'app/webroot/css/'.$css)):
			throw new Exception('The file specified do not exists: '.INST_PATH.'app/webroot/css/'.$css);
		else:
			$media = 'all';
			$type = 'text/css';
			$rel = 'stylesheet';
			if(is_array($params)):
				if(isset($params['type'])) $type = $params['type'];
				if(isset($params['rel'])) $rel = $params['rel'];
				if(isset($params['media'])) $media = $params['media'];
			endif;
			$css .= '?'.time();
			return "<link href=\"".INST_URI."css/$css\" type=\"$type\" rel=\"$rel\" media=\"$media\"  />";
		endif;
	}
	/**
	 * Recibe un arreglo como parametro en el que se define la url y otras opciones.
	 *
	 * @param array $params[0]= string $content
	 * @param array $params[url]= string $url
	 * @param array $params[url]= array $url[action]
	 * @param array $params[url]= array $url[controller]
	 * @param array $params[html]= array $html[attribute] = string $value
	 */
	function link_to($params, &$obj = NULL){
//		if($obj === NULL):
//			throw new Exception("link_tag must be called in instace of object by \$this.");
//			return NULL;
//		endif;
		$params = ($obj === NULL)? $params : $params[0];
		$link = '';
		$content = '';
		$html_options = '';
		$action = _ACTION;
		$controller = _CONTROLLER;
		//$link = INST_URI.$controller.'/'.$action;
		if(isset($params)):
			if(is_string($params) and strlen($params) > 0):
				$content = $params;
			elseif(is_array($params)):
				if(isset($params['controller'])):
					$controller = $params['controller'];
					unset($params['controller']);
					$link = INST_URI.$controller.'/';
				endif;
				if(isset($params['action'])):
					$action = $params['action'];
					unset($params['action']);
					$link .= $action;
				endif;
				if(isset($params['url'])):
					if(is_string($params['url'])):
						$link = $params['url'];
// 					elseif(is_array($params['url'])):
// 						if(isset($params['action'])) $action = $params['action'];
// 						if(isset($params['controller'])) $controller = $params['controller'];
// 						$link = INST_URI.$controller.'/'.$action;
					endif;
					unset($params['url']);
				endif;
				if(isset($params['params'])):
					$link .= '/'.$params['params'];
				endif;
				if(isset($params[0])):
					$content = $params[0];
					unset($params[0]);
//				else:
//					throw new Exception("Must provide a content for link");
				endif;
				if(isset($params['html'])):
					foreach($params['html'] as $attr => $value){
						$html_options .= " $attr=\"$value\"";
					}
					unset($params['html']);
				endif;

				if(sizeof($params) > 0 and !is_array($params)):
					if(sizeof($params) === 1):
						list($var) = $params;
						if(key($params) == 'id'):
							$link .= "/$var";
						else:
							$link .= "/?".key($params)."=".$var;
						endif;
					else:
						$link .= "/?";
						foreach($params as $var => $val){
							$link .= "$var=$val&";
						}
						$link = substr($link, 0, -1);
					endif;
				endif;
			endif;
			if(strlen($link)>0) $link = 'href="'.$link.'"';
			return "<a ".$link." $html_options>$content</a>";
//		else:
//			throw new Exception("Must provide content for link.");
		endif;
	}
	function javascript_include_tag($params, &$obj = NULL){
//		$params = $params[0];
		$js = '';
		$params = ($obj === NULL)? $params: $params[0];
		if(isset($params) or $params != NULL):
			if(is_string($params) and strlen($params) > 0):
				preg_match("@plugins/[.]*@U", $params, $arr);
				if(!empty($arr[0]) and $arr[0]=='plugins/'):
					$js = INST_URI.$params.'.js';
				else:
					$js = INST_URI."js/".$params.'.js';
				endif;
				return "<script type=\"text/javascript\" language=\"javascript\" src=\"$js\"></script>";
			elseif(is_array($params) and sizeof($params) > 0):
				$string = '';
				foreach($params as $file){
					preg_match("@plugins/[.]*@U", $file, $arr);
					if(!empty($arr[0]) and $arr[0]=='plugins/'):
						$js = INST_URI.$file.'.js';
					else:
						$js = INST_URI."js/".$file.'.js';
					endif;
					$string .= "<script type=\"text/javascript\"  src=\"$js\"></script>";
				}
				return $string;
			else:
				throw new Exception("Must give a valid string for file name.");
				return NULL;
			endif;
		else:
			throw new Exception("Must to give a file name.");
			return NULL;
		endif;
	}
?>
