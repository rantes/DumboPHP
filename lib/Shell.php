<?php
namespace DumboPHP\lib;

use Exception;
use DumboPHP\lib\Exceptions\noCommandGiven;
use DumboPHP\lib\ShellCommands\Interfaces\DumboCommand;
use DumboPHP\lib\ShellCommands\BaseShell;
use DumboPHP\lib\ShellCommands\CreateCommand;
use DumboPHP\lib\ShellCommands\HelpCommand;

class Shell extends BaseShell {
    private array $commands = [
        'autocomplete',
        'help',
        'create',
        'run',
        'db',
        'generate',
        'destroy',
        'migration'
    ];
    private array $_options = [
        'env' => ['value' => null, 'cast' => 'string'],
        'halt' => ['value' => false, 'cast' => 'boolean'],
        'standalone' => ['value' => true, 'cast' => 'boolean'],
        'dir' => ['value' => null, 'cast' => 'string'],
        'watch' => ['value' => false, 'cast' => 'boolean']
    ];
    private ?string $command = null;

    private array $arguments = [];
    private array $params = [];

    private ?DumboCommand $_createCommand = null;
    private ?DumboCommand $_helpCommand = null;

    public function __construct() {
        parent::__construct();
        $this->_createCommand = new CreateCommand();
        $this->_helpCommand = new HelpCommand();
    }

    private function _parseOptions() {
        $trueFalse = ['true' => true, 'false' => false];
        foreach($this->arguments as $i => $arg) {
            preg_match('@\-\-([a-zA-Z0-9]+)\=([a-z0-9\-\_\/]+)[\s]*@im', $arg, $match);
            if (sizeof($match) === 3) {
                if(isset($this->_options[$match[1]])){
                    switch($this->_options[$match[1]]['cast']) {
                        case 'numeric':
                            $match[2] = (integer)$match[2];
                        break;
                        case 'boolean':
                            $match[2] = $trueFalse[strtolower($match[2])];
                        break;
                        case 'string':
                            $match[2] = trim((string)$match[2]);
                        break;
                        default:
                            throw new Exception("Value not allowed for {$match[1]}");
                        break;
                    }
                    $this->_options[$match[1]]['value'] = strlen($match[2]) > 0 ? $match[2] : null;
                }
                $this->arguments[$i] = null;
                unset($this->arguments[$i]);
            }
        }
    }

    public function validateApacheConf() {
        $modsRequired = [
            'mod_rewrite'
        ];
    }

    public function run(array $argv): void {

        try {
            if(empty($argv[1]) || sizeof($argv) < 2) {
                throw new noCommandGiven();
            }

            array_shift($argv);
            $this->command = array_shift($argv);
            $this->arguments = $argv;
            $this->_parseOptions();
            $this->{"_{$this->command}Command"}->execute($this->arguments, $this->_options);
        } catch (Exception $e) {
            $this->showError('Error: '.$e->getMessage());
            if ($this->_options['halt']['value']) {
                exit(1);
            }
        }
        // if(in_array($this->command, $this->commands)){
            // switch($this->command) {
            //     case 'generate':
            //         $this->generateScripts();
            //     break;
            //     case 'destroy':
            //         $this->destroyScripts();
            //     break;
            //     case 'db':
            //         $this->dbScripts();
            //     break;
            //     case 'migration':
            //         $this->migrationScripts();
            //     break;
            //     case 'init':
            //         $this->initAppScript();
            //     break;
            //     case 'run':
            //         $this->runActionScript();
            //     break;
            //     case 'autocomplete':
            //         $this->_autocomplete();
            //     break;
            // }
        // } else {
        //     $this->help();
        // }

    }

    private function _getControllers() {
        file_exists('./config/host.php') or die();
        require_once './config/host.php';
        $controllersPath = INST_PATH.'app/controllers';
        $controllersDir = dir($controllersPath);
        $controllersList = [];
        while (($file = $controllersDir->read()) != false):
            if($file != "." and $file != ".." and preg_match('/(.+)_controller\.php/', $file, $matches) === 1):
                $file = preg_replace('/(_controller\.php)/', '', $file);
                $controllersList[] = $file;
            endif;
        endwhile;

        return $controllersList;
    }

    private function _getActions($controller) {
        file_exists('./config/host.php') or die();
        require_once './config/host.php';
        require_once "{$this->dumboBin}/dumbophp.php";

        $controllerFile = INST_PATH."app/controllers/{$controller}_controller.php";
        file_exists($controllerFile) or die();
        $className = ucfirst("{$controller}Controller");
        $actions = [];

        require_once $controllerFile;
        $methods = get_class_methods($className);
        while(null !== ($method = array_shift($methods))):
            if(preg_match('/^[^_](.+)Action/', $method)):
                $actions[] = str_replace('Action', '', $method);
            endif;
        endwhile;

        return $actions;
    }

    private function _getFullActions() {
        $list = [];
        $controllers = $this->_getControllers();
        while(null !== ($controller = array_shift($controllers))):
            $actions = $this->_getActions($controller);
            while(null !== ($action = array_shift($actions))):
                $list[] = "{$controller}/{$action}";
            endwhile;
        endwhile;

        return $list;
    }

