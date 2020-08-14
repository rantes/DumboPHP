<?php
set_include_path(implode(PATH_SEPARATOR, array(get_include_path(),PEAR_EXTENSION_DIR, '/etc/dumbophp', '/windows/system32/dumbophp', '/windows/dumbophp')));
file_exists('./config/host.php') or die('Generator must be executed at the top level of project path.'.PHP_EOL);

require_once './config/host.php';
require_once 'dumbophp.php';
require_once 'colorClass.php';

class FieldObject {

  public $name = '';
  public $type = '';
  public $isNull = 'false';
  public $types = array(
            'primary',
            'integer',
            'biginteger',
            'string',
            'text',
            'float',
            'decimal'
          );
  private $dbTypes = array(
              'primary' => array('INT AUTO_INCREMENT PRIMARY KEY','',''),
              'integer' => array('INT','',''),
              'biginteger' => array('BIGINT','',''),
              'string' => array('VARCHAR','255',''),
              'text' => array('TEXT','',''),
              'float' => array('FLOAT','',''),
              'decimal' => array('FLOAT','','')
            );

    public function __construct($field) {
        $args = explode(':', $field);
        $argsSize = sizeof($args);
        $matches = [];

        ($argsSize < 2) and die('Error on Building: Invalid field definition.'.PHP_EOL);
        if(preg_match('/\{([0-9]+)\}/is', $args[1], $matches) === 1) {
        empty($matches[1]) and die("Error on Building: Limit size for the field '{$args[0]}' is not valid.".PHP_EOL);
        $toRemove = strlen($matches[0]) * -1;
        $args[1] = substr($args[1], 0, $toRemove);
        $this->dbTypes[$args[1]][1] = $matches[1];
        }

        in_array($args[1], $this->types) or die("Error on Building: Data type for the field '{$args[0]}', is not valid.".PHP_EOL);

        $this->name = $args[0];
        $this->type = $args[1];

        if($argsSize > 2) {
        in_array('null', $args) and ($this->isNull = 'true');
        in_array('default', $args) and die("Default flag given but value is missing for field '{$args[0]}'.".PHP_EOL);

        for ($i=1; $i < $argsSize; $i++) {
            if(preg_match('/default\{(.+)\}/', $args[$i], $matches) === 1) {
            empty($matches[1]) and die("Error on Building: Default value for the field '{$args[0]}' is not valid.".PHP_EOL);
            $this->dbTypes[$args[1]][2] = $matches[1];
            break;
            }
        }
        }

    }

    public function getArray() {
        return [
            'field' => $this->name,
            'type'=>$this->dbTypes[$this->type][0],
            'null' => $this->isNull,
            'limit' => $this->dbTypes[$this->type][1],
            'default' => $this->dbTypes[$this->type][2]
        ];
    }

    public function __toString() {
        $arr = $this->getArray();

        $str = "['field'=>'{$arr['field']}', 'type'=>'{$arr['type']}', 'null'=>'{$arr['null']}'";
        empty($arr['limit']) or ($str .= ", 'limit'=>'{$arr['limit']}'");
        empty($arr['default']) or ($str .= ", 'default'=>'{$arr['default']}'");
        $str .= ']';

        return $str;
    }
}

class DumboGeneratorClass {

    public $args = null;
    public $tblName = '';
    public $camelized = '';
    public $singularized = '';
    private $fields = array();
    private $colors = null;

    public function __construct($env = '') {
        empty($env) || ($GLOBALS['env'] = $env);
        $this->colors = new Colors();
    }

    public function showError($errorMessage) {
        fwrite(STDOUT, $this->colors->getColoredString($errorMessage, 'white', 'red') . "\n");
    }

    public function showMessage($errorMessage) {
        fwrite(STDOUT, $this->colors->getColoredString($errorMessage, 'white', 'green') . "\n");
    }

