<?php

/**
 * Clase WebPage.
 *
 * @author Javier Serrano <rantes.javier@gmail.com>
 * @version 1.0
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Javier Serrano <http://www.rantes.info/>
 * @package Core
 * @extends ActiveRecord
 */
/**
 * Clase WebPage.
 *
 * Implementa los objetos de tipo pagina web y que a su vez, debe ser active record
 * debido a aque tambien debe guardar los datos a una base de datos
 *
 * @author Javier Serrano <rantes.javier@gmail.com>
 * @version 1.0
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Javier Serrano <http://www.rantes.info/>
 * @package Core
 * @extends ActiveRecord
 * @access abstract
 */
// abstract class WebPage extends ActiveRecord{
// 	private $WEBDOMDocumentPage = NULL;
// 	public function page(){
// 		if($this->WEBDOMDocumentPage === NULL):
// 			$this->WEBDOMDocumentPage = new DOMDocument();
// 			$decod = (sizeof(preg_split("/iso-8859-1/i",$output))>1 or (sizeof(preg_split("/utf-8/i",$output))<1))? 'ISO-8859-1' : 'UTF-8';
// 			@$this->WEBDOMDocumentPage->loadHTML(mb_convert_encoding($output, 'HTML-ENTITIES', $decod));
// 		endif;
// 		return $this->WEBDOMDocumentPage;
// 	}
// 	public function browse(){
// 		$ch = curl_init($this->url);
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
// 		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

// 		$this->fullContent = html_entity_decode(curl_exec($ch));
// 		$info = curl_getinfo($ch);

// 		if(curl_errno($ch)!==0):
// 			$this->info = 'error:' . curl_error($ch).", Codigo: ".$info['http_code']."<br />";
// 		else:
// 			$this->info = 'Tardó ' . $info['total_time'] . ' segundos en enviar la petición a ' . $info['url'].' con c&oacute;digo de respuesta: '.$info['http_code'].".<br />";
// 			$this->indexable = 1;
// 		endif;
// 		curl_close($ch);
// 		$this->browsed_at = time();
// 	}
// }
?>