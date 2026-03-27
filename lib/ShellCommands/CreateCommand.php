<?php
namespace DumboPHP\lib\ShellCommands;

use Exception;

class CreateCommand extends BaseShell implements Interfaces\DumboCommand {

    private string $systemFolder = '';
    private string $dumboSource = 'dumbophp/src';
    private string $dumboSystemPath = 'dumbophp';
    private string $dumboLibs = 'dumbophp/lib';
    private string $binPath = '/usr/bin';
    private string $fullPathTarget = '';
    public string $dumboBin = '';
    public string $projectName = '';

    public function __construct() {
        parent::__construct();
        $this->systemFolder = INST_PATH;

        $this->dumboSource = $this->systemFolder . 'src';
        $this->dumboSystemPath = $this->systemFolder;
        $this->dumboLibs = $this->systemFolder . 'lib';
        $this->dumboBin = $this->systemFolder . 'bin';
    }

    public function execute(array $args, array $options): void {
        if (empty($args[0])):
            throw new Exception('Project name required');
        endif;
        $this->projectName = $args[0];
        if (file_exists($this->projectName)):
            throw new Exception("Folder {$this->projectName} already exists");
        endif;
        mkdir($this->projectName);
        $this->_copyInstallFiles();
        $this->showMessage("✔ Project '{$this->projectName}' created\n");
    }

    public function help(): string {
        return "create <project-name>   Creates a new site";
    }

    private function _copyInstallFiles() {
        $target = $this->projectName;
        $d = dir($target);
        $this->fullPathTarget = realpath($d->path);
        $d->close();

        $actions = [
            'Creating directory: '.$this->fullPathTarget.'/app' =>'/app',
            'Creating directory: '.$this->fullPathTarget.'/app/controllers' => '/app/controllers',
            'Creating directory: '.$this->fullPathTarget.'/app/helpers' =>'/app/helpers',
            'Creating directory: '.$this->fullPathTarget.'/app/models' =>'/app/models',
            'Creating directory: '.$this->fullPathTarget.'/app/views' =>'/app/views',
            'Creating directory: '.$this->fullPathTarget.'/app/webroot' =>'/app/webroot',
            'Creating directory: '.$this->fullPathTarget.'/app/webroot/css' =>'/app/webroot/css',
            'Creating directory: '.$this->fullPathTarget.'/app/webroot/fonts' =>'/app/webroot/fonts',
            'Creating directory: '.$this->fullPathTarget.'/app/webroot/images' =>'/app/webroot/images',
            'Creating directory: '.$this->fullPathTarget.'/app/webroot/js' =>'/app/webroot/js',
            'Creating directory: '.$this->fullPathTarget.'/app/webroot/plugins' =>'/app/webroot/plugins',
            'Creating directory: '.$this->fullPathTarget.'/config' =>'/config',
            'Creating directory: '.$this->fullPathTarget.'/migrations' =>'/migrations'
        ];

        foreach($actions as $copy => $action) {
            $this->showMessage('Running task: '.$copy);
            if (!mkdir($this->fullPathTarget.$action)) {
                $this->showError('Error on building: Cannot write on destination folder. Exiting.');
            };
        }

        $actions = [
            'Creating file system: Main .htaccess' => [$this->dumboSource.'/main.htaccess', $this->fullPathTarget.'/.htaccess'],
            'Creating file system: Main .env.example' => [$this->dumboSource.'/main.env.example', $this->fullPathTarget.'/.env.example'],
            'Creating file system: Webroot .htaccess' => [$this->dumboSource.'/webroot.htaccess', $this->fullPathTarget.'/app/webroot/.htaccess'],
            'Creating file system: favicon' => [$this->dumboSource.'/favicon.ico', $this->fullPathTarget.'/app/webroot/favicon.ico'],
            'Creating file system: config/db' => [$this->dumboSource.'/db_settings.php', $this->fullPathTarget.'/config/db_settings.php'],
            'Creating file system: config/host' => [$this->dumboSource.'/host.php', $this->fullPathTarget.'/config/host.php'],
            'Creating file system: config/index' => [$this->dumboSource.'/index.php', $this->fullPathTarget.'/app/webroot/index.php'],
            'Creating file system: layout' => [$this->dumboSource.'/layout.phtml', $this->fullPathTarget.'/app/views/layout.phtml']
        ];

        reset($actions);

        foreach($actions as $copy => $action){
            $this->showMessage('Running task: '.$copy);

            if (!copy($action[0], $action[1])) {
                $this->showError('Error on building: Cannot write on destination folder. Exiting.');
            }
        }

        if(!empty($this->options['standalone'])) {
            $this->showMessage('Building standalone site.');
            if (copy($this->dumboSource.'/dumbophp.php',$this->fullPathTarget.'/dumbophp.php')) {
                $this->showMessage('✔ Standalone file created');
            } else {
                $this->showError('Error on building: Cannot write on destination folder. Exiting.');
            }
        }
    }
}