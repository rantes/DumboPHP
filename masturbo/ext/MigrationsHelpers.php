<?php
	function MigrationsActions($params = NULL, &$obj = NULL){
		if(isset($_POST['migrations'])):
			$path=INST_PATH.'migrations/';
			$migrationsSelected = array();
			foreach($_POST['migrations'] as $field => $value){
				if($value === 'on'):
					$Obj = 'create_'.Singulars($field);
					$migrationsSelected[Camelize($Obj)] = $field;
				elseif(preg_match('[:selected]', $field)):
					$all = false;
				endif;
			}
		endif;
//		if(isset($all) and !$all and isset($migrationsSelected)):
			$var = $migrationsSelected;
//		endif;
		
		$action = NULL;
		if(isset($_POST['migrations']['reset:all']) or isset($_POST['migrations']['reset:selected'])):
			$action = 'Reset';
		elseif(isset($_POST['migrations']['up:all']) or isset($_POST['migrations']['up:selected'])):
			$action = 'up';
		elseif(isset($_POST['migrations']['down:all']) or isset($_POST['migrations']['down:selected'])):
			$action = 'down';
		endif;
		
		if(isset($var) and isset($action)):
			foreach($var as $Obj => $Tbl){
				$file = $path.'create_'.$Tbl.'.php';
				require_once($file);
				$objaux = new $Obj();
				$objaux->{$action}();
			}
		endif;
	}
?>