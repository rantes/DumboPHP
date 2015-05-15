<?php
/**
 * Dispatcher class; handles the process of get the url and show what is needed
 * @author rantes
 * @todo routes to handle different errors
 * @todo handles the different requires
 */
if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']) && !empty($argv)){
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
}
/**
 * Se encarga de la captura de los requests y encapsula todo el proceso de hacer los llamado y responder al navegador.
 * @author rantes
 *
 */
class index{
	function __construct(){
		if(isset($_GET['_error'])){
			switch($_GET['_error']){
				case '401':
				case '403':
				case '404':
				case '500':
					echo 'Error! ('.$_GET['_error'].')';
				break;
			}
			die();
		}
		if(isset($_GET['url'])){
			$request = explode("/", $_GET['url']);
			unset($_GET['url']);
		}
		$path=INST_PATH.'app/controllers/';

		if(!isset($request[0]) or strcmp($request[0], "") === 0) $request[0] = DEF_CONTROLLER;

		if(!isset($request[1]) or strcmp($request[1], "") === 0) $request[1] = DEF_ACTION;

		$controllerFile=$request[0]."_controller.php";
		$controller=array_shift($request);
		$action = array_shift($request);

		foreach($request as $key => $value){
			if(empty($value)) unset($request[$key]);
		}
		$params = array();

		if(sizeof($request) === 1 and !strstr($request[0], "=") and is_numeric($request[0])){
			$params['id'] = $request[0];
		}elseif(sizeof($request)>0 and strstr($request[0], "=")){
			for($i=0; $i<sizeof($request); $i++){
				$p = explode('=',$request[$i]);
				if(isset($p[1])){
					$params[$p[0]] = $p[1];
				}else{
					$params[] = $p[0];
				}
				unset($request[$i]);
			}
		}elseif(sizeof($request)>0){
			foreach($request as $index => $varParam){
				$params[] = $varParam;
				unset($request[$index]);
			}
		}
		if(is_array($params)){
			$params = array_merge($params, $_GET);
		}else{
			$params = $_GET;
		}


		if(defined('SITE_STATUS') and SITE_STATUS == 'MAINTENANCE'){
			$urlToLand = explode('/',LANDING_PAGE);
			$replace = false;
			if (LANDING_REPLACE == 'ALL'){
				$replace = true;
			}else{
				$locations = explode(',',LANDING_REPLACE);
				if(in_array($controller.'/'.$action, $locations)){
					$replace = true;
				}
			}
			if($replace){
				$controller = $urlToLand[0];
				$action = $urlToLand[1];
				$controllerFile = $controller.'_controller.php';
			}
		}
		if(!file_exists($path.$controllerFile) and defined('USE_ALTER_URL') and USE_ALTER_URL){
			$params['alter_controller'] = $controller;
			$params['alter_action'] = $action;
			$parts = explode('/',ALTER_URL_CONTROLLER_ACTION);
			$controller = $parts[0];
			$action = $parts[1];
			$controllerFile = $controller.'_controller.php';
		}
		define('_CONTROLLER', $controller);
		define('_ACTION', $action);
		define('_FULL_URL', INST_URI._CONTROLLER.'/'._ACTION.'/?'.http_build_query($params));
		if(file_exists($path.$controllerFile)){
			require($path.$controllerFile);
			$classPage = Camelize($controller)."Controller";
			$page = new $classPage();
			$page->params($params);
			//loads of helpers
			if(isset($page->helper) and sizeof($page->helper) > 0){
				$page->LoadHelper($page->helper);
			}
			//before filter, executed before the action execution
			if(method_exists($page,"before_filter")){
				$actionsToExclude = $controllersToExclude = array();

				if(!empty($page->excepts_before_filter) && is_array($page->excepts_before_filter)){
					if(!empty($page->excepts_before_filter['actions']) && is_string($page->excepts_before_filter['actions'])){
						$actionsToExclude = explode(',', $page->excepts_before_filter['actions']);
						foreach($actionsToExclude as $index => $act){
							$actionsToExclude[$index] = trim($act);
						}
					}
					if(!empty($page->excepts_before_filter['controllers']) && is_string($page->excepts_before_filter['controllers'])){
						$controllersToExclude = explode(',', $page->excepts_before_filter['controllers']);
						foreach($controllersToExclude as $index => $cont){
							$controllersToExclude[$index] = trim($cont);
						}
					}
				}
				if(!in_array($controller, $controllersToExclude) && !in_array($action, $actionsToExclude)){
					$page->before_filter();
				}
			}
			if(method_exists($page,$action."Action")){
				$page->{$action."Action"}();
				//before render, executed after the action execution and before the data renderize
				if(method_exists($page,"before_render")){
					$actionsToExclude = $controllersToExclude = array();

					if(!empty($page->excepts_before_render) && is_array($page->excepts_before_render)){
						if(!empty($page->excepts_before_render['actions']) && is_string($page->excepts_before_render['actions'])){
							$actionsToExclude = explode(',', $page->excepts_before_render['actions']);
							foreach($actionsToExclude as $index => $act){
								$actionsToExclude[$index] = trim($act);
							}
						}
						if(!empty($page->excepts_before_render['controllers']) && is_string($page->excepts_before_render['controllers'])){
							$controllersToExclude = explode(',', $page->excepts_before_render['controllers']);
							foreach($controllersToExclude as $index => $cont){
								$controllersToExclude[$index] = trim($cont);
							}
						}
					}
					if(!in_array($controller, $controllersToExclude) && !in_array($action, $actionsToExclude)){
						$page->before_render();
					}
				}

				$page->display();

				if(method_exists($page,"after_render")){
					$actionsToExclude = $controllersToExclude = array();

					if(!empty($page->excepts_after_render) && is_array($page->excepts_after_render)){
						if(!empty($page->excepts_after_render['actions']) && is_string($page->excepts_after_render['actions'])){
							$actionsToExclude = explode(',', $page->excepts_after_render['actions']);
							foreach($actionsToExclude as $index => $act){
								$actionsToExclude[$index] = trim($act);
							}
						}
						if(!empty($page->excepts_after_render['controllers']) && is_string($page->excepts_after_render['controllers'])){
							$controllersToExclude = explode(',', $page->excepts_after_render['controllers']);
							foreach($controllersToExclude as $index => $cont){
								$controllersToExclude[$index] = trim($cont);
							}
						}
					}
					if(!in_array($controller, $controllersToExclude) && !in_array($action, $actionsToExclude)){
						$page->after_render();
					}
				}
			}else{
				echo "Missing Action";
			}
		}else{

			echo "Missing Controller";
		}
	}

}
?>
