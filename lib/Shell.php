<?php
namespace DumboPHP\lib;

use Exception;
use DumboPHP\Config;
use DumboPHP\Connection;
use DumboPHP\lib\ShellCommands\Interfaces\DumboCommand;
use DumboPHP\lib\ShellCommands\BaseShell;
use DumboPHP\lib\ShellCommands\CreateCommand;
use DumboPHP\lib\ShellCommands\HelpCommand;
use DumboPHP\lib\ShellCommands\MigrationCommand;
use DumboPHP\lib\ShellCommands\AutocompleteCommand;
use DumboPHP\lib\ShellCommands\GenerateCommand;
use DumboPHP\lib\ShellCommands\DestroyCommand;
use DumboPHP\lib\ShellCommands\DbCommand;
use DumboPHP\lib\ShellCommands\RunCommand;

class Shell extends BaseShell {

    private array $_options = [
        'env' => ['value' => null, 'cast' => 'string'],
        'halt' => ['value' => false, 'cast' => 'boolean'],
        'standalone' => ['value' => true, 'cast' => 'boolean'],
        'dir' => ['value' => null, 'cast' => 'string'],
        'watch' => ['value' => false, 'cast' => 'boolean'],
        'help' => ['value' => false, 'cast' => 'boolean']
    ];
    private ?string $command = null;

    private array $arguments = [];
    private array $params = [];

    private ?DumboCommand $_createCommand = null;
    private ?DumboCommand $_helpCommand = null;
    private ?DumboCommand $_migrationCommand = null;
    private ?DumboCommand $_autocompleteCommand = null;
    private ?DumboCommand $_generateCommand = null;
    private ?DumboCommand $_destroyCommand = null;
    private ?DumboCommand $_dbCommand = null;
    private ?DumboCommand $_runCommand = null;

    public function __construct() {
        parent::__construct();
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
                $this->showError('No command given.');
                die();
            }

            array_shift($argv);
            $this->command = array_shift($argv);
            $this->arguments = $argv;
            $this->_parseOptions();

            if ($this->_options['help']['value']) {
                $this->_helpCommand->execute([], []);
                die();
            }

            switch($this->command) {
                case 'create':
                    $this->_createCommand = new CreateCommand();
                break;
                case 'help':
                    $this->_helpCommand = new HelpCommand();
                break;
                case 'migration':
                    defined('APP_CONFIGS') || define('APP_CONFIGS', new Config());
                    defined('DB') || define('DB', new Connection());

                    $this->_migrationCommand = new MigrationCommand();
                break;
                case 'autocomplete':
                    $this->_autocompleteCommand = new AutocompleteCommand();
                break;
                case 'generate':
                    defined('APP_CONFIGS') || define('APP_CONFIGS', new Config());
                    defined('DB') || define('DB', new Connection());
                    $this->_generateCommand = new GenerateCommand();
                break;
                case 'destroy':
                    defined('APP_CONFIGS') || define('APP_CONFIGS', new Config());
                    defined('DB') || define('DB', new Connection());
                    $this->_destroyCommand = new DestroyCommand();
                break;
                case 'db':
                    defined('APP_CONFIGS') || define('APP_CONFIGS', new Config());
                    defined('DB') || define('DB', new Connection());
                    $this->_dbCommand = new DbCommand();
                break;
                case 'run':
                    defined('APP_CONFIGS') || define('APP_CONFIGS', new Config());
                    defined('DB') || define('DB', new Connection());
                    $this->_runCommand = new RunCommand();
                break;
                default:
                    throw new Exception("Command not found: {$this->command}");
            }
            $this->{"_{$this->command}Command"}->execute($this->arguments, $this->_options);
        } catch (Exception $e) {
            $this->showError('Error: '.$e->getMessage());
            if ($this->_options['halt']['value']) {
                exit(1);
            }
        }
    }

}
