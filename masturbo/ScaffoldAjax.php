<?
//$path=INST_PATH;
//				$file = $this->Singulars(strtolower($arraux['Table']));
//				$table = $this->Plurals($file);
//				// Creation of the list view
//				$ClassName = $this->Camelize($arraux['Table']);
//				$ClassToUse = $this->Camelize($this->Singulars($arraux['Table']));
//				$directory = $path."templates/$file/";
//				if(!is_dir($directory)) mkdir($directory);
//				$fp = fopen($directory.'list.tpl', 'w+b');
//				
//				$content="<div id=\"form_add\" style=\"display:none;\">
//					{include file=$file/addedit.tpl}
//				</div>
//				<div style=\"float:left; width:100%;\" id=\"list_id\">
//				
//				</div>
//				<a href=\"#\" id=\"add_new\">Add new...</a>
//				
//				{literal}
//				<script language=\"javascript\" type=\"text/javascript\">
//					$(document).ready(function() {
//						$.ajax({
//							url: \"List.php?table=$table\",
//							cache: false,
//							success: function(html){
//								$(\"#list_id\").append(html).fadeIn('slow');
//								
//							}
//						});
//						$(\"#add_new\").click(function() {
//							$(\"#form_add\").hide(\"slow\");
//							$(\"#".$table."_id\").val(\"\");
//							$(\"#".$table."_color\").val(\"\");
//							$(\"#form_add\").show(\"slow\");
//						});
//				
//						$(\"#submit\").click(function() {
//							$.post('/$file.php',	$(\"#$table\").serialize(),
//								function(data) {
//									$('#list_id').fadeOut('slow');
//									$('#list_id').html(\"\");
//									$.ajax({
//										url: \"List.php?table=$table\",
//										cache: false,
//										success: function(html){
//											$(\"#list_id\").append(html).fadeIn('slow');
//										}
//									});
//								}
//							);
//							return false; 
//						});
//						$(\".linkdelete\").live(\"click\", function () {
//																 
//							$.post('/$file.php',	{opt: 'del', id: $(this).attr(\"id\")},
//								function(data) {
//									$('#list_id').fadeOut('slow');
//									$('#list_id').html(\"\");
//									$.ajax({
//										url: \"List.php?table=$table\",
//										cache: false,
//										success: function(html){
//											$(\"#list_id\").append(html).fadeIn('slow');
//										}
//									});
//								}
//							);
//						});
//						
//						$(\".linkedit\").live(\"click\", function () {
//							$(\"#form_add\").fadeOut(\"slow\");
//							$(\"#".$table."_id\").val($(this).attr(\"id\"));
//							$(\"#".$table."_color\").val($(\"#color_\"+$(this).attr(\"id\")).html());
//							$(\"#form_add\").fadeIn(\"slow\");	
//						});
//						
//					});
//				
//				</script>
//				{/literal}";
//				fwrite($fp, $content);
//				fclose($fp);
//				
//				// Creation of the controller
//				$fp = fopen($path.$file.'.php', "w+b");
//				$content = "<?php \n";
//				$content .= "require('Page.php');\n";
//				$content .= "require_once(INST_PATH.'core/Migrations.php');\n\n";
//				$content .= "class ".$ClassName."Page extends Page{\n";
//				$content .= "\tfunction __construct(){\n";
//				$content .= "\t\t parent::__construct();\n\n";
//				
//				$content .= "\t\t\tif(isset(\$_POST['$table'])):
//					\$data = \$this->$ClassToUse"."->Niu(\$_POST['$table']);
//					\$data->Save();
//				elseif(isset(\$_POST['opt']) and !strcmp(\$_POST['opt'], 'del') and isset(\$_POST['id'])):
//					\$this->$ClassToUse"."->Delete((integer)\$_POST['id']);
//				else:
//					\$this->display('".$this->Singulars($table)."/list.tpl');
//				endif;\n";
//
//				$content .= "\t}\n}\n";
//				$content .= '$index'." = new ".$ClassName."Page();\n? >";
//				fwrite($fp,$content);
//				fclose($fp);
//				$Obj = new NewAr();
//				$Obj->WriteModel($arraux['Table']);
//				$_POST['migrations'][$arraux['Table']] = 'on';
//				$_POST['migrations']['up:selected'] = true;
?>