    public function showNotice($errorMessage) {
        fwrite(STDOUT, $this->colors->getColoredString($errorMessage, 'blue', 'yellow') . "\n");
    }

    public function setNames($name) {
        $this->tblName = $name;
        $this->singularized = Singulars($this->tblName);
        $this->camelized = Camelize($this->singularized);

        return true;
    }

    public function model(&$params) {
        $this->showMessage('Building: Creating model...');

        (!empty($params[1]) and $params[1] === 'no-migration') or $this->migration($params);
        empty($this->tblName) and $this->setNames($params[0]);

        $file = $this->singularized.'.php';
        $path = INST_PATH.'app/models/';

        file_exists($path.$file) and $this->showError('Error on Building: Model already exists.') and die();

        $fileContent = <<<DUMBOPHP
<?php
class $this->camelized extends ActiveRecord {
    function _init_() {

    }
 }
?>
DUMBOPHP;

        file_put_contents("{$path}{$file}", $fileContent);
        $this->showNotice("Model created at: {$path}{$file}");

        return true;
    }

    public function controller($params, $isScaffold = false) {
        echo 'Building: Creating controller...',PHP_EOL;

        empty($this->tblName) and $this->setNames($params[0]);

        $file = $this->singularized.'_controller.php';
        $path = INST_PATH.'app/controllers/';

        $fileContent = <<<DUMBOPHP
<?php
class {$this->camelized}Controller extends Page {
    public \$layout = 'layout';
    {{content}}
}
?>
DUMBOPHP;

        $content = '';

        if ($isScaffold) {
        $content = <<<DUMBOPHP
    public \$noTemplate = array('create','delete');

    public function indexAction() {
        \$this->data = \$this->{$this->camelized}->Find();
    }

    public function addeditAction() {
        if (isset(\$this->params['id'])):
            \$this->data = \$this->{$this->camelized}->Find(\$this->params['id']);
        else:
            \$this->data = \$this->{$this->camelized}->Niu();
        endif;
    }

    public function deleteAction() {
        if (isset(\$this->params['id'])):
            \$this->data = \$this->{$this->camelized}->Delete(\$this->params['id']);
        endif;

        header('Location: '.INST_URI.'{$this->singularized}/index/');
        exit;
    }

    public function createAction() {
        if (isset(\$_POST['$this->singularized'])):
            \$obj = \$this->{$this->camelized}->Niu(\$_POST['$this->singularized']);
            \$obj->Save() or die(\$obj->_error);
        endif;

        header('Location: '.INST_URI.'{$this->singularized}/index/');
        exit;
    }
DUMBOPHP;
        } elseif(sizeof($params) > 1) {
        for ($i=1; $i < sizeof($params); $i++) {
            if (!empty($params[$i])) {
            $content .= <<<DUMBOPHP

    public function {$params[$i]}Action() {

    }
DUMBOPHP;
                }
            }
        }

        $fileContent = str_replace('{{content}}', $content, $fileContent);
        file_put_contents("{$path}{$file}", $fileContent);
        $this->showNotice("Controller created at: {$path}{$file}");

        $this->views($params, $isScaffold);
    }

