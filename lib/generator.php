<?php
file_exists('./config/host.php') or die('Generator must be executed at the top level of project path.'.PHP_EOL);
defined('INST_PATH') || define('INST_PATH', dirname(realpath('./')).'/');
set_include_path(
    '/etc/dumbophp'.PATH_SEPARATOR.
    '/etc/dumbophp/bin'.PATH_SEPARATOR.
    INST_PATH.'vendor'.PATH_SEPARATOR.
    INST_PATH.'vendor/rantes/dumbophp'.PATH_SEPARATOR.
    INST_PATH.'vendor/rantes/dumbophp/bin'.PATH_SEPARATOR.
    INST_PATH.PATH_SEPARATOR.
    get_include_path().PATH_SEPARATOR.
    PEAR_EXTENSION_DIR.PATH_SEPARATOR.
    '/windows/dumbophp'.PATH_SEPARATOR.
    '/windows/dumbophp/bin'.PATH_SEPARATOR.
    '/windows/system32/dumbophp'.PATH_SEPARATOR.
    '/windows/system32/dumbophp/bin'.PATH_SEPARATOR.
    INST_PATH.'DumboPHP'
);

require_once './config/host.php';
require_once 'dumbophp.php';
require_once 'DumboShellColors.php';

class FieldObject {

    public $name = '';
    public $type = '';
    public $isNull = 'false';
    public $types = [
        'primary',
        'integer',
        'biginteger',
        'string',
        'text',
        'float',
        'decimal'
    ];
    private $dbTypes = [
        'primary' => ['INTEGER','11','0'],
        'integer' => ['INTEGER','11','0'],
        'biginteger' => ['BIGINT','','0'],
        'string' => ['VARCHAR','255',''],
        'text' => ['TEXT','',''],
        'float' => ['FLOAT','','0'],
        'decimal' => ['FLOAT','','0']
    ];

