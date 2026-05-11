<?php
namespace DumboPHP\lib\ShellCommands;

class RunCommand extends BaseShell implements Interfaces\DumboCommand {
    public array $params = [];
    private ?array $_migrations = [];

    public function execute(array $args, array $options): void {
        $this->showMessage("Requesting...\n");

        empty($_SERVER['REQUEST_METHOD']) and ($_SERVER['REQUEST_METHOD'] = 'GET');
        $_GET['url'] = empty($args[0]) ? 'index/index' : $args[0];
        array_shift($args);
        while (null !== ($arg = array_shift($args))) {
            $param = explode('=', $arg);
            sizeof($param) === 2 and ($_GET[urldecode($param[0])] = urldecode($param[1]));
        }

        require_once 'app/webroot/index.php';
        $this->showMessage("✔ Request completed.\n");
    }

    public function help(): string {
        return "run Runs the application";
    }
}
