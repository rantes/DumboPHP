<?
$arraux = (isset($arraux))? $arraux: NULL;
$path=INST_PATH;
				$_POST['migrations'][$arraux['Table']] = 'on';
				$_POST['migrations']['up:selected'] = true;
				$this->MigrationsActions();
				$file = Singulars(strtolower($arraux['Table']));
				// Creation of model
				$Obj = new NewAr();
				$Obj->WriteModel($arraux['Table']);
				
				
				// Creation of the list view
				$ClassName = Camelize($arraux['Table']);
				$ClassToUse = Camelize(Singulars($arraux['Table']));
				$directory = INST_PATH."app/templates/$file/";
				if(!is_dir($directory)) mkdir($directory);
				$fp = fopen($directory.'index.phtml', 'w+b');
				$content = '<div style="float:left; width:100%;">'."\n";
				$content .= "\t".'<div align="center">'."\n";
				$content .= "\t\t".'<table width="100%">'."\n";
				$content .= "\t\t\t".'<? foreach($this->data as $row){ ?>'."\n";
				$content .= "\t\t\t<tr>\n";
				$content .= "\t\t\t".'<? foreach($row->getArray() as $col){ ?>'."\n";
				$content .= "\t\t\t\t".'<td><?=$col;?></td>'."\n";
				$content .= "\t\t\t<? } ?>\n";
				$content .= "\t\t\t\t<td><a href=\"<?=INST_URI;?>$file/delete/<?=\$row->id;?>\">delete</a>&nbsp;<a href=\"<?=INST_URI;?>$file/addedit/<?=\$row->id;?>\">Edit</a></td>\n";
				$content .= "\t\t\t</tr>\n";
				$content .= "\t\t\t<? } ?>\n";
				$content .= "\t\t</table>\n";
				$content .= "\t</div>\n";
				$content .= "<a href=\"<?=INST_URI;?>$file/addedit/\">Add new...</a>\n</div>";
				fwrite($fp, $content);
				fclose($fp);
				
				//Creation of add/edit template
				$name = Singulars($arraux['Table']);
				$fileModel = $name.".php";
				include_once(INST_PATH.'app/models/'.$fileModel);
				
				$objModel = new $ClassToUse();

				$fields = $objModel->GetFields();

				$fp = fopen(INST_PATH.'app/templates/'.$file.'/addedit.phtml', 'w+b');
				$content = '<div style="float:left; width:100%;">'."\n";
				$content .= "\t".'<div align="center">'."\n";
				$content .= "\t\t<form action=\"<?=INST_URI;?>{$file}/create/\" method=\"post\" name=\"$name\" id=\"{$ClassToUse}_id\">\n";
				
				foreach($fields as $field => $type){
					$content .= "\t\t\t<label>$field:\n";
					$input = $this->GetInput($type);

					if($input == 'text'):
						$content .= "\t\t\t\t<input type=".'"text" name="'.$name."[$field]\" value=\"<?=\$this->data->$field;?>\" />\n";
					elseif($input == 'textarea'):
						$content .= "\t\t\t\t<textarea name=".'"'.$name."[$field]\"><?=\$this->data->$field;?></textarea>\n";
					endif;
					$content .= "\t\t\t</label><br />\n";				
				}
				$content .= "\t\t\t <input name=\"submit\" type=\"submit\" id=\"submit\" value=\"Submit\" />\n";
				$content .= "\t\t\t <input type=\"reset\" name=\"Reset\" id=\"reset\" value=\"Reset\" />\n";
				$content .= "\t\t</form>\n";
				$content .= "\t</div>\n";
				$content .= "</div>";
				fwrite($fp, $content);
				fclose($fp);
				
				// Creation of the controller
				$fp = fopen(INST_PATH.'app/controllers/'.$file.'_controller.php', "w+b");
				$content = "<?php \n";
				$content .= "class ".$this->Camelize($file)."Controller extends Page{\n";
				$content .= "\tpublic \$noTemplate = array('create','delete');\n";
				$content .= "\tfunction indexAction(){\n";
				$content .= "\t\t \$this->data = \$this->$ClassToUse"."->Find();\n";
				$content .= "\t}\n";
				$content .= "\tfunction addeditAction(){\n";
				$content .= "\t\t if(isset(\$this->params['id'])):\n";
				$content .= "\t\t\t \$this->data = \$this->$ClassToUse"."->Find(\$this->params['id']);\n";
				$content .= "\t\t else:\n";
				$content .= "\t\t\t \$this->data = \$this->$ClassToUse"."->Niu();\n";
				$content .= "\t\t endif;\n";
				$content .= "\t}\n";
				$content .= "\tfunction deleteAction(){\n";
				$content .= "\t\t if(isset(\$this->params['id'])):\n";
				$content .= "\t\t\t \$this->$ClassToUse"."->Delete(\$this->params['id']);\n";
				$content .= "\t\t endif;\n";
				$content .= "\t\t header(\"Location: \".INST_URI.\"{$file}/index/\");\n";
				$content .= "\t\t exit;\n";
				$content .= "\t}\n";
				$content .= "\tfunction createAction(){\n";
				$content .= "\t\t if(isset(\$_POST['$file'])):\n";
				$content .= "\t\t\t \$obj = \$this->$ClassToUse"."->Niu(\$_POST['$file']);\n";
				$content .= "\t\t\t \$obj->Save();\n";
				$content .= "\t\t endif;\n";
				$content .= "\t\t header(\"Location: \".INST_URI.\"{$file}/index/\");\n";
				$content .= "\t\t exit;\n";
				$content .= "\t}\n}\n";
				$content .= "?>";
				fwrite($fp,$content);
				fclose($fp);
				
?>