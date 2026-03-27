<?php
namespace DumboPHP\lib\ShellCommands;

class HelpCommand extends BaseShell implements Interfaces\DumboCommand {

    public function execute(array $args, array $options): void {
        $output = $this->help();
        $this->showPlain($output);
    }

    public function help(): string {
        $text = <<<DUMBO
▓█████▄  █    ██  ███▄ ▄███▓ ▄▄▄▄    ▒█████
▒██▀ ██▌ ██  ▓██▒▓██▒▀█▀ ██▒▓█████▄ ▒██▒  ██▒
░██   █▌▓██  ▒██░▓██    ▓██░▒██▒ ▄██▒██░  ██▒
░▓█▄   ▌▓▓█  ░██░▒██    ▒██ ▒██░█▀  ▒██   ██░
░▒████▓ ▒▒█████▓ ▒██▒   ░██▒░▓█  ▀█▓░ ████▓▒░
 ▒▒▓  ▒ ░▒▓▒ ▒ ▒ ░ ▒░   ░  ░░▒▓███▀▒░ ▒░▒░▒░
 ░ ▒  ▒ ░░▒░ ░ ░ ░  ░      ░▒░▒   ░   ░ ▒ ▒░
 ░ ░  ░  ░░░ ░ ░ ░      ░    ░    ░ ░ ░ ░ ▒
   ░       ░            ░    ░          ░ ░
 ░                                ░

DumboPHP 2.0 by Rantes
DumboPHP shell.
Ussage:

    dumbo <command> <option> <params>

Commands:

    create <project-name>
        Creates a new site. Param: site name.

    init [--standalone=[true|false]]
        Initializes the project to use DumboPHP.

    generate [scaffold|controller|model|seed] <name>
        Generates scripts for model, controller or scaffold.

    destroy [scaffold|model] <name>
        Generates scripts for model, controller or scaffold.

    migration [up|down|reset|run|sow] <migration>
        Executes migrations actions.

    db [dump|load] <all|model>
        Actions for database.

    run [controller/action] [<param=val> <paramn=valn>]
        executes a controller/action. index/index as default.

Options:

    --env=enviroment        Sets a particular enviroment for the execution
    --halt=[true|false]     Halt the script on error
    --watch                 Set a daemon to watch files (used in tests)

DUMBO;
        return $text;
    }
}