<?
/**
 *
 * Clase ConsoleController.
 *
 * Clase que administra las migraciones.
 *
 * Se encarga de crear los archivos de migraciones y la administracion de los datos.
 * @author Javier Serrano
 * @package Core
 * @subpackage Migrations
 * @Version 1.0 November 18 2009
 */
require_once("Migrations.php");
class ConsoleController extends Page{
	public $layout = 'layout';
	public $noTemplate = array("create_migrations", "dump_model", "migrations");
	public $hasPDO = true;
	public $hasPEARMail = true;

	function __construct(){
		if (!extension_loaded ('PDO')):
			$this->hasPDO = false;
			die('No tienes instalada la extension PDO, es necesaria para continuar y de una vez, la extension del motor DB que vas a usar.');
		endif;
		if(!(include 'Mail.php')):
			$this->hasPEARMail = false;
			die('No tienes instalado el plugin PEAR::Mail, es requerido para la funcionalidad de envio de emails por smtp para que nunca pasen tus email por spam.');
		endif;
	}
	function indexAction(){
		$this->title = 'Consola DB';
		$path=INST_PATH.'migrations/';
		$directorio=dir($path);
		$this->migrations = array();
		while (($archivo = $directorio->read()) != FALSE):
			if($archivo !="." and $archivo != ".." and $archivo != "_notes" and preg_match('[create_]', $archivo)):
				$nameTable = str_replace('.php', '', $archivo);
				$Object = Camelize($nameTable);
				$nameTable = str_replace('create_', '', $nameTable);
				$this->migrations = array_merge($this->migrations, array($Object => $nameTable));
			endif;
		endwhile;
		$directorio->close();
		asort($this->migrations);

		$pathmodels=INST_PATH.'app/models/';
		$dirmodels=dir($pathmodels);
		$this->models = array();
		while (($archivo = $dirmodels->read()) != FALSE):
			if($archivo !="." and $archivo != ".." and $archivo != "_notes" and preg_match('/.php/', $archivo)):
				$nameTable = str_replace('.php', '', $archivo);
				$nameTable = $this->Plurals($nameTable);
				$this->models[] = $nameTable;
			endif;
		endwhile;
		$dirmodels->close();
		asort($this->models);
	}

	function create_migrationsAction(){
		//Require_Admin_Login();
		if(isset($_POST['fields']['sub'])):
			$arraux = array();
			$arraux['Table'] = $_POST['create_migrations']['Table'];
			foreach($_POST['create_migrations'] as $row){
				if($row != $_POST['create_migrations']['Table'] and (isset($_POST['create_migrations']['scaffold']) and $row != $_POST['create_migrations']['scaffold']) and $row['Field'] !== ''):
					$arraux[] = array('field' => $row['Field'], 'type' => $row['Type'], 'null' => (isset($row['NotNull']) and $row['NotNull'] == 'on')? 'false': 'true', 'limit' => (isset($row['Length']))? $row['Length']: NULL);
				endif;
			}
			$path=INST_PATH.'migrations/';
			//$fp = fopen($path.'create_'.strtolower($arraux['Table']).'.php', "w+b");
			$string = "<?php \n class Create".Camelize(Singulars(strtolower($arraux['Table'])));
			$string .= " extends Migrations{ \n";
			$string .= "\tfunction up(){ \n";
			$string .= "\t\t".'$this'."->Create_Table(array('Table'=>'".$arraux['Table']."', \n";
			$i=2;
			foreach($arraux as $row){
				if($row != $arraux['Table']):
					$string .= "\t\t\t\t\t\t\t\tarray('field'=>'".$row['field']."', 'type'=>'".$row['type']."', 'null'=>'".$row['null']."'";
					if(isset($row['limit']) and $row['limit']) $string .= ", 'limit'=>'".$row['limit']."'";
					$string .= ")";
					if($i < sizeof($arraux)):
						$string .= ", \n";
					else:
						$string .= "\n";
					endif;
					$i++;
				endif;
			}
			$string .= "\t\t\t\t\t\t\t)); \n";
			$string .= "\t}\n";
			$string .= "\tfunction down(){\n";
			$string .= "\t\t".'$this'."->Drop_Table('".$arraux['Table']."');\n";
			$string .= "\t}\n}\n";
			$string .= "?>";
			file_put_contents($path.'create_'.strtolower($arraux['Table']).'.php', $string);
			//fwrite($fp, $string);
			//fclose($fp);

			if(isset($_POST['create_migrations']['scaffold']) and $_POST['create_migrations']['scaffold'] = 'on'):
				include_once('Scaffold.php');
			endif;

		endif;
		header("Location: ".INST_URI."console/index");
		exit;
	}

	function migrationsAction(){
		//Require_Admin_Login();
		$this->MigrationsActions();
		header("Location: ".INST_URI."console/index");
		exit;
	}
	function dump_modelAction(){
		//Require_Admin_Login();
		if(isset($_POST['models']['dump:selected']) or isset($_POST['models']['dump:all'])):
			$pathXml=INST_PATH.'migrations/dumps/';
			$dirXml=dir($pathXml);
			if(isset($_POST['models']['dump:selected'])) $fordump = $_POST['models'];
			if(isset($_POST['models']['dump:all'])):
				$pathmodels=INST_PATH.'app/models/';
				$dirmodels=dir($pathmodels);
				$models = array();
				while (($archivo = $dirmodels->read()) != FALSE):
					if($archivo !="." and $archivo != ".." and $archivo != "_notes" and preg_match('/.php/', $archivo)):
						$nameTable = str_replace('.php', '', $archivo);
						//$nameTable = $this->Plurals($nameTable);
						$models[] = $nameTable;
					endif;
				endwhile;
				foreach($models as $model){
					$fordump[$model] = 'on';
				}
			endif;
			if(isset($fordump)):
				foreach($fordump as $model => $value){
					if($value == 'on'):
						$toDel = array();
						while (($archivo = $dirXml->read()) != FALSE):
							if($archivo !="." and $archivo != ".." and $archivo != "_notes" and preg_match("[.xml]", $archivo)):
								$nameTable = str_replace('.xml', '', $archivo);
								if(in_array($nameTable,$_POST['models'],true)) $toDel[] = $archivo;
							endif;
						endwhile;

						foreach($toDel as $file){
							unlink($pathXml.$file);
						}
						$words = explode("_", $model);
						$i = sizeof($words) - 1;
						$words[$i] = $this->Singulars($words[$i]);
						$Object = implode("_", $words);
						$Object = $this->Camelize($Object);
						$data = $this->$Object->Find();
						$data->Dump($data->getArray(), $pathXml);
					endif;
				}
			endif;
			$dirXml->close();
		elseif(isset($_POST['models']['load:selected']) or isset($_POST['models']['load:all'])):
			$pathXml=INST_PATH.'migrations/dumps/';
			$dirXml=dir($pathXml);
			if(isset($_POST['models']['load:selected'])) $forload = $_POST['models'];
			if(isset($_POST['models']['load:all'])):
				$forload = array();
				foreach($models as $model){
					$forload[$model] = 'on';
				}
			endif;
			if(isset($forload)):
				foreach($forload as $model => $value){
					if($value == 'on'):
						$Object = $this->Singulars($model);
						$Object = $this->Camelize($Object);
						$this->$Object->LoadDump($model.'.xml');
					endif;
				}
			endif;
			$dirXml->close();
		endif;
		header("Location: ".INST_URI."console/index");
		exit;
	}
}

?>