    public function __construct($field) {
        $args = explode(':', $field);
        $argsSize = sizeof($args);
        $matches = [];

        ($argsSize < 2) and die('Error on Building: Invalid field definition.'.PHP_EOL);
        if(preg_match('/\{([0-9]+)\}/is', $args[1], $matches) === 1):
            empty($matches[1]) and die("Error on Building: Limit size for the field '{$args[0]}' is not valid.".PHP_EOL);
            $toRemove = strlen($matches[0]) * -1;
            $args[1] = substr($args[1], 0, $toRemove);
            $this->dbTypes[$args[1]][1] = $matches[1];
        endif;

        in_array($args[1], $this->types) or die("Error on Building: Data type for the field '{$args[0]}', is not valid.".PHP_EOL);

        $this->name = $args[0];
        $this->type = $args[1];

        if($argsSize > 2):
            in_array('null', $args) and ($this->isNull = 'true');
            in_array('default', $args) and die("Default flag given but value is missing for field '{$args[0]}'.".PHP_EOL);

            for ($i=1; $i < $argsSize; $i++):
                if(preg_match('/default\{(.+)\}/', $args[$i], $matches) === 1):
                    empty($matches[1]) and die("Error on Building: Default value for the field '{$args[0]}' is not valid.".PHP_EOL);
                    $this->dbTypes[$args[1]][2] = $matches[1];
                    break;
                endif;
            endfor;
        endif;

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
        ($this->type === 'primary') and $str = "{$str}, 'primary'=>true, 'autoincrement'=>true";
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
    private $_scaffoldFolder = '';

    public function __construct($env = '') {
        empty($env) || ($GLOBALS['env'] = $env);
        $this->colors = new DumboShellColors();
        $this->_scaffoldFolder = INST_PATH.'scaffold/';
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
    /**
     * Will handle the model files generator.
     * will attempt to create a migration then will set the table up in database.
     */
    public function model(array $params) {
        $this->showMessage('Building: Creating model...');

        (!empty($params[1]) and $params[1] === 'no-migration') or $this->migration($params);
        empty($this->tblName) and $this->setNames($params[0]);

        $file = $this->singularized.'.php';
        $path = INST_PATH.'app/models/';

        file_exists($path.$file) and $this->showError('Error on Building: Model already exists.') and die();

        if(file_exists("{$this->_scaffoldFolder}model.tpl")):
            $fileContent = file_get_contents("{$this->_scaffoldFolder}model.tpl");
            $fileContent = str_replace('{{model}}', $this->camelized, $fileContent);
        else:
            $fileContent = <<<DUMBOPHP
<?php
class $this->camelized extends ActiveRecord {
    function _init_() {

    }
}

DUMBOPHP;
        endif;

        file_put_contents("{$path}{$file}", $fileContent);
        $this->showNotice("Model created at: {$path}{$file}");

        return true;
    }

    public function controller($params, $isScaffold = false) {
        $this->showMessage('Building: Creating controller...');

        empty($this->tblName) and $this->setNames($params[0]);

        $file = $this->singularized.'_controller.php';
        $path = INST_PATH.'app/controllers/';

        if(file_exists("{$this->_scaffoldFolder}controller.tpl")):
            $fileContent = file_get_contents("{$this->_scaffoldFolder}controller.tpl");
            $fileContent = str_replace('{{controller}}', "{$this->camelized}Controller", $fileContent);
        else:
            $fileContent = <<<DUMBOPHP
<?php
class {$this->camelized}Controller extends Page {
    public \$layout = 'layout';
    {{content}}
}

DUMBOPHP;
        endif;

        $content = '';

        if ($isScaffold):
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
        elseif(sizeof($params) > 1):
            while(empty($param = array_shift($params))):
                $content .= <<<DUMBOPHP

public function {$param}Action() {

}
DUMBOPHP;
            endwhile;
        endif;

        $fileContent = str_replace('{{content}}', $content, $fileContent);
        file_put_contents("{$path}{$file}", $fileContent);
        $this->showNotice("Controller created at: {$path}{$file}");
    }

    public function views($params, $isScaffold = false) {
        $this->showMessage('Building: Creating views...');

        empty($this->tblName) and $this->setNames($params[0]);

        $path = INST_PATH.'app/views/'.$this->singularized.'/';
        is_dir($path) or mkdir($path);

        if ($isScaffold):
            require_once INST_PATH.'app/models/'.$this->singularized.'.php';
            $model = new $this->camelized();
            $obj = $model->Find();

            $this->fields = $model->getRawFields();
            $columnNames = '';
            $dataRow = '';
            $formContent = '';
            foreach($this->fields as $field):
                $columnNames .= "<th>{$field}</th>\n";
                $dataRow .= "<td><?=\$row->{$field};?></td>\n";
                $formContent .= $obj->input_for($field)."\n";
            endforeach;

            $file = 'index.phtml';

            if(file_exists("{$this->_scaffoldFolder}list_view.tpl")):
                $fileContent = file_get_contents("{$this->_scaffoldFolder}list_view.tpl");
                $fileContent = str_replace('{{controller}}', "{$this->singularized}", $fileContent);
                $fileContent = str_replace('{{column_names}}', $columnNames, $fileContent);
                $fileContent = str_replace('{{data}}', $dataRow, $fileContent);
            else:

                $fileContent = <<<DUMBOPHP
<section>
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
              {$dataRow}
              <td>
                <a href="<?=INST_URI;?>{$this->singularized}/delete/<?=\$row->id;?>">delete</a>
                <a href="<?=INST_URI;?>{$this->singularized}/addedit/<?=\$row->id;?>">Edit</a>
              </td>
            </tr>
            <? endforeach; ?>
            </tbody>
        </table>
    </div>
    <div>
        <a href="<?=INST_URI;?>{$this->singularized}/addedit/">Add new...</a>
    </div>
</section>
DUMBOPHP;
            endif;

            file_put_contents("{$path}{$file}", $fileContent);
            $this->showNotice("View created at: {$path}{$file}");

            $file = 'addedit.phtml';

            $fileContent = <<<DUMBOPHP
<div>
    <div>
        <form action="/{$this->singularized}/create/" name="{$this->singularized}">
        $formContent
        <input name="submit" type="submit" id="submit-{$this->singularized}" value="Submit" />
        </form>
    </div>
</div>
DUMBOPHP;
            file_put_contents("{$path}{$file}", $fileContent);
            $this->showNotice("View created at: {$path}{$file}");
        elseif(sizeof($params) > 1):
            for ($i=1; $i < sizeof($params); $i++) {
                if (!empty($params[$i])) {
                    $file = "{$params[$i]}.phtml";
                    file_put_contents("{$path}{$file}", '');
                    $this->showNotice("View created at: {$path}{$file}");
                }
            }
        else:
            $this->showNotice("No view created.");
        endif;
    }

    public function migration($params) {
        $this->showMessage('Building: Creating migration...');

        empty($params[1]) and die('Error on Building: fields params are mandatory.'.PHP_EOL);

        empty($this->tblName) and $this->setNames(array_shift($params));

        while(null !== ($param = array_shift($params))):
            $this->fields[] = new FieldObject($param);
        endwhile;

        $path = INST_PATH.'migrations/';
        $file = "create_{$this->tblName}.php";

        file_exists($path.$file) and die('Error on Building: Migration already exists.'.PHP_EOL);

        $fieldsString = implode(",\n            ", $this->fields);
        $fileContent = <<<DUMBOPHP
<?php
class Create{$this->camelized} extends Migrations {
    function _init_() {
        \$this->_fields = [
            {$fieldsString}
        ];
    }

    function up() {
        \$this->Create_Table();
    }

    function down() {
        \$this->Drop_Table();
    }
}

DUMBOPHP;

        file_put_contents("{$path}{$file}", $fileContent);
        $this->showNotice("Migration created at: {$path}{$file}");
        $this->showMessage('Building: Running migration...');
        require_once $path.$file;
        $class = "Create{$this->camelized}";
        $obj = new $class();
        $obj->up();
        $this->showNotice('Migration executed.');

        return true;
    }

    public function scaffold($params) {
        $this->model($params);
        $this->controller($params, true);
        $this->views($params, true);

        return true;
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

DUMBOPHP;

        file_put_contents("{$path}{$file}", $fileContent);
        $this->showNotice("Seed file created at: {$path}{$file}");

        return true;
    }
}

?>
