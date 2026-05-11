<?php
namespace DumboPHP\lib\ShellCommands;

use \Exception;
use function DumboPHP\Camelize;
use function DumboPHP\Singulars;


class DestroyCommand extends BaseShell implements Interfaces\DumboCommand {
    public array $params = [];
    private ?array $_migrations = [];

    public function execute(array $args, array $options): void {
        $this->showMessage("Destroying...\n");

        if (empty($args[0]) or empty($args[1])) {
            throw new Exception('Error: Missing params.');
        }

        if (sizeof($args) > 2) {
            throw new Exception('Error: Only one model for delete at once.');
        }

        file_exists('./config/host.php') or throw new Exception('Destroy actions must be executed at the top level of project path.');

        require_once './config/host.php';

        // empty($this->_options['env']['value']) || ($GLOBALS['env'] = $this->_options['env']['value']);

        switch ($args[0]) {
        case 'scaffold':
            $this->showNotice('Deleting scaffold for "' . $args[1] . '".');
            $singular   = singulars($args[1]);
            $model      = INST_PATH . "app/models/{$singular}.php";
            $migration  = INST_PATH . "migrations/create_{$args[1]}.php";
            $controller = INST_PATH . "app/controllers/{$singular}_controller.php";
            $views      = INST_PATH . "app/views/{$singular}";

            $this->showNotice("Deleting model: {$model}");
            is_file($model) && unlink($model);

            $this->showNotice("Deleting migration: {$args[1]}");
            is_file($migration) && unlink($migration);

            $this->showNotice("Deleting controller: {$controller}");
            is_file($controller) && unlink($controller);

            if (is_dir($views)):
                $dir = opendir($views);
                while ($file = readdir($dir)):
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
            $model     = INST_PATH . 'app/models/' . singulars($args[1]) . '.php';
            $migration = INST_PATH . 'migrations/create_' . $args[1] . '.php';
            $this->showNotice('Deleting model: "' . $model . '".');
            file_exists($model) or die($this->showError('Model file does not exists.' . PHP_EOL));
            unlink($model);
            $this->showNotice('Deleting migration: "' . $migration . '".');
            file_exists($migration) or die($this->showError('Migration file does not exists.' . PHP_EOL));
            unlink($migration);
            break;
        /**
             * @todo script for remove controller and views
             */
        case 'controller':
            $controller = INST_PATH . 'app/controllers/' . $args[1] . '_controller.php';
            $this->showNotice('Deleting controller: "' . $controller . '".');
            file_exists($controller) or throw new Exception('controller file does not exists.');
            unlink($controller);
            break;
        default:
            $this->_helpCommand->execute([], []);
            $this->showError('Option no valid for generate.');
            die();
            break;
        }

        $this->showMessage("✔ Destruction completed.\n");
    }

    public function help(): string {
        return "Destroys existing files";
    }
}
