<?php
//require "Mail.php";
if(!class_exists('Mail') and file_exists(PEAR_EXTENSION_DIR."/Mail.php")):
	require_once "Mail.php";
endif;

/**
 *
 * Envia emails con templates. Incluye encabezados completos para evitar que sea tomado como spam.
 * Utiliza PEAR::Mail para la conexion smtp
 * @param mixed $params Arreglo de parametros
 * @param object $obj El objeto, en caso de invocarse en la instancia de un objeto
 * @return boolean
 */
	function email($arr, &$obj=NULL){
		if($obj == null or sizeof($arr)>0):

			$headers = array();

			$boundary = 'masTurbo'.md5('+Turbo'.time());
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

			if(!isset($arr['body'])) return false;
			$body = '';
			if(isset($arr['template'])):
				$content = $arr['body'];
				ob_start();
				include $arr['template'];
				$body = ob_get_clean();
			else:
				$body = $arr['body'];
			endif;
//			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/erroresemail.txt', $body);
			$plain = "--".$boundary."\n";
			$plain .= "Content-Type: text/plain; charset=utf-8\n";
			$plain .= "Content-Transfer-Encoding: 7bit\n";
			$plain .= strip_tags($body);

			$html = "--".$boundary."\n";
			$html .= "Content-Type: text/html; charset=utf-8\n";
			$html .= "Content-Transfer-Encoding: 7bit\n";
			$html .= "\r\n".$body."\r\n";
			$html .= "--".$boundary."--";
//			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/erroresemail.txt', $plain.$html);
			//$sent = mail($arr['to'], $arr['subject'], $plain.$html, $header);

			 $host = isset($arr['smtp_host'])? $arr['smtp_host']:_CONTACT_SMTP_HOST;
			 $username = isset($arr['smtp_user'])? $arr['smtp_user']:_CONTACT_SMTP_USER;
			 $password = isset($arr['smtp_password'])? $arr['smtp_password']:_CONTACT_SMTP_PASSWORD;

//			 file_put_contents($_SERVER['DOCUMENT_ROOT'].'/erroresemail.txt', 'before smpt');

			 $smtp = Mail::factory('smtp',
			   array ('host' => $host,
			     'auth' => true,
			     'username' => $username,
			     'password' => $password,
			   	 'port'=>26));

//			 file_put_contents($_SERVER['DOCUMENT_ROOT'].'/erroresemail.txt', 'before mail');
			 $mail = $smtp->send($arr['to'], $headers, $plain.$html);
//			 file_put_contents($_SERVER['DOCUMENT_ROOT'].'/erroresemail.txt', 'after mail');

			 if (PEAR::isError($mail)):
			   $sent = false;
//			   echo $mail->getMessage();
			   file_put_contents($_SERVER['DOCUMENT_ROOT'].'/erroresemail.txt', $arr['to'].' not sent'.$mail->getMessage());
			 else:
			   $sent = true;
//			   file_put_contents($_SERVER['DOCUMENT_ROOT'].'/erroresemail.txt', $arr['to'].' sent');
			 endif;


			return $sent;

		endif;
		return false;
	}
?>