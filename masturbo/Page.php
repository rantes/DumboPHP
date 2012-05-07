<?php

/**
 *
 * Clase Page.
 *
 * Clase que administra los controladores.
 * 
 * Se encarga de cargar todos los modelos y demas funciones generales.
 * @author Javier Serrano
 * @package Core
 * @subpackage Controllers
 * @Version 3.0 November 18 2009
 */
abstract class Page extends Core_General_Class{

	protected $layout = "";
	protected $render = NULL;
	protected $flash = "";
	protected $content = "";
	protected $params = array();
	protected $metaDescription = '';
	protected $pageTitle = '';
	protected $controller = '';
	protected $action = '';
	
	public function __get($var){
		$model = unCamelize($var);
		if(file_exists(INST_PATH.'app/models/'.$model.'.php')):
			if(!class_exists($var)):
				require_once INST_PATH.'app/models/'.$model.'.php';
			endif;
			$obj = new $var();
			return $obj; 
		endif;
	}
	public function display($view){
		$renderPage = TRUE;
		$this->action = _ACTION;
		$this->controller = _CONTROLLER;
		if(property_exists($this, 'noTemplate') and in_array($view['action'], $this->noTemplate)) $renderPage = FALSE;
		define("_FULL_URL", INST_URI.'/'._CONTROLLER.'/'._ACTION.'/'.implode('/', $this->params));

		if(isset($this->render) and is_array($this->render)):
			if(isset($this->render['file'])):
				$view = $this->render['file'];
			elseif(isset($this->render['partial'])):
				$view = $view['controller'].'/_'.$this->render['partial'].'.phtml';
			elseif(isset($this->render['text'])):
				$this->content = $this->render['text'];
				$renderPage = FALSE;
			else:
				$view = $view['controller'].'/'.$view['action'].'.phtml';
			endif;
		else:
			$view = $view['controller'].'/'.$view['action'].'.phtml';
		endif;
		
		if($renderPage):
			ob_start();
			include_once(INST_PATH."app/templates/".$view);
			$this->content = ob_get_clean();
		endif;
		
		
		if(isset($this->render['layout']) and $this->render['layout'] !== false):
			$this->layout = $this->render['layout'];
		endif;
		
		if(isset($this->render['layout']) and $this->render['layout'] === false):
			$this->layout = '';
		endif;
		
		if(strlen($this->layout)>0):
			ob_start();
			include_once(INST_PATH."app/templates/".$this->layout.".phtml");
			$buffer = ob_get_clean();
			echo $buffer;
		else:
			echo $this->content;
		endif;
	}
	
	public function LoadHelper($helper=NULL){
		if(isset($helper) and is_array($helper)):
			foreach($helper as $file){
				require_once(INST_PATH."app/helpers/".$file."_Helper.php");
			}
		elseif(isset($helper) and is_string($helper)):
			 require_once(INST_PATH."app/helpers/".$helper."_Helper.php");
		endif;
	}
	public function params($params = NULL){
		$this->params = $params;
	}
}

?>