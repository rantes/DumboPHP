<?php
/**
 * Dispatcher class; handles the process of get the url and show what is needed
 * @author rantes
 * @package Core
 * @subpackage Controllers
 * @todo routes to handle different errors
 * @todo handles the different requires
 */
if(!empty($argv)):
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
endif;
/**
 * Se encarga de la captura de los requests y encapsula todo el proceso de hacer los llamado y responder al navegador.
 * @author rantes
 * @package Core
 * @subpackage Controllers
 */
class index{
	/**
	 * Se captura la url y se maneja seg&uacute;n el request. Los parametros via $_GET, pasan al arreglo @link $params.
	 * @todo manejo de vistas para los errores
	 */
	function __construct(){
		if(isset($_GET['_error'])):
			switch($_GET['_error']):
				case '401':
				case '403':
				case '404':
				case '500':
					echo 'Error! ('.$_GET['_error'].')';
				break;
			endswitch;
			die();
		endif;

		if(isset($_GET['url'])):
			$request = explode("/", $_GET['url']);
			unset($_GET['url']);
		endif;

		$path=INST_PATH.'app/controllers/';


		if(!isset($request[0]) or strcmp($request[0], "") === 0) $request[0] = DEF_CONTROLLER;

		if(!isset($request[1]) or strcmp($request[1], "") === 0) $request[1] = DEF_ACTION;

		$controllerFile=$request[0]."_controller.php";
		$controller=array_shift($request);
		$action = array_shift($request);

		foreach($request as $key => $value):
			if(!is_numeric($value) and empty($value)) unset($request[$key]);
		endforeach;
		$params = array();

		if(sizeof($request) === 1 and !strstr($request[0], "=") and is_numeric($request[0])):
			$params['id'] = $request[0];
		elseif(sizeof($request)>0 and strstr($request[0], "=")):
			for($i=0; $i<sizeof($request); $i++):
				$p = explode('=',$request[$i]);
				if(isset($p[1])):
					$params[$p[0]] = $p[1];
				else:
					$params[] = $p[0];
				endif;
				unset($request[$i]);
			endfor;
		elseif(sizeof($request)>0):
			foreach($request as $index => $varParam):
				$params[] = $varParam;
				unset($request[$index]);
			endforeach;
		endif;
		if(is_array($params)):
			$params = array_merge($params, $_GET);
		else:
			$params = $_GET;
		endif;


		if(defined('SITE_STATUS') and SITE_STATUS == 'MAINTENANCE'):
			$urlToLand = explode('/',LANDING_PAGE);
			$replace = false;
			if (LANDING_REPLACE == 'ALL'):
				$replace = true;
			else:
				$locations = explode(',',LANDING_REPLACE);
				if(in_array($controller.'/'.$action, $locations)):
					$replace = true;
				endif;
			endif;
			if($replace):
				$controller = $urlToLand[0];
				$action = $urlToLand[1];
				$controllerFile = $controller.'_controller.php';
			endif;
		endif;
		// verificacion de requisitos para masturbo
		if($controller === 'InstallMasTurbo'):
			// Contenido de la verificacion de requisitos de masturbo
			$verifyContent = array('PDO'=>array('installed'=>false,'message'=>'No tienes instalada la extension PDO, es necesaria para continuar y de una vez, la extension del motor DB que vas a usar.'),
									'PEAR::Mail'=>array('installed'=>false,'message'=>'No tienes instalado el plugin PEAR::Mail, es requerido para la funcionalidad de envio de emails por smtp para que nunca pasen tus email por spam.'));
			if (extension_loaded ('PDO')):
				$verifyContent['PDO']['istalled'] = true;
				$verifyContent['PDO']['message'] = 'Extension PDO cargada, por favor verifique que este cargada la libreria PDO necesaria para el motor BD que se va a utilizar.';
			endif;
			if((require 'Mail.php') !== false and class_exists('Mail')):
				$verifyContent['PEAR::Mail']['istalled'] = true;
				$verifyContent['PEAR::Mail']['message'] = 'Libreria PEAR::Mail cargada.';
			endif;
			$controller = 'console';
			$controllerFile = $controller.'_controller.php';
		endif;
		define('_CONTROLLER', $controller);
		define('_ACTION', $action);
		define("_FULL_URL", INST_URI._CONTROLLER.'/'._ACTION.'/'.implode('/', $params));

		if(file_exists($path.$controllerFile)):
			require($path.$controllerFile);
			$classPage = Camelize($controller)."Controller";

			$page = new $classPage();

			$page->params($params);

			$page->Controller = $controller;
			$page->Action = $action;
			//loads of helpers
			if(isset($page->helper) and sizeof($page->helper) > 0):
				$page->LoadHelper($page->helper);
			endif;

			if(method_exists($page,$action."Action")):

				$page->{$action."Action"}();

				$page->display(array('controller'=>$controller,'action'=>$action));
			else:
				echo "Missing Action";
			endif;
		else:
			echo "Missing Controller";
		endif;
	}

}
?>
