<?php
if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']) && !empty($argv)){
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
}

define('HTTP_200', 200);
define('HTTP_201', 201);
define('HTTP_204', 204);
define('HTTP_304', 304);
define('HTTP_400', 400);
define('HTTP_401', 401);
define('HTTP_403', 403);
define('HTTP_404', 404);
define('HTTP_405', 405);
define('HTTP_406', 406);
define('HTTP_500', 500);

final class IrregularNouns {
	public $singular = array();
	public $plural = array();

	public function __construct(){
		$this->singular[] =	'abyss';
		$this->singular[] =	'alumnus';
		$this->singular[] =	'analysis';
		$this->singular[] =	'aquarium';
		$this->singular[] =	'arch';
		$this->singular[] =	'atlas';
		$this->singular[] =	'axe';
		$this->singular[] =	'baby';
		$this->singular[] =	'bacterium';
		$this->singular[] =	'batch';
		$this->singular[] =	'beach';
		$this->singular[] =	'browse';
		$this->singular[] =	'brush';
		$this->singular[] =	'bus';
		$this->singular[] =	'calf';
		$this->singular[] =	'chateau';
		$this->singular[] =	'cherry';
		$this->singular[] =	'child';
		$this->singular[] =	'church';
		$this->singular[] =	'circus';
		$this->singular[] =	'city';
		$this->singular[] =	'cod';
		$this->singular[] =	'copy';
		$this->singular[] =	'crisis';
		$this->singular[] =	'curriculum';
		$this->singular[] =	'datum';
		$this->singular[] =	'deer';
		$this->singular[] =	'dictionary';
		$this->singular[] =	'diagnosis';
		$this->singular[] =	'domino';
		$this->singular[] =	'dwarf';
		$this->singular[] =	'echo';
		$this->singular[] =	'elf';
		$this->singular[] =	'emphasis';
		$this->singular[] =	'family';
		$this->singular[] =	'fax';
		$this->singular[] =	'fish';
		$this->singular[] =	'flush';
		$this->singular[] =	'fly';
		$this->singular[] =	'foot';
		$this->singular[] =	'fungus';
		$this->singular[] =	'half';
		$this->singular[] =	'hero';
		$this->singular[] =	'hippopotamus';
		$this->singular[] =	'hoax';
		$this->singular[] =	'hoof';
		$this->singular[] =	'index';
		$this->singular[] =	'iris';
		$this->singular[] =	'kiss';
		$this->singular[] =	'knife';
		$this->singular[] =	'lady';
		$this->singular[] =	'leaf';
		$this->singular[] =	'life';
		$this->singular[] =	'loaf';
		$this->singular[] =	'man';
		$this->singular[] =	'mango';
		$this->singular[] =	'memorandum';
		$this->singular[] =	'mess';
		$this->singular[] =	'moose';
		$this->singular[] =	'motto';
		$this->singular[] =	'mouse';
		$this->singular[] =	'nanny';
		$this->singular[] =	'neurosis';
		$this->singular[] =	'nucleus';
		$this->singular[] =	'oasis';
		$this->singular[] =	'octopus';
		$this->singular[] =	'page';
		$this->singular[] =	'party';
		$this->singular[] =	'pass';
		$this->singular[] =	'penny';
		$this->singular[] =	'person';
		$this->singular[] =	'plateau';
		$this->singular[] =	'poppy';
		$this->singular[] =	'potato';
		$this->singular[] =	'purchase';
		$this->singular[] =	'quiz';
		$this->singular[] =	'reflex';
		$this->singular[] =	'runner-up';
		$this->singular[] =	'scarf';
		$this->singular[] =	'scratch';
		$this->singular[] =	'series';
		$this->singular[] =	'sheaf';
		$this->singular[] =	'sheep';
		$this->singular[] =	'shelf';
		$this->singular[] =	'son-in-law';
		$this->singular[] =	'species';
		$this->singular[] =	'splash';
		$this->singular[] =	'spy';
		$this->singular[] =	'status';
		$this->singular[] =	'stitch';
		$this->singular[] =	'story';
		$this->singular[] =	'syllabus';
		$this->singular[] =	'tax';
		$this->singular[] =	'thesis';
		$this->singular[] =	'thief';
		$this->singular[] =	'tomato';
		$this->singular[] =	'tooth';
		$this->singular[] =	'tornado';
		$this->singular[] =	'try';
		$this->singular[] =	'volcano';
		$this->singular[] =	'waltz';
		$this->singular[] =	'wash';
		$this->singular[] =	'watch';
		$this->singular[] =	'wharf';
		$this->singular[] =	'wife';
		$this->singular[] =	'woman';

		$this->plural[] =	'abysses';
		$this->plural[] =	'alumni';
		$this->plural[] =	'analyses';
		$this->plural[] =	'aquaria';
		$this->plural[] =	'arches';
		$this->plural[] =	'atlases';
		$this->plural[] =	'axes';
		$this->plural[] =	'babies';
		$this->plural[] =	'bacteria';
		$this->plural[] =	'batches';
		$this->plural[] =	'beaches';
		$this->plural[] =	'browses';
		$this->plural[] =	'brushes';
		$this->plural[] =	'buses';
		$this->plural[] =	'calves';
		$this->plural[] =	'chateaux';
		$this->plural[] =	'cherries';
		$this->plural[] =	'children';
		$this->plural[] =	'churches';
		$this->plural[] =	'circuses';
		$this->plural[] =	'cities';
		$this->plural[] =	'cod';
		$this->plural[] =	'copies';
		$this->plural[] =	'crises';
		$this->plural[] =	'curricula';
		$this->plural[] =	'data';
		$this->plural[] =	'deer';
		$this->plural[] =	'dictionaries';
		$this->plural[] =	'diagnoses';
		$this->plural[] =	'dominoes';
		$this->plural[] =	'dwarves';
		$this->plural[] =	'echoes';
		$this->plural[] =	'elves';
		$this->plural[] =	'emphases';
		$this->plural[] =	'families';
		$this->plural[] =	'faxes';
		$this->plural[] =	'fish';
		$this->plural[] =	'flushes';
		$this->plural[] =	'flies';
		$this->plural[] =	'feet';
		$this->plural[] =	'fungi';
		$this->plural[] =	'halves';
		$this->plural[] =	'heroes';
		$this->plural[] =	'hippopotami';
		$this->plural[] =	'hoaxes';
		$this->plural[] =	'hooves';
		$this->plural[] =	'indexes';
		$this->plural[] =	'irises';
		$this->plural[] =	'kisses';
		$this->plural[] =	'knives';
		$this->plural[] =	'ladies';
		$this->plural[] =	'leaves';
		$this->plural[] =	'lives';
		$this->plural[] =	'loaves';
		$this->plural[] =	'men';
		$this->plural[] =	'mangoes';
		$this->plural[] =	'memoranda';
		$this->plural[] =	'messes';
		$this->plural[] =	'moose';
		$this->plural[] =	'mottoes';
		$this->plural[] =	'mice';
		$this->plural[] =	'nannies';
		$this->plural[] =	'neuroses';
		$this->plural[] =	'nuclei';
		$this->plural[] =	'oases';
		$this->plural[] =	'octopi';
		$this->plural[] =	'pages';
		$this->plural[] =	'parties';
		$this->plural[] =	'passes';
		$this->plural[] =	'pennies';
		$this->plural[] =	'people';
		$this->plural[] =	'plateaux';
		$this->plural[] =	'poppies';
		$this->plural[] =	'potatoes';
		$this->plural[] =	'shopping';
		$this->plural[] =	'quizzes';
		$this->plural[] =	'reflexes';
		$this->plural[] =	'runners-up';
		$this->plural[] =	'scarves';
		$this->plural[] =	'scratches';
		$this->plural[] =	'series';
		$this->plural[] =	'sheaves';
		$this->plural[] =	'sheep';
		$this->plural[] =	'shelves';
		$this->plural[] =	'sons-in-law';
		$this->plural[] =	'species';
		$this->plural[] =	'splashes';
		$this->plural[] =	'spies';
		$this->plural[] =	'statuses';
		$this->plural[] =	'stitches';
		$this->plural[] =	'stories';
		$this->plural[] =	'syllabi';
		$this->plural[] =	'taxes';
		$this->plural[] =	'theses';
		$this->plural[] =	'thieves';
		$this->plural[] =	'tomatoes';
		$this->plural[] =	'teeth';
		$this->plural[] =	'tornadoes';
		$this->plural[] =	'tries';
		$this->plural[] =	'volcanoes';
		$this->plural[] =	'waltzes';
		$this->plural[] =	'washes';
		$this->plural[] =	'watches';
		$this->plural[] =	'wharves';
		$this->plural[] =	'wives';
		$this->plural[] =	'women';
	}
}

