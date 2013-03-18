<?php
/**
* Generaci&oacute;n de los archivos controlador y vistas.
* @package Core
*/
$arraux = (isset($arraux))? $arraux: NULL;
$path=INST_PATH;
				$_POST['migrations'][$arraux['Table']] = 'on';
				$_POST['migrations']['up:selected'] = true;
				$this->MigrationsActions();
				$file = Singulars(strtolower($arraux['Table']));
				// Creation of model
				$Obj = new NewAr();
				$Obj->WriteModel($arraux['Table']);
				
				$name = Singulars($arraux['Table']);
				$fileModel = $name.".php";
				$ClassName = Camelize($arraux['Table']);
				$ClassToUse = Camelize(Singulars($arraux['Table']));
				require_once(INST_PATH.'app/models/'.$fileModel);
				
				$objModel = new $ClassToUse();
				
				$fields = $objModel->GetFields();
				
				
				// Creation of the list view
				
				$directory = INST_PATH."app/templates/$file/";
				if(!is_dir($directory)) mkdir($directory);
				$fp = fopen($directory.'index.phtml', 'w+b');
				$columnNames = '';
				$dataRow = '';
				$formContent = '';
				foreach($fields as $field => $type){
					$columnNames .= "<th>$field</th>\n";
					$dataRow .= "<td><?=\$row->$field;?></td>\n\t\t\t\t\t";
					$formContent .= "<label>$field :</label>\n<?=\$this->data->input_for(array('$field'));?>\n";
				}
				
				$content = <<<PLUSTURBO
				<div style="float:left; width:100%;text-align:center;">
					<div style="float:left;">
						<table>
							<thead>
							<tr>
								$columnNames
								<th>Actions</th>
							</tr>
							</thead>
							<tbody>
							<? foreach(\$this->data as \$row): ?>
							<tr>
								$dataRow
								<td>
									<a href="<?=INST_URI;?>$file/delete/<?=\$row->id;?>" style="margin-right:10px;">delete</a>
									<a href="<?=INST_URI;?>$file/addedit/<?=\$row->id;?>">Edit</a>
								</td>
							</tr>
							<? endforeach; ?>
							</tbody>
						</table>
					</div>
					<a href="<?=INST_URI;?>$file/addedit/">Add new...</a>
				</div>
PLUSTURBO;
				fwrite($fp, $content);
				fclose($fp);
				
				//Creation of add/edit template
				

				$fp = fopen(INST_PATH.'app/templates/'.$file.'/addedit.phtml', 'w+b');
				$content = <<<PLUSTURBO
				<div style="float:left; width:100%;text-align:center;">
					<div style="float:left;">
						<?=\$this->data->form_for(array('action'=>INST_URI.'$file/create/'));?>
						$formContent
						<input name="submit" type="submit" id="submit" value="Submit" />
						<?=end_form_for();?>
					</div>
				</div>
PLUSTURBO;
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