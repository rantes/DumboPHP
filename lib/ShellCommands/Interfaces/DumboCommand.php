<?php
namespace DumboPHP\lib\ShellCommands\Interfaces;

interface DumboCommand {
    public function execute(array $args, array $options): void;
    public function help(): string;
}
