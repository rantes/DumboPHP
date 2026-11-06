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

        for ($i=1; $i < sizeof($args); $i++) {
            $this->params[] = $args[$i];
        }

        if (empty($args[0]) || ($args[0] !== 'sow' && sizeof($this->params) === 0)) {
            throw new Exception('Error: Not enough arguments; the migrations to affect must be defined.');
        }

        $migrationsPath = INST_PATH.'migrations/';
        $objects = [];

        if($args[0] !== 'sow') {
            if(sizeof($this->params) === 1 and $this->params[0] === 'all') {
                $migrationsDir = dir($migrationsPath);
                while (($file = $migrationsDir->read()) != false) {
                    if($file != "." and $file != ".." and preg_match('/create_(.+)\.php/', $file, $matches) === 1) {
                        $this->showNotice('º Running action ', $args[0], ' for: ', $matches[1]);

                        $class = 'Migrations\\Create'.Camelize($matches[1]);
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
        // PERF-CACHE-ESQUEMA — todo comando que toca el esquema (todos salvo sow) deja un sello nuevo: la caché del esquema anterior
        // deja de usarse y, de ser posible, se borra. La primera ejecución crea el primer sello (desde ahí hay caché).
        $args[0] === 'sow' or ($this->_renewSchemaStamp() or $this->showError('Warning: the schema cache could not be invalidated; delete tmp/cache/schema/stamp by hand.'));
        $this->showMessage("✔ Migrations completed.\n");
    }

    private function _renewSchemaStamp(): bool {
        return DB->renewSchemaStamp();
    }

    public function help(): string {
        return "migration Runs database migrations";
    }
}
