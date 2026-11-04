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

        // app/webroot/index.php deja el front controller en $index. Si respondió 500 por un Throwable sin atrapar,
        // el detalle completo ya está en el log (STDERR); aquí solo se muestra el mensaje y se sale con código distinto de cero.
        $failure = isset($index) ? $index->page->failure() : null;
        $failure === null or fwrite(STDERR, 'Error: ' . get_class($failure) . ': ' . $failure->getMessage() . PHP_EOL);
        $failure === null or exit(1);

        $this->showMessage("✔ Request completed.\n");
    }

    public function help(): string {
        return "run Runs the application";
    }
}
