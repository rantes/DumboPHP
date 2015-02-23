<?php
/**
 * Mailing functions.
 *
 * Funciones para la gestion de correos electronicos.
 *
 * @author Javier Serrano
 * @package Core
 * @subpackage Extensions
 * @Version 3.0 November 18 2009
 */
if(!class_exists('Mail')):// and file_exists(PEAR_EXTENSION_DIR."/Mail.php")):
	require_once INST_PATH."vendor/Mail/Mail.php";
endif;
/**
 *
 * Envia emails con templates. Incluye encabezados completos para evitar que sea tomado como spam.
 * Utiliza PEAR::Mail para la conexion smtp
 * @param mixed $arr Arreglo de parametros
 * @param object $obj El objeto, en caso de invocarse en la instancia de un objeto
 * @return boolean
 */
	function email($arr, &$obj=NULL){
		if($obj == null or sizeof($arr)>0){

			$headers = array();
			$sent = false;

			$boundary = 'DumboPHP'.md5('DumboPHP'.time());
			$who = isset($arr['from'])? $arr['from'] : _CONTACT_MAIL;
			$headers = array (
				'From' => $who,
				'To' => $arr['to'],
				'Subject' => $arr['subject'],
				"X-Mailer" => "+Turbo",
				"X-Priority" => "3",
				"Reply-To" => $who,
				"Return-Path" => $who,
				"MIME-Version" => "1.0",
				"Content-type" => "multipart/alternative;\n boundary=\"".$boundary."\""
			);
			if(!empty($arr['bcc'])){
				$headers['Bcc'] = $arr['bcc'];
			}
			if(!isset($arr['body'])) return false;
			$body = '';
			if(isset($arr['template'])){
				$content = $arr['body'];
				ob_start();
				include $arr['template'];
				$body = ob_get_clean();
			} else {
				$body = $arr['body'];
			}
			$plain = "--".$boundary."\n";
			$plain .= "Content-Type: text/plain; charset=utf-8\n";
			$plain .= "Content-Transfer-Encoding: 7bit\n";
			$plain .= strip_tags($body);

			$html = "--".$boundary."\n";
			$html .= "Content-Type: text/html; charset=utf-8\n";
			$html .= "Content-Transfer-Encoding: 7bit\n";
			$html .= "\r\n".$body."\r\n";
			$html .= "--".$boundary."--";

			 $host = !empty($arr['smtp_host'])? $arr['smtp_host']:_CONTACT_SMTP_HOST;
			 $username = !empty($arr['smtp_user'])? $arr['smtp_user']:_CONTACT_SMTP_USER;
			 $password = !empty($arr['smtp_password'])? $arr['smtp_password']:_CONTACT_SMTP_PASSWORD;
			 $port = !empty($arr['smtp_port'])? $arr['smtp_port'] : _CONTACT_SMTP_PORT;

			$option = 'html';
			if(!empty($arr['opt_content'])){
				$option = $arr['opt_content'];
			}
			switch ($option){
				case 'plain':
					$content = $plain;
				break;
				case 'both':
					$content = $plain.$html;
				case 'html':
				default:
					$content = $html;
				break;
			}
			if(!defined('USE_SMTP')){
				define('USE_SMTP', false);
			}
			$Mail = new Mail();
			if(USE_SMTP){
			 	$mailo = $Mail->factory('smtp',
			  		array ('host' => $host,
				     'auth' => true,
				     'username' => $username,
				     'password' => $password,
				   	 'port'=>$port));

			} else {
				$mailo = $Mail->factory('mail', '-f rantes.javier@gmail.com');
			}
			 	$mail = $mailo->send($arr['to'], $headers, $content);
			 	
			 	$pear = new PEAR();
			 	
				if ($pear->isError($mail)){
				   $sent = false;
				   file_put_contents(INST_PATH.'/erroresemail.txt', date('Y m d H:i:s').'[error]:'.$arr['to'].' not sent'.$mail->getMessage()."\n", FILE_APPEND);
				} else {
				   $sent = true;
				}

			return $sent;

		}
		return false;
	}
?>