<?php
namespace DumboPHP\lib\ShellCommands;

use \Exception;
use function DumboPHP\Camelize;
use function DumboPHP\Singulars;


class DBCommand extends BaseShell implements Interfaces\DumboCommand {
    public array $params = [];
    private ?array $_migrations = [];

    public function execute(array $args, array $options): void {
        $this->showMessage("Generating...\n");

        if (empty($args[0]) or empty($args[1])) {
            throw new Exception('Error: Missing params.');
        }

        file_exists('./config/host.php') or die($this->showError('DB actions must be executed at the top level of project path.' . PHP_EOL));

        require_once './config/host.php';

        // empty($this->_options['env']['value']) || ($GLOBALS['env'] = $this->_options['env']['value']);

        $modelsPath = INST_PATH . 'app/models/';
        $models     = [];

        if ($args[1] === 'all') {
            $modelsDir = dir($modelsPath);
            while (($file = $modelsDir->read()) != FALSE) {
                if ($file != "." and $file != ".." and preg_match('/(.+)\.php/', $file, $matches) === 1) {
                    $models[] = ['file' => $matches[0], 'model' => $matches[1], 'class' => Camelize($matches[1])];
                }
            }
        } else {
            for ($i = 1; $i < sizeof($args); $i++) {
                $name = Singulars($args[$i]);
                if (file_exists($modelsPath . $name . '.php')) {
                    $models[] = ['file' => $name . '.php', 'model' => $name, 'class' => Camelize($name)];
                } else {
                    $this->showError('Model not found: ' . $args[$i]);
                }
            }
        }

        switch ($args[0]) {
        case 'load':
            if (! empty($models)) {
                foreach ($models as $model) {
                    $this->showNotice('Loading data for the model: "' . $model['model'] . '".');
                    $className = "App\Models\\{$model['class']}";
                    $obj = new $className();
                    $obj->LoadDump();
                }
            }
            break;
        case 'dump':
            if (! empty($models)) {
                foreach ($models as $model) {
                    $this->showNotice('Exporting data for the model: "' . $model['model'] . '".');
                    $className = "App\Models\\{$model['class']}";
                    $obj  = new $className();
                    $data = $obj->Find();
                    $data->Dump();
                }
            }
            break;
        default:
            throw new Exception('Error: Option no valid.');
            break;
        }
        $this->showMessage("✔ Generation completed.\n");
    }

    public function help(): string {
        return "db Manages database operations";
    }
}
