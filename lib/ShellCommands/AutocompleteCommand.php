<?php
namespace DumboPHP\lib\ShellCommands;

// use \Exception;
use DumboPHP\lib\ShellCommands\Interfaces\DumboCommand;
use DumboPHP\lib\ShellCommands\BaseShell;
use function \get_class_methods;
use function DumboPHP\Camelize;
use function DumboPHP\Singulars;

class AutocompleteCommand extends BaseShell implements DumboCommand {
    private array $_commands = [
        'autocomplete',
        'help',
        'create',
        'run',
        'db',
        'generate',
        'destroy',
        'migration'
    ];

    public function help(): string {
        return "Autocomplete mechanism for shell commands.";
    }

    private function _getControllers() {
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
        $name = Camelize($controller);
        $className = "App\\Controllers\\{$name}Controller";
        $actions = [];

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

    public function execute(array $args, array $options): void {
        array_shift($this->_commands);
        $output = implode("\n", $this->_commands);

        if(!empty($args[0]) and in_array($args[0], $this->_commands)):
            switch ($args[0]):
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
}
