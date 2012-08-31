<?php
/**
 * Dispatcher class; handles the proces of get the url and show what is needed
 * @author rantes
 * @todo routes to handle different errors
 *
 */
class index{
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
			if(empty($value)) unset($request[$key]);
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