function Plurals($params, &$obj=NULL){
	if($obj === NULL) $string = $params;
	else $string = $params[0];
	$IN = new IrregularNouns();
	if(in_array($string, $IN->singular)):
		$key = array_search($string, $IN->singular);
		$strconv = $IN->plural[$key];
	elseif(in_array($string, $IN->plural)):
		$strconv = $string;
	else:
		$vowels = array('a', 'e', 'i', 'o', 'u');
		if(substr($string, -1, 1) == 'y'):
			$prec = substr($string, -2, 1);
			if(in_array($prec, $vowels)):
				$strconv = $string.'s';
			else:
				$strconv = str_replace('y', 'ies', $string);
			endif;

		elseif(substr($string, -1, 1) == 'x' or substr($string, -1, 1) == 's' or substr($string, -2, 2) == 'ch' or substr($string, -2, 2) == 'sh' or substr($string, -2, 2) == 'ss'):
			$strconv = $string.'es';
		else:
			$strconv = $string.'s';
		endif;
	endif;
	return $strconv;
}

function Singulars($params, &$obj=NULL){
	if($obj == NULL) $string = $params;
	else $string = $params[0];

	$IN = new IrregularNouns();
	$strconv = '';

	if(in_array($string, $IN->plural)):
		$key = array_search($string, $IN->plural);
		$strconv = $IN->singular[$key];
	elseif(substr($string, -3, 3) == 'ies'):
		$strconv = str_replace('ies', 'y', $string);
	elseif(substr($string, -2, 2) == 'es'):
		$test = substr($string, 0, -2);
		if(substr($test, -1, 1) == 'x' or substr($test, -1, 1) == 's' or substr($test, -2, 2) == 'ch' or substr($test, -2, 2) == 'sh' or substr($test, -2, 2) == 'ss'):
			$strconv = substr($string, 0, -2);
		else:
			$strconv = substr($string, 0, -1);
		endif;
	elseif(substr($string, -1, 1) == 's'):
		$strconv = substr($string, 0, -1);
	else:
		$strconv = $string;
	endif;
	return $strconv;
}

function Camelize($params, &$obj=NULL){
	if($obj === NULL) $string = $params;
	else $string = $params[0];
	$newName = "";
	if(preg_match("[_]", $string)):
		$names = preg_split("[_]", $string);
		$i=1;
		foreach($names as $single){
			$newName .= ucfirst($single);
			$i++;
		}
	else:
		$newName .= ucfirst($string);
	endif;
	return $newName;
}

 function ToList(&$arr, &$obj = NULL){
 	$list = '';

	if(isset($obj) and is_object($obj) and get_parent_class($obj) == 'ActiveRecord'):
		$arr = $obj->getArray();
	endif;

	if(isset($arr) and sizeof($arr) > 0):
		foreach($arr as $value){
			if(is_array($value)):
				$list .= ToList($value).',';
			else:
				$list .= $value.',';
			endif;
		}
		$list = substr($list,0,-1);

	endif;

 	return $list;
 }

 function unCamelize($params, &$obj=NULL){
	if($obj === NULL) $string = $params;
	else $string = $params[0];
	$newstring = '';

	if(isset($string) and is_string($string)):
		$string[0] = strtolower($string[0]);
		for($i = 0; $i < strlen($string); $i++){
			if(preg_match('`[A-Z]`',$string[$i])) $newstring .= '_';
			$newstring .= strtolower($string[$i]);
		}
	endif;

 	return $newstring;
 }

 function cleanToSEO($params, &$obj = NULL){
	if($obj === NULL) $string = $params;
	else $string = $params[0];

	$specialChars = array("/\xc0/","/\xc1/","/\xc2/","/\xc3/","/\xc4/","/\xc5/","/\xc6/","/\xc7/","/\xc8/","/\xc9/","/\xca/","/\xcb/","/\xcc/","/\xcd/","/\xce/","/\xcf/","/\xd0/","/\xd1/",
 			"/\xd2/","/\xd3/","/\xd4/","/\xd5/","/\xd6/","/\xd7/","/\xd8/","/\xd9/","/\xda/","/\xdb/","/\xdc/","/\xdd/","/\xde/","/\xdf/","/\xe0/","/\xe1/","/\xe2/","/\xe3/","/\xe4/","/\xe5/","/\xe6/",
 			"/\xe7/","/\xe8/","/\xe9/","/\xea/","/\xeb/","/\xec/","/\xed/","/\xee/","/\xef/","/\xf0/","/\xf1/","/\xf2/","/\xf3/","/\xf4/","/\xf5/","/\xf6/","/\xf7/","/\xf8/","/\xf9/","/\xfa/","/\xfb/","/\xfc/","/\xfd/","/\xfe/","/\xff/");
 	$normalChars =  array('A','A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','D','N',
 			'O','O','O','O','O','O','U','U','U','U','Y','B','Ss','a','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','d','n',
 			'o','o','o','o','o','o','u','u','u','u','y','b','y');
	$string = preg_replace($specialChars, $normalChars, $string);
	$string = strtolower($string);
	$string = preg_replace('/[\s]+/', '-', $string);
	$string = preg_replace('/[^a-zA-Z0-9-]/', '', $string);

	return $string;
 }

 function cleanASCII($string) {
 	$specialChars = array("/\xc0/","/\xc1/","/\xc2/","/\xc3/","/\xc4/","/\xc5/","/\xc6/","/\xc7/","/\xc8/","/\xc9/","/\xca/","/\xcb/","/\xcc/","/\xcd/","/\xce/","/\xcf/","/\xd0/","/\xd1/",
 			"/\xd2/","/\xd3/","/\xd4/","/\xd5/","/\xd6/","/\xd7/","/\xd8/","/\xd9/","/\xda/","/\xdb/","/\xdc/","/\xdd/","/\xde/","/\xdf/","/\xe0/","/\xe1/","/\xe2/","/\xe3/","/\xe4/","/\xe5/","/\xe6/",
 			"/\xe7/","/\xe8/","/\xe9/","/\xea/","/\xeb/","/\xec/","/\xed/","/\xee/","/\xef/","/\xf0/","/\xf1/","/\xf2/","/\xf3/","/\xf4/","/\xf5/","/\xf6/","/\xf7/","/\xf8/","/\xf9/","/\xfa/","/\xfb/","/\xfc/","/\xfd/","/\xfe/","/\xff/");
 	$normalChars =  array('&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;',
 			'&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&Oslash;','U','U','U','U','Y','B','Ss','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring','&aElig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&eth;','&ntilde;',
 			'&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&oslash;','u','u','u','u','y','b','y');
 	return preg_replace($specialChars, $normalChars, $string);
 }

 function strGenerate($params = null) {
 	$length = 8;
 	$case = 'both';
 	$includeNumbers = true;
 	$lowerChars = 'abcdefghijklmnopqrstuvwxyz';
 	$upperChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
 	$numberChars = '0123456789';
 	$primaryString = '';
 	$result = '';
 	if(!empty($params) && is_array($params)) {
 		if(!empty($params['case'])) {
 			$case = $params['case'];
 		}
 		if(isset($params['includeNumbers'])) {
 			$includeNumbers = $params['includeNumbers'];
 		}
 		if(!empty($params['length'])) {
 			$length = $params['length'];
 		}
 	}
 	switch ($case) {
 		case 'lower':
 			$primaryString = $lowerChars;
 		break;
 		case 'upper':
 			$primaryString = $upperChars;
 		break;
 		case 'both':
 			$primaryString = $lowerChars.$upperChars;
 		break;
 	}
 	if($includeNumbers) {
 		$primaryString .= $numberChars;
 	}
 	$max = strlen($primaryString) - 1;
 	do {
 		$pos = mt_rand(0, $max);
 		$result .= $primaryString[$pos];
 	} while(strlen($result) < $length);

 	return $result;
 }

 function GetInput($type, &$obj=NULL){
	if($obj!=NULL) $type = $type[0];
	$type = strtolower($type);
	if(strpos($type, 'int') !== FALSE or strpos($type, 'integer') !== FALSE or strpos($type, 'varchar') !== FALSE or strpos($type, 'float') !== FALSE):
		return 'text';
	elseif(strpos($type, 'text') !== FALSE):
		return 'textarea';
	endif;
}

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

function checkBoxToInt(&$arr, &$obj = NULL){
	if($arr !== NULL and $arr == 'on') return 1;
	return 0;
}

function end_form_for(){
	return '</form>';
}



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

function link_to($params = array(), &$obj = NULL){
	$params = ($obj === NULL)? $params : $params[0];
	$link = '';
	$content = '';
	$html_options = '';
	$action = _ACTION;
	$controller = _CONTROLLER;
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
				endif;
				unset($params['url']);
			endif;
			if(isset($params['params'])):
				$link .= '/'.$params['params'];
			endif;
			if(isset($params[0])):
				$content = $params[0];
				unset($params[0]);
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
	endif;
}

