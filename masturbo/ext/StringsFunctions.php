<?php
/*
* Extensi�n de Cadenas
*
* Este archivo contiene funciones para el manejo de cadenas.
* La funciones estan desarrolladas para cadenas en ingles.
* @version 3.0
* @author Javier Serrano <rantes.javier@gmail.com
* @package Core
* @subpackage extensions
*/

//carga de los arreglos de los plurales irregulares
include(dirname(__FILE__).'/StringFunctionsLibrary/IrregularNouns.php');

	/**
	 * Metodo publico Plurals()
	 *
	 * Este metodo se encarga de cambiar a plural la cadena entregada.
	 * @param string $string La cadena para cambiar a plural.
	 */
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
	/**
	 * Metodo publico Singulars()
	 *
	 * Este metodo se encarga de cambiar a singular la cadena entregada.
	 * @param string $string La cadena para cambiar a singular.
	 */
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
	/**
	 * Metodo publico Camelize()
	 *
	 * Este metodo se encarga de cambiar a CamelCase la cadena entregada.
	 * @param string $string La cadena para cambiar a camelcase.
	 */
	function Camelize($params, &$obj=NULL){
		if($obj === NULL) $string = $params;
		else $string = $params[0];
		$newName = "";
		if(preg_match("[_]", $string)):
			$names = preg_split("[_]", $string);
			$i=1;
			foreach($names as $single){
				//if($i == sizeof($names)) $single = Singulars($single);
				$newName .= ucfirst($single);
				$i++;
			}
		else:
			$newName .= ucfirst($string);
		endif;
		return $newName;
	}
	/**
	 * Metodo publico ValidStrings()
	 *
	 * Este metodo se encarga de cambiar a caracteres aceptables en la base de datos, los caracteres especiales.
	 * @param string $string La cadena para validar.
	 */
//	function ValidStrings($string){
//		$var = htmlentities($string[0],ENT_QUOTES,"UTF-8");
//		return $var;
//	}
	/**
	 * ToList($array)
	 *
	 * Este metodo se encarga de convertir un arreglo en una lista separada por comas.
	 * @return string $list Listado separado por comas.
	 */
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
	 /**
	 * Metodo publico unCamelize()
	 *
	 * Este metodo se encarga de revertir una camelizaci�n y devuelve un string en su estado natural de palabras min�sculas
	 * y separadas por underscore.
	 * @return string $natural Cadena devuelta a su nombre natural en singular.
	 * @param string $string Cadena a convertir
	 */
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

	 /**
	 * Metodo cleanToSEO()
	 *
	 * Cambia caracteres especiales con su equivalente a ascii normal, espacios a raya y los no equivalentes, los elimina.
	 * Se idealiza para el uso de links descriptivos para SEO.
	 * @return string $string Cadena convertida.
	 * @param string $string Cadena a convertir.
	 */
	 function cleanToSEO($params, &$obj = NULL){
		if($obj === NULL) $string = $params;
		else $string = $params[0];

//		$specialChars = array('Ä','Á','À','Â','Ã','Å','Æ','ä','á','à','â','ã','å','æ','Ö','Ó','Ò','Ô','Õ','Ø','Ő','ö','ó','ò','ô','õ','ø','Ë','É','È','Ê','ë','é','è','ê','Ç','ç','Ï','Í','Ì','Î','ï','í','ì','î','Ü','Ú','Ù','Û','ü','ú','ù','û','Ÿ','Ý','Ỳ','Ŷ','ÿ','ý','ỳ','ŷ','Ñ','ñ');
//		$normalChars =  array('A','A','A','A','A','A','A','a','a','a','a','a','a','a','O','O','O','O','O','O','O','o','o','o','o','o','o','E','E','E','E','e','e','e','e','C','c','I','I','I','I','i','i','i','i','U','U','U','U','u','u','u','u','Y','Y','Y','Y','y','y','y','y','N','n');
// 		$specialChars = array('Ä','Á','À','Â','Ã','Å','Æ','Ą','ä','á','à','â','ã','å','æ','Ę','ę','Ė','ė','Ö','Ó','Ò','Ô','Õ','Ø','Ǫ','Ō','Ő','ö','ǫ','ó','ò','ô','õ','ø','ō','Ë','É','È','Ê','Ē','ē','ë','é','è','ê','Ç','ç','Č','č','Ï','Í','Ì','Î','Ī','ï','í','ì','î','ī','Ü','Ú','Ù','Û','Ū','Ů','ů','ü','ú','ù','û','ū','Ÿ','Ý','Ỳ','Ŷ','Ȳ','ÿ','ý','ỳ','ŷ','ȳ','Ñ','ñ','Ž','ž','Ż','ż','Š','š','Ş','ş','Ð','ð','Œ','œ','Þ','þ','ŵ','ẅ','Ł','ł','Č','č','ß','Ğ','ğ');
//         $normalChars =  array('A','A','A','A','A','A','A','A','a','a','a','a','a','a','a','E','e','E','e','O','O','O','O','O','O','O','O','O','o','o','o','o','o','o','o','o','E','E','E','E','E','e','e','e','e','e','C','c','C','c','I','I','I','I','I','i','i','i','i','i','U','U','U','U','U','U','u','u','u','u','u','u','Y','Y','Y','Y','Y','y','y','y','y','y','N','n','Z','z','Z','z','S','S','S','s','D','d','OE','oe','P','p','w','w','L','l','C','c','ss','G','g');
// 		$charstrans = array('Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
//     'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
//     'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
//     'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
//     'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
//     'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
//     'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f');
    	$specialChars = array("\xc0","\xc1","\xc2","\xc3","\xc4","\xc5","\xc6","\xc7","\xc8","\xc9","\xca","\xcb","\xcc","\xcd","\xce","\xcf","\xd0","\xd1",
    			"\xd2","\xd3","\xd4","\xd5","\xd6","\xd7","\xd8","\xd9","\xda","\xdb","\xdc","\xdd","\xde","\xdf","\xe0","\xe1","\xe2","\xe3","\xe4","\xe5","\xe6",
    			"\xe7","\xe8","\xe9","\xea","\xeb","\xec","\xed","\xee","\xef","\xf0","\xf1","\xf2","\xf3","\xf4","\xf5","\xf6","\xf7","\xf8","\xf9","\xfa","\xfb","\xfc","\xfd","\xfe","\xff");
    	$normalChars =  array('A','A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','D','N',
    			'O','O','O','O','O','O','O','U','U','U','U','Y','B','Ss','a','a','a','a','a','a','a',
    			'c','e','e','e','e','i','i','i','i','o','n','o','o','o','o','o','o','o','u','u','u','u','y','b','y');
		$string = str_replace($specialChars, $normalChars, utf8_decode($string));
		$string = strtolower($string);
		$string = preg_replace('/[\s]+/', '-', $string);
		$string = preg_replace('/[^a-zA-Z0-9-]/', '', $string);

		return $string;
	 }
?>
