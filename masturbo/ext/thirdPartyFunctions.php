<?php
/**
 * Third Party functions.
 *
 * Funciones que integran apis o servicios de terceros.
 *
 * @author Javier Serrano
 * @package Core
 * @subpackage Extensions
 * @Version 1.0 March 20 2013
 */
/**
 * Ubica lat y lng a partir de una direccion
 * @param array $params [address, city, state, country]
 * @param object $obj
 * @return boolean|object
 */
function geoLocator($params = array('address'=>'','city'=>'', 'state'=>'', 'country'=>''), &$obj = null){
	if($obj !== null):
		if(!empty($params[0])) $params = $params[0];
		if(!empty($obj->address) and empty($params['address'])):
			$params['address'] = $obj->address;
		endif;
		if(!empty($obj->city) and empty($params['city'])):
			$params['city'] = $obj->city;
		endif;
		if(!empty($obj->state) and empty($params['state'])):
			$params['state'] = $obj->state;
		endif;
		if(!empty($obj->country) and empty($params['country'])):
			$params['country'] = $obj->country;
		endif;
	endif;
	$mapsUrl = 'http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address='.urlencode($params['address'].', '.$params['city'].', '.$params['state'].', '.$params['country']);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $mapsUrl);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$res = curl_exec($ch);
	curl_close($ch);
	$obj1 = json_decode($res);
	if($obj1->status == 'OK'):
		$result = $obj1->results[0];
		if($obj !== null):
			$obj->lat = $result->geometry->location->lat;
			$obj->lng = $result->geometry->location->lng;
			return true;
		else:
			return $result->geometry->location;
		endif;
	endif;
	
	return false;
}
?>