    public function views($params, $isScaffold) {
        $this->showMessage('Building: Creating views...');

        empty($this->tblName) and $this->setNames($params[0]);

        $path = INST_PATH.'app/views/'.$this->singularized.'/';
        is_dir($path) or mkdir($path);

        if ($isScaffold) {
            require_once INST_PATH.'app/models/'.$this->singularized.'.php';
            $model = new $this->camelized();

            $this->fields = $model->getRawFields();
            $columnNames = '';
            $dataRow = '';
            $formContent = '';
            foreach($this->fields as $field){
                $columnNames .= "                    <th>{$field}</th>\n";
                $dataRow .= "                    <td><?=\$row->{$field};?></td>\n";
                $formContent .= ($field !== 'id')? "                <label>{$field} :</label>\n" : '';
                $formContent .= "                <?=\$this->data->input_for(array('{$field}'));?>\n";
            }

        $file = 'index.phtml';

        $fileContent = <<<DUMBOPHP
      <div>
        <div>
          <table>
            <thead>
            <tr>
              {$columnNames}
              <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <? foreach(\$this->data as \$row): ?>
            <tr>
              $dataRow
              <td>
                <a href="<?=INST_URI;?>{$this->singularized}/delete/<?=\$row->id;?>">delete</a>
                <a href="<?=INST_URI;?>{$this->singularized}/addedit/<?=\$row->id;?>">Edit</a>
              </td>
            </tr>
            <? endforeach; ?>
            </tbody>
          </table>
        </div>
        <a href="<?=INST_URI;?>{$this->singularized}/addedit/">Add new...</a>
      </div>
DUMBOPHP;

        file_put_contents("{$path}{$file}", $fileContent);
        $this->showNotice("View created at: {$path}{$file}");

        $file = 'addedit.phtml';

        $fileContent = <<<DUMBOPHP
  <div>
    <div>
      <?=\$this->data->form_for(array('action'=>INST_URI.'{$this->singularized}/create/'));?>
$formContent
      <input name="submit" type="submit" id="submit" value="Submit" />
      <?=end_form_for();?>
    </div>
  </div>
DUMBOPHP;
        file_put_contents("{$path}{$file}", $fileContent);
        $this->showNotice("View created at: {$path}{$file}");
        } elseif(sizeof($params) > 1) {
        for ($i=1; $i < sizeof($params); $i++) {
            if (!empty($params[$i])) {
            $file = "{$params[$i]}.phtml";
            file_put_contents("{$path}{$file}", '');
            $this->showNotice("View created at: {$path}{$file}");
            }
        }
        } else {
            $this->showNotice("No view created.");
        }
    }

    public function migration($params) {
        $this->showMessage('Building: Creating migration...');

        empty($params[1]) and die('Error on Building: fields params are mandatory.'.PHP_EOL);

        for ($i=1; $i < sizeof($params); $i++) {
        $this->fields[] = new FieldObject($params[$i]);
        }

        empty($this->tblName) and $this->setNames($params[0]);

        $path = INST_PATH.'migrations/';
        $file = "create_{$this->tblName}.php";

        file_exists($path.$file) and die('Error on Building: Migration already exists.'.PHP_EOL);

        $fileContent = <<<DUMBOPHP
<?php
class Create{$this->camelized} extends Migrations {
    function _init_() {
          \$this->_fields = [
              {{fields}}
          ];
    }

    function up() {
        \$this->Create_Table();
    }

    function down() {
        \$this->Drop_Table();
    }
}
?>

DUMBOPHP;
        $fieldsString = implode(",\n                ", $this->fields);
        $fileContent = str_replace('{{fields}}', $fieldsString, $fileContent);

        file_put_contents("{$path}{$file}", $fileContent);
        $this->showNotice("Migration created at: {$path}{$file}");
        $this->showMessage('Building: Running migration...');
        require_once $path.$file;
        $class = "Create{$this->camelized}";
        $obj = new $class();
        $obj->up();
        $this->showNotice("Migration executed.");

        return true;
    }

    public function scaffold($params) {
        $this->model($params);
        $this->controller($params, true);
    }

    public function seed() {
        $this->showMessage('Building: Creating seed...');

        $path = INST_PATH.'migrations/';
        $file = 'seeds.php';

        file_exists($path.$file) and die('Error on Building: Seed file already exists.'.PHP_EOL);

        $fileContent = <<<DUMBOPHP
<?php
class Seed extends Page {
    function sow() {

    }
}
?>
DUMBOPHP;

        file_put_contents("{$path}{$file}", $fileContent);
        $this->showNotice("Seed file created at: {$path}{$file}");

        return true;
    }
}

?>