function javascript_include_tag($params, &$obj = NULL){
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

class Driver extends PDO {
	public $settings = null;

	function __construct($file = 'config/db_settings.ini') {
		if (!$this->settings = parse_ini_file($file, TRUE)) throw new exception('Unable to open ' . $file . '.');
		switch ($this->settings['database']['driver']) {
			case 'firebird':
				$dsn = 'firebird:dbname='.$this->settings['database']['host'].'/'.$this->settings['database']['port'].':'.$this->settings['database']['schema'];
			break;
			case 'sqlite':
			case 'sqlite2':
				if($this->settings['database']['schema'] === 'memory'){
					$dsn = $this->settings['database']['driver'].'::memory:';
				} else {
					$dsn = $this->settings['database']['driver'].':'.$this->settings['database']['schema'];
				}
			break;

			default:
				$dsn = $this->settings['database']['driver'] .
				((!empty($this->settings['database']['host'])) ? (':host=' . $this->settings['database']['host']) : '') .
				((!empty($this->settings['database']['port'])) ? (';port=' . $this->settings['database']['port']) : '') .
				';dbname=' . $this->settings['database']['schema'] .
				((!empty($this->settings['database']['dialect'])) ? (';dialect=' . $this->settings['database']['dialect']) : '') .
				((!empty($this->settings['database']['charset'])) ? (';charset=' . $this->settings['database']['charset']) : '');
			break;
		}
		empty($this->settings['database']['username']) and $this->settings['database']['username'] = null;
		empty($this->settings['database']['password']) and $this->settings['database']['password'] = null;
		parent::__construct($dsn, $this->settings['database']['username'], $this->settings['database']['password'],array(PDO::ATTR_PERSISTENT => true));
	}
}

class Errors{
	private $actived = FALSE;
	private $messages = array();
	private $counter = 0;

	public function add($params = NULL){
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

	public function __toString(){
		$strmes = '';
		foreach($this->messages as $field => $messages){
			$strmes .= "Error on $field field: \n";
			foreach($messages as $message){
				$strmes .= "\t".$message['message']."\n";
			}
		}
		return $strmes;
	}

	public function isActived(){
		return $this->actived;
	}

	public function errCodes(){
		$errorsCodes = array();
		foreach($this->messages as $field => $messages){
			foreach($messages as $message){
				$errorCodes[] = $message['code'];
			}
		}
		return $errorCodes;
	}

	public function errFields(){
		$errorsFields = array();
		foreach($this->messages as $field => $messages){
			$errorFields[] = $field;
		}
		return $errorFields;
	}

	public function hasErrorCode($code = NULL){
		return in_array($code, $this->errCodes());
	}
}

abstract class Core_General_Class extends ArrayObject{

	public function __call($ClassName, $val = NULL){
		$field = Singulars(strtolower($ClassName));
		$classFromCall = Camelize($ClassName);
		$conditions = '';
		$params = array();
		if (file_exists(INST_PATH.'app/models/'.$field.'.php')){
			$way = 'down';
			if(!empty($val[0])){
				switch ($val[0]){
					case 'up':
					case 'down':
						$way = $val[0];
					break;
					case ':first':
						$params = array(':first');
					break;
					default:
						$params = $val[0];
					break;
				}
			}
			$foreign = strtolower($field)."_id";
			$prefix = unCamelize(get_class($this));
			if(!class_exists($classFromCall)){
				require_once INST_PATH.'app/models/'.$field.'.php';
			}
			$obj1 = new $classFromCall();
			$conditions = "`".$prefix."_id`='".$this->id."'";
			if(method_exists($obj1,'Find')){
				if($classFromCall == get_class($this) and in_array($ClassName,$this->has_many_and_belongs_to)){
					$conditions = ($way=='up')? "`id`='".$this->{$foreign}."'" : $conditions;
				}elseif(in_array($ClassName, $this->belongs_to)){
					$conditions = "`id`='".$this->{$foreign}."'";
				}

				$params['conditions'] = empty($params['conditions'])? $conditions : ' AND '.$conditions;
				return ($conditions !== NULL)?$obj1->Find($params):$obj1->Niu();
			}
			return NULL;
		}elseif(preg_match('/Find_by_/', $ClassName)){
			$nustring = str_replace("Find_by_", '',$ClassName);
			return $this->Find(array('conditions'=>$nustring."='".$val[0]."'"));
		}else{
			return $ClassName($val, $this);
		}
	}
}

abstract class ActiveRecord extends Core_General_Class{
	public $PaginatePageVarName = 'page';
	public $PaginateTotalItems = 0;
	public $PaginateTotalPages = 0;
	public $PaginatePageNumber = 1;
	public $driver = NULL;
	public $_error = NULL;
	public $_sqlQuery = '';
	public $candump = true;
	public $wakedUpVars = array();
	protected $_ObjTable;
	protected $_singularName;
	protected $_counter = 0;
	protected $has_many = array();
	protected $has_one = array();
	protected $belongs_to = array();
	protected $has_many_and_belongs_to = array();
	protected $validate = array();
	protected $before_insert = array();
	protected $after_insert = array();
	protected $after_find = array();
	protected $before_find = array();
	protected $after_save = array();
	protected $before_save = array();
	protected $after_delete = array();
	protected $before_delete = array();
	protected $dependents = '';
	protected $_data = array();
	protected $_attrs = array();
	protected $_dataAttributes = array();
	protected $_models = array();
	protected $_params = array('fields'=>'*','conditions'=>'');
	protected $pk = 'id';
	protected $escapeField = array();
	private $memcached = null;
	private $engine = 'mysql';
	private $_fields = array();

	public function _init_(){}

	final public function __construct() {
		$a = func_get_args();
		defined('AUTO_AUDITS') or define('AUTO_AUDITS',true);
		// $this->_data = NULL;
		// $this->_data = array();
		// $this->_attrs = NULL;
		// $this->_attrs = array();
		$this->__destruct();
		$this->checkMemcached();
		if(!empty($a[0]) && is_object($a[0]) && get_class($a[0]) === 'Driver') {
			$this->driver = $a[0];
		} else {
			$this->Connect();
		}

		$this->_init_();
	}

	public function __destruct(){
		$this->_data = NULL;
		$this->_data = array();
		$this->_attrs = NULL;
		$this->_attrs = array();
		$this->_error = NULL;
		$this->_params = null;
	}

	public function serialize() {
		$name = get_class($this);
		$countVars = 0;
		$conts = '';
		$backtrace = debug_backtrace();
		if(sizeof($backtrace) <= 1)	throw new Exception('Active Record objects can not be storaged into session due to security reasons.');
		$serializes = null;
		$vars = get_class_vars($name);
		foreach ($vars as $var => $type){
			if(!empty($this->{$var}) && $var !== 'driver'){
				$countVars++;
				$typeof = gettype($this->{$var});
				$conts .= 's:'.strlen($var).':"'.$var.'";';
				switch ($typeof){
					case 'integer':
						$conts .= 'i:'.$this->{$var}.';';
						break;
					case 'string':
						$conts .= 's:'.strlen($this->{$var}).':"'.$this->{$var}.'";';
						break;
					case 'boolean':
						$conts .= 'b:'.((integer)$this->{$var}).';';
						break;
					case 'array':
					case 'object':
						$conts .= serialize($this->{$var});
						break;
				}
			}
		}
		$serializes = $conts;

		if($this->_counter > 1){
			$countVars += $this->_counter;
			for($i = 0; $i < $this->_counter; $i++){
				$countVars1 = 0;
				$conts = '';
				foreach ($vars as $var => $type){
					if(!empty($this[$i]->{$var}) && $var !== 'driver'){
						$countVars1++;
						$typeof = gettype($this[$i]->{$var});
						$conts .= 's:'.strlen($var).':"'.$var.'";';
						switch ($typeof){
							case 'integer':
								$conts .= 'i:'.$this[$i]->{$var}.';';
								break;
							case 'string':
								$conts .= 's:'.strlen($this[$i]->{$var}).':"'.$this[$i]->{$var}.'";';
								break;
							case 'boolean':
								$conts .= 'b:'.((integer)$this[$i]->{$var}).';';
								break;
							case 'array':
							case 'object':
								$conts .= serialize($this[$i]->{$var});
								break;
						}
					}
				}
				$serializes1 = 'O:'.strlen($name).':"'.$name.'":'.$countVars1.':{'.$conts.'}';
				$serializes .= 'i:'.$i.';C:'.strlen($name).':"'.$name.'":'.strlen($serializes1).':{'.$serializes1.'}';
			}
		}
		$serializes = 'O:'.strlen($name).':"'.$name.'":'.$countVars.':{'.$serializes.'}';
		return $serializes;
	}

	public function unserialize($data) {
		$a = unserialize($data);
		if(!empty($a->wakedUpVars['main'][1]['_counter']) && $a->wakedUpVars['main'][1]['_counter'] <= 1){
			$min = sizeof($a->wakedUpVars['main']) - $a->wakedUpVars['main'][1]['_counter'];
			for($i = 0; $i < $a->wakedUpVars['main'][1]['_counter']; $i++){
				$this->offsetSet($i, NULL);
				$classToUse = get_class($this);
				$this[$i] = new $classToUse();
				foreach($a->wakedUpVars['main'][$min+$i] as $obj){
					$this[$i] = $obj;
				}
			}
		} else {
			foreach ($a->wakedUpVars['main'] as $var){
				foreach ($var as $key => $value){
					if(is_numeric($key)){
						$this->offsetSet($key, NULL);
						$classToUse = get_class($this);
						$this[$key] = $value;
					} else {
						$this->{$key} = $value;
					}
				}
			}
		}
	}

	public function __wakeup(){
		foreach($this as $key => $value){
			$this->wakedUpVars['main'][] = array($key => $value);
		}
	}

	public function __set($name, $value) {
		if(isset($this->_data[$name]) || preg_match('/_data_/', $name) || in_array($name, $this->_fields)){
			$name = str_replace("_data_", '', $name);
			$this->_data[$name] = $value;
		} else {
			$this->_attrs[$name] = $value;
		}
	}

	public function __unset($name) {
		if(isset($this->_attrs[$name])) {
			$this->_attrs[$name] = NULL;
			unset($this->_attrs[$name]);
		}
		if (isset($this->_data[$name])) {
			$this->_data[$name] = NULL;
			unset($this->_data[$name]);
		}
		if (isset($this->{$name})) {
			$this->{$name} = NULL;
			unset($this->{$name});
		}
	}

	public function __isset($var){
		return ((!empty($this->_attrs) and array_key_exists($var, $this->_attrs)) || (!empty($this->_data) and array_key_exists($var, $this->_data)));
	}

	public function getIterator() {
            return new ArrayIterator($this);
    }

	public function __get($name) {

			switch($name){
				case '_ObjTable':
					return $this->_TableName();
				break;
				case '_errors':
					return $this->_errors;
				break;
				default:
					if (isset($this->_data[$name])){
						return $this->_data[$name];
					} elseif(isset($this->_attrs[$name])){
						return $this->_attrs[$name];
					}else{
						return null;
					}
				break;
			}
		return null;
	}

	public function Connect(){

		if($this->driver === NULL and !is_object($this->driver) and get_class($this->driver) != 'Driver'){
			$this->driver = new Driver(INST_PATH.'config/db_settings.ini');
			$this->engine = $this->driver->getAttribute(PDO::ATTR_DRIVER_NAME);
		}

		$this->_error = new Errors();

		return true;
	}

	private function checkMemcached(){
		$memcached = null;
		defined('CAN_USE_MEMCACHED') or define('CAN_USE_MEMCACHED', false);

		if(CAN_USE_MEMCACHED && empty($this->memcached)){
			$memcached = new Memcached();
			defined('MEMCACHED_HOST') or define('MEMCACHED_HOST','localhost');
			defined('MEMCACHED_PORT') or define('MEMCACHED_PORT','11211');
			$memcached->addServer(MEMCACHED_HOST, MEMCACHED_PORT);
		}
		return $memcached;
	}

	protected function getData($query){

		$this->_data = NULL;
		$this->_data = array();
		$this->_attrs = NULL;
		$this->_attrs = array();
		$result = array();

		foreach($this as $i => $val) {
			$this[$i] = null;
			$this->offsetUnset($i);
		}

		$j=0;
		$regs = NULL;
		if(empty($this->driver)) $this->connect();
		$regs = $this->driver->query($query);
		if(!is_object($regs)) die("Error in SQL Query. Please check the SQL Query: ".$query);
		$regs->setFetchMode(PDO::FETCH_ASSOC);
		$resultset = $regs->fetchAll();
		$classToUse = get_class($this);
		$count = sizeof($resultset);

		$this->_set_attributes($resultset);

		if($count > 0){
			for($j = 0; $j < $count; $j++){
				$this->offsetSet($j, new $classToUse($this->driver));

				foreach($resultset[$j] as $property => $value){
					if(!is_numeric($property)){
						if (!in_array($property, $this->_fields)) {
							$this->_fields[] = $property;
						}
						$this[$j]->_counter = 1;
						$this[$j]->{'_data_'.$property} = (!empty($this->_dataAttributes[$property]) && $this->_dataAttributes[$property]['cast']) ? 0 + $value : $value;
					}
				}
			}
		}

		$this->_counter = $j;
		if($this->_counter === 0){
			$this->offsetSet(0, NULL);
			$this[0] = NULL;
			$this->_data = NULL;
			unset($this[0]);
			$this->Niu();
		}

		if($this->_counter === 1) {
			foreach ($this->_fields as $field) {
				if(isset($this[0]->{$field})) {
					$this->{$field} = $this[0]->{$field};
				}
			}
		}
	}

	public function Find($params = NULL){
		$this->_params = null;
		$memcached = $this->checkMemcached();
		if(!empty($params)) $this->_params = $params;
		if(sizeof($this->before_find) >0){
			foreach($this->before_find as $functiontoRun){
				$this->{$functiontoRun}();
			}
		}

		$tail = '';
		$head = 'SELECT ';
		$body = " FROM {$this->_TableName()} ";

		if(!empty($this->_params)){
			if(is_numeric($this->_params) && strpos($this->_params,',') === FALSE) $this->_params = 0 + $this->_params;
			$type = gettype($this->_params);
			$strint = '';
			switch($type){
				case 'integer':
					$tail .= " WHERE ".$this->pk." in ($this->_params)";
				break;
				case 'string':
					if(strpos($this->_params,',')!== FALSE){
						$tail .= " WHERE ".$this->pk." in ({$this->_params})";
					}
				break;
				case 'array':
					if(!empty($this->_params['conditions'])){
						if(is_array($this->_params['conditions'])){
							$NotOnlyInt = FALSE;
							while(!$NotOnlyInt and (list($key, $value) = each($this->_params['conditions']))){
								$NotOnlyInt = (!is_numeric($key))? TRUE: FALSE;
							}
							if(!$NotOnlyInt){
								$tail .= $this->pk." in (".implode(',',$this->_params['conditions']).")";
							}else{
								foreach($this->_params['conditions'] as $field => $value){
									if(is_numeric($field)) $tail .= " AND ".$value;
									else $tail .= " AND $field='$value'";
								}
								$tail = substr($tail, 4);
							}
						}elseif(is_string($this->_params['conditions'])){
							$tail .= $this->_params['conditions'];
						}
						$tail = ' WHERE '.$tail;
					}
					if(!empty($this->_params['join'])){
						$body .= $this->_params['join'];
					}
					if(isset($this->_params['group'])){
						$tail .= " GROUP BY ".$this->_params['group'];
					}
					if(isset($this->_params['sort'])){
						switch (gettype($this->_params['sort'])){
							case 'string':
								$tail .= " ORDER BY ".$this->_params['sort'];
							break;
							case 'array':
								null;
							break;
						}
					}

					if(isset($this->_params['limit'])){
						$tail .= " LIMIT ".$this->_params['limit'];
					}
					if(isset($this->_params[0])){
						switch($this->_params[0]){
						case ':first':
							$tail .= " LIMIT 1";
						break;
						}
					}
				break;
			}
		}
		$fields = (!is_array($this->_params) || (is_array($this->_params) && empty($this->_params['fields'])))? '*' : $this->_params['fields'];
		$sql = $head.$fields.$body.$tail;
		$this->_sqlQuery = $sql;
		if(CAN_USE_MEMCACHED){
			$key = md5($sql);
			$res = null;
			$res = $memcached->get($key);
			if($memcached->getResultCode() == 0 && is_object($res)){
				return $res;
			}
		}
		$this->getData($sql);
		$this->_sqlQuery = $sql;

		if(sizeof($this->after_find)>0){
			foreach($this->after_find as $functiontoRun){
				$this->{$functiontoRun}();
			}
		}
		if(CAN_USE_MEMCACHED){
			$memcached->set($key,$this);
		}
		$obj = clone($this);
		return $obj;
	}

	public function Find_by_SQL($query = NULL){
		if(!$query){
			trigger_error( "The query can not be NULL", E_USER_ERROR );
			exit;
		}else{
			$memcached = $this->checkMemcached();
			$this->_sqlQuery = $query;
			if(CAN_USE_MEMCACHED){
				$key = md5($query);
				$res = null;
				$res = $memcached->get($key);
				if($memcached->getResultCode() == 0 && is_object($res)){
					return $res;
				}
			}
			$this->getData($query);
			return clone($this);
		}
	}

	private function _set_attributes($resultset) {
		$fields = array();
		switch ($this->engine) {
			case 'mysql':
				$result1 = $this->driver->query("SHOW COLUMNS FROM ".$this->_TableName());
			break;
			case 'firebird':
				$result1 = $this->driver->query("SELECT rdb\$field_name FROM rdb\$relation_fields WHERE rdb\$relation_name='".$this->_TableName()."'");
			break;
			case 'sqlite':
			case 'sqlite2':
				$result1 = $this->driver->query("PRAGMA table_info(".$this->_TableName().")");
			break;
		}

		$result1->setFetchMode(PDO::FETCH_ASSOC);
		$resultset1 = $result1->fetchAll();
		foreach ($resultset1 as $res){
			if($this->engine === 'sqlite'){
				$res['Field'] = $res['name'];
				$res['Type'] = $res['type'];
			}
			$type = strtoupper(preg_replace('@\([0-9]+\)@', '', $res['Type']));
			$fields[] = array(
						'Field'=>$res['Field'],
						'Type'=>$type,
						'Value' => null
						);
		}

		foreach($fields as $row) {
			$toCast= false;
			switch($row['Type']) {
				case 'NUMERIC':
				case 'INTEGER':
				case 'INT':
				case 'FLOAT':
				case 'DOUBLE':
					$toCast = true;
				break;
			}

			$value = '';

			$this->_fields[] = $row['Field'];
			$this->_data[$row['Field']] = '';
			$this->_dataAttributes[$row['Field']]['native_type'] = $row['Type'];
			$this->_dataAttributes[$row['Field']]['cast'] = $toCast;

		}
		if(!empty($resultset)) {
			foreach ($resultset[0] as $key => $value) {
				if(!in_array($key, $this->_fields)) {
					$this->_fields[] = $key;
					$this->_data[$key] = '';
					$this->_dataAttributes[$key]['native_type'] = 'VARCHAR';
					$this->_dataAttributes[$key]['cast'] = false;
				}
			}
		}
	}

	public function Niu($contents = NULL){

		foreach($this as $i => $val) {
			$this[$i] = null;
			$this->offsetUnset($i);
		}

		$this->_data = NULL;
		$this->_data = array();
		$this->_attrs = NULL;
		$this->_attrs = array();
		$this->Connect();

		empty($this->_fields) and $this->_set_attributes(array());

		if (!empty($contents)) {
			foreach ($contents as $field => $content) {
				if (in_array($field, $this->_fields)) {
					$this->_data[$field] = $this->_dataAttributes[$field]['cast'] ? 0 + $content : $content;
				}
			}
			foreach ($this->_data as $field => $value) {
				if(empty($value) && $value !== 0) {
					unset($this->{$field});
					unset($this->_data[$field]);
				}
			}
			$this->_counter = 1;
		} else {
			foreach ($this->_fields as $field) {
				$this->_data[$field] = $this->_dataAttributes[$field]['cast'] ? 0 : '';
			}
			$this->_counter = 0;
		}

		return clone($this);
	}

	public function Update($params) {
		if(!is_array($params)){
			throw new Exception('The params for the Update() method must be an array');
		}

		if(empty($params['conditions']) || !is_string($params['conditions'])){
			throw new Exception('The param conditions should not be empty and must be string.');
		}

		if(empty($params['data']) || !is_array($params['data'])) {
			throw new Exception('The param data should not be empty and must be array.');
		}
		defined('AUTO_AUDITS') or define('AUTO_AUDITS',true);
		$this->Connect();
		$prepared = array();
		$query = 'UPDATE `'.$this->_TableName().'` SET ';
		foreach ($params['data'] as $field => $value) {
			$query .= "`$field`=:$field,";
			$prepared[':'.$field] = $value;
		}
		$query = substr($query, 0, -1);
		if(AUTO_AUDITS){
			$query .= ',`updated_at`='.time();
		}

		$query .= ' WHERE '.$params['conditions'];

		$sh = $this->driver->prepare($query);

		if(!$sh->execute($prepared)){
			$e = $this->driver->errorInfo();
			$this->_error->add(array('field' => $this->_TableName(),'message'=>$e[2]."\n $query"));
			return false;
		}

		return true;
	}

	private function _ValidateOnSave($action = 'insert') {
		if(!empty($this->validate)){
			if(!empty($this->validate['email'])){
				foreach($this->validate['email'] as $field){
					$message = 'The email provided is not a valid email address.';
					if (is_array($field)) {
						if (empty($field['field'])) throw new Exception('Field key must be defined in array.');
						empty($field['message']) or ($message = $field['message']);
						$field = $field['field'];
					}

					isset($this->_data[$field]) and empty(preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/",$this->_data[$field]))  and $this->_error->add(array('field' => $field,'message'=>$message));
				}
			}

			if(!empty($this->validate['numeric'])){
				foreach($this->validate['numeric'] as $field){
					$message = 'This Field must be numeric.';
					if (is_array($field)) {
						if (empty($field['field'])) throw new Exception('Field key must be defined in array.');
						empty($field['message']) or ($message = $field['message']);
						$field = $field['field'];
					}
					isset($this->_data[$field]) and (!is_numeric($this->_data[$field])) and $this->_error->add(array('field' => $field,'message'=>$message));
				}
			}

			if(!empty($this->validate['unique'])){
				foreach($this->validate['unique'] as $field){
					$message = 'This field can not be duplicated.';
					if (is_array($field)) {
						if (empty($field['field'])) throw new Exception('Field key must be defined in array.');
						empty($field['message']) or ($message = $field['message']);
						$field = $field['field'];
					}
					if(!empty($this->_data[$field])){
						$obj1 = new $this;
						$resultset = $obj1->Find(array('fields'=>$field, 'conditions'=>"`{$field}`='".$this->_data[$field]."' AND `{$this->pk}`<>'".$this->_data[$this->pk]."'"));
						if($resultset->counter()>0) $this->_error->add(array('field' => $field,'message'=>$message));
					}
				}
			}

			if(!empty($this->validate['presence_of'])){
				foreach($this->validate['presence_of'] as $field){
					$message = 'This field can not be empty or null.';
					if (is_array($field)) {
						if (empty($field['field'])) throw new Exception('Field key must be defined in array.');
						empty($field['message']) or ($message = $field['message']);
						$field = $field['field'];
					}
					empty($this->_data[$field]) and $this->_error->add(array('field'=>$field,'message'=>$message));
				}
			}
		}
	}

	public function Save(){
		defined('AUTO_AUDITS') or define('AUTO_AUDITS',true);
		$this->Connect();
		$className = get_class($this);

		if(sizeof($this->before_save)>0){
			foreach($this->before_save as $functiontoRun){
				$this->{$functiontoRun}();
			}
		}

		if($this->_error->isActived()) return FALSE;

		if(!empty($this->{$this->pk})){
			$kind = 'update';
			$this->_ValidateOnSave($kind);
			if($this->_error->isActived()) return false;

			if($this->engine == 'firebird'){
				$query = "UPDATE ".$this->_TableName()." SET ";
				if(AUTO_AUDITS){
					$this->_data['updated_at'] = time();
				}
				foreach($this->_data as $key => $value){
					if($key != $this->pk &&  $value !== null){
						$query .= "$key = :".$key.",";
					}
				}
				$query = substr($query, 0,-1);
				$query .= " WHERE ".$this->pk." = ".$this->{$this->pk};
			} else {
				$query = "UPDATE `".$this->_TableName()."` SET ";
				if(AUTO_AUDITS){
					$this->_data['updated_at'] = time();
				}
				foreach($this->_data as $key => $value){
					if($key != $this->pk &&  $value !== null){
						$query .= "`$key` = :".$key.",";
					}
				}
				$query = substr($query, 0,-1);
				$query .= " WHERE `".$this->pk."` = ".$this->{$this->pk};
			}
		}else{
			$kind = "insert";

			if(!empty($this->before_insert)){
				foreach($this->before_insert as $functiontoRun){
					$this->{$functiontoRun}();
				}
			}

			if($this->_error->isActived()) return false;

			$this->_ValidateOnSave();
			if($this->_error->isActived()) return false;

			if($this->engine == 'firebird'){
				$query = "INSERT INTO ".$this->_TableName()." ";
				$fields = "";
				$values = "";
				$i=1;
				if(AUTO_AUDITS){
					$this->_data['created_at'] = time();
					$this->_data['updated_at'] = 0;
				}
				foreach($this->_data as $field => $value){
					if(!is_array($value)){
						if($field != $this->pk &&  $value !== null){
							$fields .= "$field, ";
							$values .= ":".$field.", ";
						}
						$i++;
					}
				}
			} else {
				$query = "INSERT INTO `".$this->_TableName()."` ";
				if(isset($this->before_insert[0])){
					foreach($this->before_insert as $functiontoRun){
						$this->{$functiontoRun}();
					}
				}
				$fields = "";
				$values = "";
				$i=1;
				if(AUTO_AUDITS){
					$this->_data['created_at'] = time();
				}
				foreach($this->_data as $field => $value){
					if(!is_array($value) && $field != $this->pk && $value !== null){
						$fields .= "`$field`, ";
						$values .= ":".$field.", ";//'$value'
						$i++;
					}
				}
			}
			$fields = substr($fields, 0, -2);
			$values = substr($values, 0, -2);
			$query .= "($fields) VALUES ($values)";
		}
		$this->_sqlQuery = $query;
		$sh = $this->driver->prepare($query);
		$prepared = array();
		foreach($this->_data as $field => $value){
			if(!is_array($value) && $field != $this->pk && $value !== null){
				$prepared[':'.$field] = $value;
			}
		}
		if(!$sh->execute($prepared)){
		    $e = $this->driver->errorInfo();
		    $this->_error->add(array('field' => $this->_TableName(),'message'=>$e[2]."\n $query"));
		    return FALSE;
		}
		if($kind == "insert"){
			$this->{$this->pk} = $this->driver->lastInsertId() + 0;
			$this[0]->_data[$this->pk] = $this->{$this->pk};
			if(sizeof($this->after_insert)>0){
				foreach($this->after_insert as $functiontoRun){
					$this->{$functiontoRun}();
				}
			}
		}
		if(sizeof($this->after_save)>0){
			foreach($this->after_save as $functiontoRun){
				$this->{$functiontoRun}();
			}
		}
		return true;
	}

	public function Insert(){
		defined('AUTO_AUDITS') or define('AUTO_AUDITS',true);
		$this->Connect();
		$fields = "";
		$values = "";
		if(AUTO_AUDITS){
			$this->created_at = time();
		}
		if($this->engine == 'firebird'){
			$query = "INSERT INTO ".$this->_TableName()." ";
			foreach($this->_data as $field => $value){
				if(!is_array($value)){
					$fields .= "$field,";
					$values .= ":".$field.",";
				}
			}
		} else {
			$query = "INSERT INTO `".$this->_TableName()."` ";
			foreach($this->_data as $field => $value){
				if(!is_array($value)){
					$fields .= "`$field`,";
					$values .= ":".$field.",";
				}
			}
		}
		$fields = substr($fields, 0,-1);
		$values = substr($values, 0,-1);

		$query .= "($fields) VALUES ($values)";
		$this->_sqlQuery = $query;
		$sh = $this->driver->prepare($query);
		$prepared = array();
		foreach($this->_data as $field => &$value){
			if(!is_array($value)){
				$prepared[':'.$field] = $value;
			}
		}

		if (!$sh->execute($prepared)) {
			$e = $sh->errorInfo();
		    $this->_error->add(array('field' => $this->_TableName(),'message'=>$e[2]));
		    return false;
		}

		return true;
	}

	public function Delete($conditions = NULL){
		!empty($this->driver) or $this->Connect();
		if($this->_counter > 1){
			$conditions = array();
			foreach($this as $ele){
				$conditions[] = $ele->{$this->pk};
			}
		}
		if($conditions === NULL and !empty($this->{$this->pk})) $conditions = $this->{$this->pk};
		if($conditions === NULL and empty($this->{$this->pk})){
			$this->_error->add(array('field' => $this->_TableName(),'message'=>"Must specify a register to delete"));
			return FALSE;
		}else{
			$query = "DELETE FROM `".$this->_TableName()."` ";
			if(is_numeric($conditions)){
				$this->{$this->pk} = $conditions;
				$query .= "WHERE ".$this->pk."='$conditions'";
			}elseif(is_array($conditions) && empty($conditions['conditions'])){
				$query .= 'WHERE `'.$this->pk.'` IN ('.implode(',', $conditions).')';
			}elseif(!empty($conditions['conditions'])){
				$query .= 'WHERE '.$conditions['conditions'];
			}
			if(sizeof($this->before_delete) >0){
				foreach($this->before_delete as $functiontoRun){
					$this->{$functiontoRun}();
				}
				if(!empty($this->_error) && $this->_error->isActived()){
					return false;
				}
			}
			$this->_delete_or_nullify_dependents((integer)$conditions) or print($this->_error);
			if(!$this->driver->exec($query)){
			    $e = $this->driver->errorInfo();
			    $this->_error->add(array('field' => $this->_TableName(),'message'=>$e[2]."\n $query"));
			    return FALSE;
			}
			if(sizeof($this->after_delete) >0){
				foreach($this->after_delete as $functiontoRun){
					$this->{$functiontoRun}();
				}
			}
			return TRUE;
		}
	}

	protected function _delete_or_nullify_dependents($id){
		if (!empty($this->dependents) and $id != 0){
			foreach ($this->has_many as $model){
				$s = Singulars($model);
				$m = Camelize($s);
				class_exists($m) or require_once INST_PATH.'app/models/'.strtolower($s).'.php';
				$model1 = new $m();
				$children = $model1->Find(array('conditions'=>Singulars($this->_TableName())."_id='".$id."'"));
				if($children->counter() > 0){
					foreach ($children as $child){
						switch ($this->dependents){
							case 'destroy':
								if(!$child->Delete()){
								    $this->_error->add(array('field' => $this->_TableName(),'message'=>"Cannot delete dependents"));
								    return FALSE;
								}
							break;
							case 'nullify':
								$child->{$this->_TableName().'_id'}='';
								if(!$child->Save()){
								    $this->_error->add(array('field' => $this->_TableName(),'message'=>"Cannot nullify dependents"));
								    return FALSE;
								}
							break;
						}

					}
				}
			}
		}
		return true;
	}

	public function __debugInfo() {
		$this->inspect();
	}

	public function inspect($tabs = 0){
		echo get_class($this)," ActiveRecord (",sizeof($this),")",": ",$this->ListProperties_ToString($tabs);
	}

	protected function ListProperties_ToString($i=0){
		$listProperties = "{\n";
		foreach ($this->_data as $var => $value){
			ob_start();
			var_dump($value);
			$buffer = ob_get_clean();
			for($j=0; $j<$i+1; $j++){
				$listProperties .= "\t";
			}
			$listProperties .= "{$var} => {$buffer}";
		}
		foreach ($this->_attrs as $var => $value){
			ob_start();
			var_dump($value);
			$buffer = ob_get_clean();
			for($j=0; $j<$i+1; $j++){
				$listProperties .= "\t";
			}
			$listProperties .= "{$var}: => {$buffer}";
		}
		for ($m = 0; $m < sizeof($this); $m++){
			$listProperties .= "[{$m}] :\n";


			if(is_object($this[$m]) && get_parent_class($this[$m]) == 'ActiveRecord'){
				ob_start();
				$this[$m]->inspect(1);
				$buffer = ob_get_clean();

				$listProperties .= "\t{$buffer}";
			} else {
				ob_start();
				var_dump($this[$m]);
				$buffer = ob_get_clean();

				$listProperties .= "\t{$buffer}";
			}

		}

		for($j=0; $j<$i; $j++){
			$listProperties .= "\t";
		}
		$listProperties .= "}\n";
		return $listProperties;
	}

	public function __toString(){
		$a = $this->ListProperties_ToString();
		return $a;
	}

	public function getArray(){
		$arraux = array();

		if($this->_counter > 0){
			if($this->_counter === 1) {
				foreach($this->_data as $property => $value){
			        $arraux[0][$property] = (is_object($value) and get_parent_class($value) == 'ActiveRecord')? $value->getArray() : $value;
		        }
				foreach ($this->_attrs as $index => $attribute) {
					if(!empty($arraux[0][$index])) $index .= '_1';
					$arraux[0][$index] = (is_object($attribute) and get_parent_class($attribute) == 'ActiveRecord')? $attribute->getArray() : $attribute;
				}
			} else {
				$n=$m=0;
		        for($t = 0; $t < $this->_counter; $t++){
		        	if(!empty($this[$t]->_data)){
				        foreach($this[$t]->_data as $property => $value){
					        $arraux[$n][$property] = (is_object($value) and get_parent_class($value) == 'ActiveRecord')? $value->getArray():$value;
				        }
				        $n++;
			        }
			        if(!empty($this[$t]->_attrs)){
			        	foreach($this[$t]->_attrs as $property => $value){
			        		$arraux[$m][$property] = (is_object($value) and get_parent_class($value) == 'ActiveRecord')? $value->getArray():$value;
			        	}
			        	$m++;
			        }
		        }
			}
		}
		return $arraux;
	}

	public function Dump(){
		$model = $this->_TableName();
		$dom = new DOMDocument('1.0', 'utf-8');

		$dataDump = $this->getArray();
		$path = INST_PATH.'migrations/dumps/';

		$sroot = $dom->appendChild(new DOMElement('table_'.$model));
		foreach($dataDump as $reg){
			$root = $sroot->appendChild(new DOMElement($model));
			foreach($reg as $element => $value){
				if(preg_match("(&|<|>)", $value)){
					$value = $dom->createCDATASection($value);
					$element = $root->appendChild(new DOMElement($element, ""));
					$element->appendChild($value);
				}else{
					$element = $root->appendChild(new DOMElement($element, $value));
				}
			}
		}

		file_put_contents($path.$model.'.xml', $dom->saveXML());
	}

	public function LoadDump(){
		$doc = new DOMDocument;
		$doc->load(INST_PATH.'migrations/dumps/'.$this->_TableName().'.xml');
		$items = $doc->getElementsByTagName($this->_TableName());
		for($i=0; $i<$items->length; $i++){
			$xitem = $items->item($i);
			$idfield = $xitem->getElementsByTagName($this->pk);
			if($idfield->length > 0){
				$id  = $idfield->item(0)->nodeValue;
				$Obj = Camelize(Singulars($this->_TableName()));
				$Obj = new $Obj();
				$Obj->Niu();
				$arrObj = $Obj->GetFields();
				$Obj->{$this->pk} = $id;
				foreach($arrObj as $key => $value){
					if($key != 'table'){
						$field = $xitem->getElementsByTagName("$key");
						$Obj->{$key} = (is_object($field->item(0)))?addslashes($field->item(0)->nodeValue):'';
					}
				}
				$Obj->Insert() or die($Obj->_error);
			}
		}

	}

	public function WriteSchema($tableName){
		$createFile = FALSE;
		$stringtoINI = '';
		$file = INST_PATH.'migrations/Schema.ini';
		file_exists($file) or file_put_contents($file, '');
		if (!$schema = parse_ini_file($file, TRUE)){
			$createFile = TRUE;
		}
		if($createFile){
			$stringtoINI .= "[$tableName] \n";
			$fp = fopen($file, "w+b");
			fwrite($fp, $stringtoINI);
			fclose($fp);
		}elseif(!in_array($tableName, $schema)){
			$schema[$tableName] = "";
			$stringtoINI = "";
			foreach($schema as $table => $val){
				$stringtoINI .= "[$table] \n";
			}
			$fp = fopen($file, "w+b");
			fwrite($fp, $stringtoINI);
			fclose($fp);
		}
	}

	public function GetFields(){
		$this->Connect();

		switch ($this->engine) {
			case 'mysql':
				$result = $this->driver->query("SHOW COLUMNS FROM `".$this->_TableName()."`") or die(print_r($this->driver->errorInfo(), true));
			break;

			case 'sqlite':
				$result = $this->driver->query("PRAGMA table_info(".$this->_TableName().")") or die(print_r($this->driver->errorInfo(), true));
			break;
		}

		$arraux = array();
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$resultset = $result->fetchAll();
		foreach($resultset as $row){
			if($this->engine === 'sqlite'){
				$row['Field'] = $row['name'];
				$row['Type'] = $row['type'];
			}

			$arraux[$row['Field']] = $row['Type'];
		}

		return $arraux;
	}

	public function getError(){
		return $this->_errors;
	}

	public function counter(){
		return (integer)$this->_counter;
	}

	public function first(){
		return $this->_counter > 0 ? $this[0] : FALSE;
	}

	public function last(){
		return $this->_counter > 0 ? $this[$this->counter() - 1] : FALSE;
	}

	public function _TableName($name = null){
		if(!empty($name)){
			$this->_ObjTable = $name;
		}elseif(empty($this->_ObjTable) or strlen($this->_ObjTable) < 1){
			$className =  unCamelize(get_class($this));
			$words = explode("_", $className);
			$i = sizeof($words) - 1;
			$words[$i] = Plurals($words[$i]);
			$this->_ObjTable = implode("_", $words);
		}
		return $this->_ObjTable;
	}

	public function _sqlQuery(){
	    return $this->_sqlQuery;
	}

	public function _nativeType($field){
		if (empty($this->_dataAttributes[$field]['native_type'])) {
			return false;
		}
		return $this->_dataAttributes[$field]['native_type'];
	}

	public function slice($start = null, $length = null){
		if(empty($length)) $length = $this->_counter;
		if($start === null) $start = 0;
		$end = $start + $length;
		if ($end > $this->_counter) $end = $this->_counter;
		$name = get_class($this);
		$arr = new $name();
		for($i=$start; $i<$end; $i++){
			$arr[] = $this[$i];
		}
		return $arr;
	}

	public function _unset($index = 0) {
		if($this->_counter === 1) {
			$this->_data = null;
			$this->_attrs = null;
		} elseif($this->offsetExists($index)) {
			$this[$index]->_data = null;
			$this[$index]->_attrs = null;
			$this->offsetUnSet($index);
		}
		$this->_counter--;
		if($this->_counter < 0) $this->_counter = 0;
	}

	public function toJSON() {
		return json_encode($this->getArray());
	}

	public function Paginate($params = NULL){

		if(is_array($params) && sizeof($params) === 1 && !empty($params[0])) $params = $params[0];
		$arr_params = array();
		$arr_2 = array();
		$per_page = (isset($params['per_page']))?$params['per_page']:10;
		if(!empty($params['varPageName'])) {
			$this->PaginatePageVarName = $params['varPageName'];
		}
		if(!empty($params['page'])) {
			$this->PaginatePageNumber = $params['page'];
		}
		$start = ($this->PaginatePageNumber-1)*$per_page;
		if(isset($params['conditions'])) $arr_2['conditions'] = $arr_params['conditions'] = $params['conditions'];
		if(isset($params['join'])) $arr_2['join'] = $arr_params['join'] = $params['join'];
		if(isset($params['fields'])) $arr_params['fields'] = $params['fields'];
		if(isset($params['group'])) $arr_2['group'] = $arr_params['group'] = $params['group'];
		if(isset($params['sort'])) $arr_2['sort'] = $arr_params['sort'] = $params['sort'];
		$arr_params['limit'] = $start.",".$per_page;
		$arr_2['fields'] = "COUNT({$this->_TableName()}.{$this->pk}) AS PaginateTotalRegs";
		$this->PaginateTotalItems = $this->Find($arr_2)->PaginateTotalRegs;
		$this->PaginateTotalPages = ceil($this->PaginateTotalItems/$per_page);

		return $this->Find($arr_params);
	}

	public function WillPaginate($params = NULL){
		if(is_array($params) && sizeof($params) === 1 && !empty($params[0])) $params = $params[0];
		$str = '';
		$tail = '';
		$i = 1;
		if($this->PaginatePageNumber > 1):
			$str .= '<a class="paginate paginate-first-page" href="?'.$this->PaginatePageVarName.'=1">|&lt;&lt;</a>&nbsp;';
			$str .= '<a class="paginate paginate-prev-page" href="?'.$this->PaginatePageVarName.'='.($this->PaginatePageNumber-1).'">&lt;</a>&nbsp;';
		endif;
		$top = $this->PaginateTotalPages;
		if($this->PaginateTotalPages > 10):
			$top = ($this->PaginatePageNumber-1)+10;
			if($top > $this->PaginateTotalPages) $top = $this->PaginateTotalPages;
			$i = $top-10;
			if($i < 1) $i = 1;
		endif;
		if($this->PaginatePageNumber < $this->PaginateTotalPages):
			$tail .= '<a class="paginate paginate-next-page" href="?'.$this->PaginatePageVarName.'='.($this->PaginatePageNumber+1).'">&gt;</a>&nbsp;';
			$tail .= '<a class="paginate paginate-last-page" href="?'.$this->PaginatePageVarName.'='.($this->PaginateTotalPages).'">&gt;&gt;|</a>&nbsp;';
		endif;
		for(; $i <= $top; $i++){
			$str .= '<a class="paginate paginate-page'.($this->PaginatePageNumber == $i ? " paginate-active-page" : "").'" href="?'.$this->PaginatePageVarName.'='.$i.'">'.$i.'</a>&nbsp;';
		}
		$str .= $tail;
		return $str;
	}

	public function input_for($params){
		$stringi = '<input';
		$stringt = '<textarea';
		$strings = '<select';
		$name = '';
		$html = '';
		$type = '';
		$input = '';
		if(!empty($params) or !empty($params[0]) or isset($params['field'])) {
			if(is_array($params)){
				$field = isset($params['field'])?$params['field']:$params[0];
			} else {
				$field = $params;
			}
			if(empty($params['name']) or !is_array($params)){
				$name = Singulars(strtolower($this->_TableName())).'['.$field.']';
			} else {
				$name = $params['name'];
			}
			if(is_array($params) and !empty($params['html']) and is_array($params['html'])) {
				foreach($params['html'] as $element => $value){
					$html .= $element.'="'.$value.'" ';
				}
			}
			$html = trim($html);
			if(strlen($html)>0):
				$html = ' '.$html;
			endif;
			if(is_array($params) and !empty($params['type']) and is_string($params['type'])){
				$type = $params['type'];
			} else {
				$nattype = $this->_nativeType($field);
				switch($nattype) {
					case 'INTEGER':
					case 'LONG':
					case 'STRING':
					case 'INT':
					case 'BIGINT':
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
				}
			}
			if($field === 'id') $type = 'hidden';
			switch ($type) {
				case 'text':
				case 'hidden':
					$input = $stringi.' type="'.$type.'" name="'.$name.'"'.$html.' value="'.$this->_data[$field].'" />';
				break;
				case 'textarea':
					$input = $stringt.' type="'.$type.'" name="'.$name.'"'.$html.'>'.$this->_data[$field].'</textarea>';
				break;
				case 'select':
					$cont = !empty($params['first']) ? '<option value="">'.$params['first'].'</option>' : '';

					foreach($params['list'] as $value => $option):
						$default = '';
						if($this->_data[$field] == $value) $default = 'selected="selected"';
						$cont .= '<option value="'.$value.'"'.$default.'>'.$option.'</option>'.PHP_EOL;
					endforeach;
					$input = $strings.' name="'.$name.'"'.$html.'>'.$cont.'</select>';
				break;
			}
		} else{
			throw new Exception("Must to give the field to input.");
			return null;
		}

		return $input;
	}

	public function form_for($params){
		$string = '<form';
		$method = 'post';
		$action = '#';
		$name = '';
		$id='';
		$html = '';

		$name = singulars(strtolower($this->_TableName()));
		$action = !empty($params['action'])? $params['action'] : INST_URI.strtolower($this->_TableName());
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
}

abstract class Page extends Core_General_Class {
	public $excepts_before_filter = array();
	protected $layout = "";
	protected $render = NULL;
	protected $flash = "";
	protected $yield = "";
	protected $params = array();
	protected $metaDescription = '';
	protected $pageTitle = '';
	protected $controller = '';
	protected $action = '';
	protected $htmlcontent= '';
	protected $excepts_after_filter = array();
	protected $excepts_after_render = array();
	protected $excepts_before_render = array();
	protected $outputHtml = true;
	protected $_data_ = array();
	private $_respondToAJAX = '';
	private $_canrespondtoajax = false;
	private $models = array();

	public function __get($var){
		$model = unCamelize($var);
		if(file_exists(INST_PATH.'app/models/'.$model.'.php')) {
			if(!class_exists($var)){
				require INST_PATH.'app/models/'.$model.'.php';
			}
			$this->{$var} = new $var();
			return $this->{$var};
		}
	}

	public function display(){
		$renderPage = TRUE;
		$this->action = _ACTION;
		$this->controller = _CONTROLLER;
		if(property_exists($this, 'noTemplate') and in_array(_ACTION, $this->noTemplate)) $renderPage = FALSE;
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
		if(isset($this->render) and is_array($this->render)){
			if (isset($this->render['action']) && $this->render['action'] === false) {
				$this->yield = '';
				$renderPage = FALSE;
			}elseif(!empty($this->render['file'])){
				$view = $this->render['file'];
			}elseif(!empty($this->render['partial'])){
				$view = _CONTROLLER.'/_'.$this->render['partial'].'.phtml';
			}elseif(!empty($this->render['text'])){
				$this->yield = $this->render['text'];
				$renderPage = FALSE;
			}elseif(!empty($this->render['action'])){
				$view = _CONTROLLER.'/'.$this->render['action'].'.phtml';
			}else{
				$view = _CONTROLLER.'/'._ACTION.'.phtml';
			}
		}else{
			$view = _CONTROLLER.'/'._ACTION.'.phtml';
		}

		if($renderPage):
			ob_start();
			include_once(INST_PATH."app/templates/".$view);
			$this->yield = ob_get_clean();
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
			$this->htmlcontent = $this->yield;
		endif;
		if($this->outputHtml){
			echo $this->htmlcontent;
		}
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

class NewAr extends ActiveRecord{}

abstract class Migrations extends Core_General_Class {

	public function __construct(){}

	public function __destruct(){}

	public function up(){
		echo 'Nothing to do.';
	}

	public function down(){
		echo 'Nothing to do.';
	}

	public function alter(){
		echo 'Nothing to do.';
	}

	public function Reset(){
		$this->down();
		$this->up();

	}

	public function Run() {
		$this->up();
		$this->alter();
	}


	protected function Create_Table($table = NULL){
		defined('AUTO_AUDITS') or define('AUTO_AUDITS', true);
		if($table !== NULL){

			$tablName = $table['Table'];
			$query = "CREATE TABLE IF NOT EXISTS `".$tablName."` (";
			$query .= "`id` INT PRIMARY KEY ,";
			foreach($table as $key => $Field){
				if(strcmp($key, 'Table') != 0){
					if($Field['type'] == 'VARCHAR' and empty($Field['limit'])) $Field['limit'] = 250;
					$query .= (!empty($Field['field']) and !empty($Field['type']))? "`".$Field['field']."` ".$Field['type'] : NULL;
					$query .= (!empty($Field['limit']))? " (".$Field['limit'].")" : NULL;
					$query .= (!empty($Field['null']))? " NOT NULL" : NULL;
					$query .= (!empty($Field['default']))? " DEFAULT '".$Field['default']."'" : NULL;
					$query .= (!empty($Field['comments']))? " COMMENT '".$Field['comment']."'" : NULL;
					$query .= " ,";
				}
			}
			if(AUTO_AUDITS){
				$query .= "`created_at` INT NOT NULL ,";
				$query .= "`updated_at` INT NOT NULL ,";
			}
			$query = substr($query, 0, -2);
			$query .= ");";
			echo 'Running query: ', $query, PHP_EOL;
			$Ar = new NewAr();
			$Ar->Connect();
			if($Ar->driver->exec($query) === false) print_r($Ar->driver->errorInfo());
			$Ar->WriteSchema($tablName);
			if ($Ar->driver->settings['database']['driver'] == 'mysql') {
				$query = "ALTER TABLE `$tablName` MODIFY COLUMN `id` INT AUTO_INCREMENT";
				echo 'Running query: ', $query, PHP_EOL;
				if($Ar->driver->exec($query) === false) print_r($Ar->driver->errorInfo());
			}
		}
	}

	protected function Drop_Table($table){
		$query = "DROP TABLE IF EXISTS `".$table."`";
		echo 'Running query: ', $query, PHP_EOL;
		$Ar = new NewAr();
		$Ar->Connect();
		if($Ar->driver->exec($query) === false) print_r($Ar->driver->errorInfo());
	}

	protected function Add_Column($columns = NULL){
		if(is_array($columns) && !empty($columns)){
			if($columns['type'] == 'VARCHAR' and empty($columns['limit'])) $columns['limit'] = '255';
			$query = "ALTER TABLE `".$columns['Table']."` ADD COLUMN `".$columns['field']."` ".strtoupper($columns['type']);
			$query .= (isset($columns['limit']) and $columns['limit'] != '')? "(".$columns['limit'].")" : NULL;
			$query .= (isset($columns['null']) and $columns['null'] != '')? " NOT NULL" : NULL;
			$query .= (isset($columns['default']) and $columns['default'] != '')? " DEFAULT '".$columns['default']."'" : NULL;
			$query .= (!empty($columns['comments']))? " COMMENT '".$columns['comment']."'" : NULL;
			echo 'Running query: ', $query, PHP_EOL;
			$Ar = new NewAr();
			$Ar->Connect();
			if($Ar->driver->exec($query) === false) print_r($Ar->driver->errorInfo());
		}else{
			throw new Exception('Cannot add a column with '.gettype($columns).'.');
		}
	}

	protected function Remove_Column($column=NULL){
		$query = "ALTER TABLE `".$column[0]."` DROP `".$column[1]."`";
		echo 'Running query: ', $query, PHP_EOL;
		$Ar = new NewAr();
		$Ar->Connect();
		if($Ar->driver->exec($query) === false) print_r($Ar->driver->errorInfo());
	}
}

class index {
	public function __construct(){
		if(isset($_GET['url'])){
			$request = explode("/", $_GET['url']);
			unset($_GET['url']);
		}
		$path=INST_PATH.'app/controllers/';

		if(!isset($request[0]) or strcmp($request[0], "") === 0) $request[0] = DEF_CONTROLLER;

		if(!isset($request[1]) or strcmp($request[1], "") === 0) $request[1] = DEF_ACTION;

		$controllerFile=$request[0]."_controller.php";
		$controller = array_shift($request);
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

		$canGo = true;

		if(!file_exists($path.$controllerFile) && defined('USE_ALTER_URL') && USE_ALTER_URL){
			$params['alter_controller'] = $controller;
			$params['alter_action'] = $action;
			$parts = explode('/',ALTER_URL_CONTROLLER_ACTION);
			$controller = $parts[0];
			$action = $parts[1];
			$controllerFile = $controller.'_controller.php';
		}elseif(!file_exists($path.$controllerFile)){
			$canGo = false;
			echo "Missing Controller";
		}

		define('_CONTROLLER', $controller);
		define('_ACTION', $action);
		define('_FULL_URL', INST_URI._CONTROLLER.'/'._ACTION.'/?'.http_build_query($params));

		if($canGo) {
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
		}
	}
}
?>