    private function _autocomplete() {
        array_shift($this->commands);
        $output = implode("\n", $this->commands);

        if(!empty($this->arguments[0]) and in_array($this->arguments[0], $this->commands)):
            switch ($this->arguments[0]):
                case 'run':
                    $list = $this->_getFullActions();
                    $output = implode(' ', $list);
                break;
                case 'generate':
                    $list = ['scaffold', 'controller', 'model', 'seed'];
                    $output = implode(' ', $list);
                break;
                case 'destroy':
                    $list = ['scaffold', 'model'];
                    $output = implode(' ', $list);
                break;
                case 'migration':
                    $list = ['up', 'down', 'reset', 'run', 'sow'];
                    $output = implode(' ', $list);
                break;
                case 'db':
                    $list = ['dump', 'load'];
                    $output = implode(' ', $list);
                break;
            endswitch;
        endif;

        echo $output;
    }

    private function generateScripts() {
        if(empty($this->arguments[0]) || $this->arguments[0] !== 'seed' && sizeof($this->arguments) < 2) {
            $this->help();
            $this->showError('Error: Missing params.');
            die();
        }

        for ($i=1; $i < sizeof($this->arguments); $i++) {
            $this->params[] = $this->arguments[$i];
        }

        require_once "{$this->dumboLibs}/generator.php";
        $generator = new DumboGeneratorClass($this->_options['env']['value']);

        switch ($this->arguments[0]) {
            case 'scaffold':
                $this->showNotice('Creating scaffold for "'.$this->arguments[1].'".');
                $generator->scaffold($this->params);
            break;
            case 'controller':
                $this->showNotice('Creating controller: "'.$this->arguments[1].'".');
                $generator->controller($this->params);
                $generator->views($this->params);
            break;
            case 'model':
                $this->showNotice('Creating model: "'.$this->arguments[1].'".');
                $generator->model($this->params);
            break;
            case 'seed':
                $this->showNotice('Creating seed file...');
                $generator->seed();
            break;

            default:
                $this->help();
                die($this->showError('Option no valid for generate.'));
            break;
        }
    }

    private function destroyScripts() {
        if(empty($this->arguments[0]) or empty($this->arguments[1])) {
            $this->help();
            $this->showError('Error: Missing params.');
            die();
        }

        if(sizeof($this->arguments) > 2) {
            $this->help();
            $this->showError('Error: Only one model for delete at once.');
            die();
        }

        file_exists('./config/host.php') or die($this->showError('Destroy actions must be executed at the top level of project path.'.PHP_EOL));

        require_once './config/host.php';
        require_once "{$this->dumboBin}/dumbophp.php";

        empty($this->_options['env']['value']) || ($GLOBALS['env'] = $this->_options['env']['value']);

        switch ($this->arguments[0]) {
            case 'scaffold':
                $this->showNotice('Deleting scaffold for "'.$this->arguments[1].'".');
                $singular = singulars($this->arguments[1]);
                $model = INST_PATH."app/models/{$singular}.php";
                $migration = INST_PATH."migrations/create_{$this->arguments[1]}.php";
                $controller = INST_PATH."app/controllers/{$singular}_controller.php";
                $views = INST_PATH."app/views/{$singular}";

                $this->showNotice("Deleting model: {$model}");
                is_file($model) && unlink($model);

                $this->showNotice("Deleting migration: {$this->arguments[1]}");
                is_file($migration) && unlink($migration);

                $this->showNotice("Deleting controller: {$controller}");
                is_file($controller) && unlink($controller);

                if (is_dir($views)):
                    $dir = opendir($views);
                    while($file = readdir($dir)):
                        if ($file != "." && $file != ".."):
                            $this->showNotice("Deleting view: {$file}");
                            unlink("{$views}/{$file}");
                        endif;
                    endwhile;
                    closedir($dir);
                    $this->showNotice("Deleting views folder: {$views}");
                    rmdir($views);
                endif;
            break;
            case 'model':
                $model = INST_PATH.'app/models/'.singulars($this->arguments[1]).'.php';
                $migration = INST_PATH.'migrations/create_'.$this->arguments[1].'.php';
                $this->showNotice('Deleting model: "'.$model.'".');
                file_exists($model) or die($this->showError('Model file does not exists.'.PHP_EOL));
                unlink($model);
                $this->showNotice('Deleting migration: "'.$migration.'".');
                file_exists($migration) or die($this->showError('Migration file does not exists.'.PHP_EOL));
                unlink($migration);
            break;
            /**
             * @todo script for remove controller and views
             */
            case 'controller':
                $controller = INST_PATH.'app/controllers/'.$this->arguments[1].'_controller.php';
                $this->showNotice('Deleting controller: "'.$controller.'".');
                file_exists($controller) or die($this->showError('controller file does not exists.'.PHP_EOL));
                unlink($controller);
            break;
            default:
                $this->help();
                $this->showError('Option no valid for generate.');
                die();
            break;
        }
    }

