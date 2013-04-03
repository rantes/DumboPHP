<?php

//include('paperclip/class.upload.php');
//class PaperClip{
//
//	public function __construct($params = array()){
//
//	}
//	public function __set($name, $value){
//		$this->_data[$name] = $value;
//	}
//	public function __get($name){
//		if (array_key_exists($name, $this->data)):
//            return $this->data[$name];
//        endif;
//		return NULL;
//	}
//	public function url($style = NULL){
//
//	}
//	public function path(){
//		
//	}
//	
//	public function __tostring(){
//		return "PaperClip Object";
//	}
//}
//
//function PaperClip($arg = NULL, &$obj){
//
//	//Defintion of different paths, for uri and localize file.
//	$path = INST_PATH."app/webroot/images/attachment/";
//	$url = INST_URI;
//	$arg = $arg[0];
//
//	//Must to pass args to do it.
//	if($arg !== NULL):
////		if(isset($arg['path']) and strlen($arg['path']) > 0) $path = $arg['path'];
////		if(isset($arg['url']) and strlen($arg['url']) > 0) $url = $arg['url'];
////		
////		$Paper = new PaperClip();
////		$Paper->_path = $path;
////		$Paper->_url = $url;
////		
////		$ClassName = strtolower($obj->unCamelize(get_class($obj)));
////
////		if(isset($_FILES[$ClassName]) or isset($_FILES[$arg[0]])):
////
////			if(is_array($_FILES[$ClassName]['name']) and isset($_FILES[$ClassName]['name'][$arg[0]])):
////				$arr = array();
////				foreach($_FILES[$ClassName] as $key => $element){
////					$arr[$arg[0]][$key] = $_FILES[$ClassName][$key][$arg[0]];
////				}
////			elseif(isset($_FILES[$arg[0]])):
////				$arr[$arg[0]] = $_FILES[$arg[0]];
////			endif;
////			$handle = new Upload($arr[$arg[0]]);
////
////			if ($handle->uploaded):
////				$path .= $obj->id."/";
////				$handle->Process($path);
//////		
//////				// we check if everything went OK
////				if ($handle->processed):
////					echo "yes!!";
////					unset($_FILES);
////				endif;
////			endif;
////		endif;
////		$obj->{$arg[0]} = $Paper;
//	else:
//		throw new Exception('Attribute not defined to find attached files');
//	endif;
//
//
//}
//
//function validates_attachment_presence(&$fields = NULL){
//	
//}
//
//function validates_attachment_size(&$fields, &$options = NULL){
//	
//}
//
//function validates_attachment_content_type(){
//	
//}

?>