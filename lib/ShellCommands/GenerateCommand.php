<?php
namespace DumboPHP\lib\ShellCommands;

use \Exception;
use DumboPHP\lib\DumboGeneratorClass;
use function DumboPHP\Camelize;
use function DumboPHP\Singulars;


class GenerateCommand extends BaseShell implements Interfaces\DumboCommand {
    public array $params = [];
    private ?array $_migrations = [];

    public function execute(array $args, array $options): void {
        $this->showMessage("Generating...\n");

        if (empty($args[0]) || $args[0] !== 'seed' && sizeof($args) < 2) {
            throw new Exception('Error: Not enough arguments.');
        }

        for ($i = 1; $i < sizeof($args); $i++) {
            $this->params[] = $args[$i];
        }

        $generator = new DumboGeneratorClass($options['env']['value']);

        switch ($args[0]) {
        case 'scaffold':
            $this->showNotice('Creating scaffold for "' . $args[1] . '".');
            $generator->scaffold($this->params);
            break;
        case 'controller':
            $this->showNotice('Creating controller: "' . $args[1] . '".');
            $generator->controller($this->params);
            $generator->views($this->params);
            break;
        case 'model':
            $this->showNotice('Creating model: "' . $args[1] . '".');
            $generator->model($this->params);
            break;
        case 'seed':
            $this->showNotice('Creating seed file...');
            $generator->seed();
            break;

        default:
            throw new Exception('Error: Not enough arguments.');
        }
        $this->showMessage("✔ Generation completed.\n");
    }

    public function help(): string {
        return "generate Generates new files based on blueprints";
    }
}