    private function dbScripts() {
        if(empty($this->arguments[0]) or empty($this->arguments[1])) {
            $this->help();
            $this->showError('Error: Missing params.');
            die();
        }

        file_exists('./config/host.php') or die($this->showError('DB actions must be executed at the top level of project path.'.PHP_EOL));

        require_once './config/host.php';
        require "{$this->dumboBin}/dumbophp.php";

        empty($this->_options['env']['value']) || ($GLOBALS['env'] = $this->_options['env']['value']);

        $modelsPath = INST_PATH.'app/models/';
        $models = array();

        if ($this->arguments[1] === 'all') {
            $modelsDir = dir($modelsPath);
            while (($file = $modelsDir->read()) != FALSE) {
                if($file != "." and $file != ".." and preg_match('/(.+)\.php/', $file, $matches) === 1) {
                    $models[] = array('file'=>$matches[0],'model'=>$matches[1],'class'=>Camelize($matches[1]));
                }
            }
        } else {
            for ($i=1; $i < sizeof($this->arguments); $i++) {
                $name = Singulars($this->arguments[$i]);
                if (file_exists($modelsPath.$name.'.php')) {
                    $models[] = array('file'=>$name.'.php','model'=>$name,'class'=>Camelize($name));
                } else {
                    $this->showError('Model not found: '.$this->arguments[$i]);
                }
            }
        }

        switch ($this->arguments[0]) {
            case 'load':
                if (!empty($models)) {
                    foreach ($models as $model) {
                        $this->showNotice('Loading data for the model: "'.$model['model'].'".');
                        require_once $modelsPath.$model['file'];
                        $obj = new $model['class']();
                        $obj->LoadDump();
                    }
                }
            break;
            case 'dump':
                if (!empty($models)) {
                    foreach ($models as $model) {
                        $this->showNotice('Exporting data for the model: "'.$model['model'].'".');
                        require_once $modelsPath.$model['file'];
                        $obj = new $model['class']();
                        $data = $obj->Find();
                        $data->Dump();
                    }
                }
            break;
            default:
                $this->help();
                die($this->showError('Error: Option no valid.'));
            break;
        }
    }

    private function runActionScript() {
        empty($_SERVER['REQUEST_METHOD']) and ($_SERVER['REQUEST_METHOD'] = 'GET');
        $_GET['url'] = empty($this->arguments[0])? 'index/index' : $this->arguments[0];
        array_shift($this->arguments);
        while(null !== ($arg = array_shift($this->arguments))){
            $param = explode('=', $arg);
            sizeof($param) === 2 and ($_GET[urldecode($param[0])] = urldecode($param[1]));
        }

        require_once('app/webroot/index.php');
    }

    private function migrationScripts() {
        file_exists('./config/host.php') or die($this->showError('Migration actions must be executed at the top level of project path.'.PHP_EOL));

        require_once './config/host.php';
        require_once "{$this->dumboBin}/dumbophp.php";

        for ($i=1; $i < sizeof($this->arguments); $i++) {
            $this->params[] = $this->arguments[$i];
        }

        empty($this->arguments[0]) && die($this->showError('Error: Not enough arguments; the migrations to affect must be defined.'));

        ($this->arguments[0] === 'sow' || sizeof($this->params) > 0) or die($this->showError('Error: Not enough arguments; the migrations to affect must be defined.'));

        empty($this->_options['env']['value']) || ($GLOBALS['env'] = $this->_options['env']['value']);
        $migrationsPath = INST_PATH.'migrations/';
        $objects = [];

        if($this->arguments[0] !== 'sow') {
            if(sizeof($this->params) === 1 and $this->params[0] === 'all') {
                $migrationsDir = dir($migrationsPath);
                while (($file = $migrationsDir->read()) != false) {
                    if($file != "." and $file != ".." and preg_match('/create_(.+)\.php/', $file, $matches) === 1) {
                        echo PHP_EOL, 'Running action ', $this->arguments[0], ' for: ', $matches[1], PHP_EOL;
                        require_once $migrationsPath.$matches[0];
                        $class = 'Create'.Camelize(Singulars($matches[1]));
                        $objects[] = new $class();
                    }
                }
            } else {
                foreach ($this->params as $migration) {
                    $file = $migrationsPath.'create_'.$migration.'.php';
                    file_exists($file) or die('Migration file '.$migration.', does not exists.'.PHP_EOL);
                    echo PHP_EOL, 'Running action ', $this->arguments[0], ' for: ', $migration, PHP_EOL;
                    require_once $file;
                    $class = 'Create'.Camelize(Singulars($migration));
                    $objects[] = new $class();
                }
            }
        }

        switch ($this->arguments[0]) {
            case 'sow':
                $this->showNotice('Sowing the seeds of this project...');
                file_exists($migrationsPath.'seeds.php')  or die($this->showError('Error: No seeds file exists.'));
                require_once $migrationsPath.'seeds.php';
                $Seeds = new Seed();
                $Seeds->sow();
            break;
            case 'reset':
                foreach($objects as $obj) {
                    $obj->down();
                    $obj->up();
                }
            break;
            default:
                foreach($objects as $obj) {
                    $obj->{$this->arguments[0]}();
                }
            break;
        }
    }
}