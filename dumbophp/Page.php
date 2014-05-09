<?php
/**
 * Page.
 *
 * Clase que administra los controladores.
 *
 * Se encarga de cargar todos los modelos y demas funciones generales.
 * @author Javier Serrano
 * @package Core
 * @subpackage Controllers
 * @Version 3.0 November 18 2009
 * @extends Core_General_Class
 */
/**
 *Page.
 *
 * Clase que administra los controladores.
 *
 * Se encarga de cargar todos los modelos y demas funciones generales.
 * @extends Core_General_Class
 * @package Core
 * @subpackage Controllers
 */
abstract class Page extends Core_General_Class{
	/**
	 * Define el layout a renderizar por controlador. Tambien se puede definir en una accion.
	 * @var string
	 */
	protected $layout = "";
	/**
	 * Define el comportamiento de la renderizacio;n.
	 * Puede contener:
	 * 'layout' => boolean [true|false] para indicar si renderiza o no un layout.
	 * 'text' => string [texto] para renderizar solo texto. No intenta renderizar una vista asociada.
	 * 'partial' => string [ruta/hacia/el/partial] intenta renderizar un partial expresado.
	 * @var array
	 */
	protected $render = NULL;
	/**
	 * Establece el mensaje global a mostrar en la vista.
	 * @var string
	 */
	protected $flash = "";
	/**
	 * El contenido renderizado de la accion para mostrar en el layout;
	 * @var string
	 */
	protected $content = "";
	/**
	 * Arreglo que contiene los parametros que se envian por $_GET.
	 * @var array
	 */
	protected $params = array();
	/**
	 * Etiqueta description del meta encabezado.
	 * @var string
	 */
	protected $metaDescription = '';
	/**
	 * T&acute;tulo de la pagina.
	 * @var string
	 */
	protected $pageTitle = '';
	/**
	 * Controlador actual.
	 * @var string
	 */
	protected $controller = '';
	/**
	 * Accion actual.
	 * @var string
	 */
	protected $action = '';
	/**
	 * Respuesta que se va a dar en un evento AJAX
	 * @var string
	 */
	private $_respondToAJAX = '';
	/**
	 * Verificacion si se puede responder a un evento AJAX
	 * @var boolean
	 */
	private $_canrespondtoajax = false;
	/**
	 * Arreglo que contiene las acciones y controladores en las que no se van a ajecutar el metodo before_filter
	 * los controladores y acciones, deben estar descritas en un string separados por comas (,)
	 * de tipo [controllers]=>'controladores,que_no,ejecutara_el_metodo', [actions] => 'acciones, que_no_ejecutaran, el_metodo';
	 * @var array
	 */
	protected $excepts_before_filter = array();
	/**
	 * Contenido html renderizado
	 * @var String
	 */
	protected $htmlcontent= '';
	/**
	 * Contiene controladores y acciones que no ejecutaran el callback after_filter
	 * los controladores y acciones, deben estar descritas en un string separados por comas (,)
	 * de tipo [controllers]=>'controladores,que_no,ejecutara_el_metodo', [actions] => 'acciones, que_no_ejecutaran, el_metodo';
	 * @var Array
	 */
	protected $excepts_after_filter = array();
	/**
	 * Contiene controladores y acciones que no ejecutaran el callback after_render
	 * los controladores y acciones, deben estar descritas en un string separados por comas (,)
	 * de tipo [controllers]=>'controladores,que_no,ejecutara_el_metodo', [actions] => 'acciones, que_no_ejecutaran, el_metodo';
	 * @var Array
	 */
	protected $excepts_after_render = array();
	/**
	 * Contiene controladores y acciones que no ejecutaran el callback after_render
	 * los controladores y acciones, deben estar descritas en un string separados por comas (,)
	 * de tipo [controllers]=>'controladores,que_no,ejecutara_el_metodo', [actions] => 'acciones, que_no_ejecutaran, el_metodo';
	 * @var Array
	 */
	protected $excepts_before_render = array();
	/**
	 * Define si mostrar el html generado
	 * @var Boolean
	 */
	protected $outputHtml = true;
	/**
	 * Metodo magico para el auto cargado de los modelos.
	 * @param unknown $var
	 * @return unknown
	 */
	public function __get($var){
		$model = unCamelize($var);
		if(file_exists(INST_PATH.'app/models/'.$model.'.php')):
			if(!class_exists($var)):
				require INST_PATH.'app/models/'.$model.'.php';
			endif;
			$obj = new $var();
			return $obj;
		endif;
	}
	/**
	 * Metodo para renderizar la accion.
	 * @param unknown $view
	 */
	public function display($view){
		$renderPage = TRUE;
		$this->action = _ACTION;
		$this->controller = _CONTROLLER;
		if(property_exists($this, 'noTemplate') and in_array($view['action'], $this->noTemplate)) $renderPage = FALSE;
		if($this->canRespondToAJAX()){
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: application/json');
			if(!empty($this->params['callback'])){
				echo $this->params['callback'].'(';
			}
			echo $this->respondToAJAX();
			if(!empty($this->params['callback'])) echo ');';
			exit();
		}
		if(isset($this->render) and is_array($this->render)):
			if(!empty($this->render['file'])):
				$view = $this->render['file'];
			elseif(isset($this->render['partial'])):
				$view = $view['controller'].'/_'.$this->render['partial'].'.phtml';
			elseif(isset($this->render['text'])):
				$this->content = $this->render['text'];
				$renderPage = FALSE;
			elseif(!empty($this->render['action'])):
				$view = $view['controller'].'/'.$this->render['action'].'.phtml';
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
			$this->htmlcontent = ob_get_clean();
		else:
			$this->htmlcontent = $this->content;
		endif;
		if($this->outputHtml){
			echo $this->htmlcontent;
		}
	}
	/**
	 * Metodo para cargar los helpers definidos en el controlador.
	 * @param string $helper
	 */
	public function LoadHelper($helper=NULL){
		if(isset($helper) and is_array($helper)):
			foreach($helper as $file){
				require_once(INST_PATH."app/helpers/".$file."_Helper.php");
			}
		elseif(isset($helper) and is_string($helper)):
			 require_once(INST_PATH."app/helpers/".$helper."_Helper.php");
		endif;
	}
	/**
	 * Establece parametros GET.
	 * @param string $params
	 */
	public function params($params = NULL){
		$this->params = $params;
	}
	/**
	 * Da respuesta a una peticion AJAX
	 * @param JSON $val
	 * @return JSON
	 */
	public function respondToAJAX($val = null){
		if($val === null):
			return $this->_respondToAJAX;
		else:
			$this->_respondToAJAX = $val;
			$this->_canrespondtoajax = true;
		endif;
	}
	public function canRespondToAJAX(){
		return $this->_canrespondtoajax;
	}
}

?>