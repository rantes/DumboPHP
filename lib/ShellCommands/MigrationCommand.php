<?php
namespace DumboPHP\lib\ShellCommands;

use \Exception;
use DumboPHP\Migrations;
use function DumboPHP\Camelize;
use function DumboPHP\Singulars;


class MigrationCommand extends BaseShell implements Interfaces\DumboCommand {
    public array $params = [];
    private ?array $_migrations = [];

    public function execute(array $args, array $options): void {
        $this->showMessage("Running migrations...\n");
        if (!file_exists('./config/host.php')) {
            throw new Exception('Migration actions must be executed at the top level of project path.');
        }
        // require_once './config/host.php';
        // require_once "{$this->dumboBin}/dumbophp.php";

        for ($i=1; $i < sizeof($args); $i++) {
            $this->params[] = $args[$i];
        }

        if (empty($args[0]) || ($args[0] !== 'sow' && sizeof($this->params) === 0)) {
            throw new Exception('Error: Not enough arguments; the migrations to affect must be defined.');
        }



        // empty($this->_options['env']['value']) || ($GLOBALS['env'] = $this->_options['env']['value']);
        $migrationsPath = INST_PATH.'migrations/';
        $objects = [];

        if($args[0] !== 'sow') {
            if(sizeof($this->params) === 1 and $this->params[0] === 'all') {
                $migrationsDir = dir($migrationsPath);
                while (($file = $migrationsDir->read()) != false) {
                    if($file != "." and $file != ".." and preg_match('/create_(.+)\.php/', $file, $matches) === 1) {
                        $this->showNotice('º Running action ', $args[0], ' for: ', $matches[1]);

                        $class = 'Migrations\\Create'.Camelize(Singulars($matches[1]));
                        $this->_migrations[] = new $class();
                    }
                }
            } else {
                foreach ($this->params as $migration) {
                    $this->showNotice('º Running action ', $args[0], ' for: ', $migration);
                    $class = 'Migrations\\Create'.Camelize($migration);
                    $this->_migrations[] = new $class();
                }
            }
        }

        switch ($args[0]) {
            case 'sow':
                $this->showNotice('Sowing the seeds of this project...');
                $seeds = 'Migrations\\seeds';
                $Seeds = new $seeds();
                $Seeds->sow();
            break;
            case 'reset':
                while(null !== ($obj = array_shift($this->_migrations))) {
                    $obj->down();
                    $obj->up();
                }
            break;
            default:
                while(null !== ($obj = array_shift($this->_migrations))) {
                    $obj->{$args[0]}();
                }
            break;
        }
        $this->showMessage("✔ Migrations completed.\n");
    }

    public function help(): string {
        return "migration Runs database migrations";
    }
}
