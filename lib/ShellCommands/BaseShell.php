<?php
namespace DumboPHP\lib\ShellCommands;

use DumboPHP\lib\DumboShellColors;

class BaseShell {
    public ?DumboShellColors $_colors = null;

    public function __construct() {
        $this->_colors = new DumboShellColors();
    }

    private function help() {
        return '';
    }

    public function showError($message) {
        fwrite(STDOUT, $this->_colors->getColoredString($message, "white", "red") . "\n");
    }

    public function showMessage($message) {
        fwrite(STDOUT, $this->_colors->getColoredString($message, "white", "green") . "\n");
    }

    public function showNotice($message) {
        fwrite(STDOUT, $this->_colors->getColoredString($message, "blue", "yellow") . "\n");
    }

    public function showPlain($message) {
        fwrite(STDOUT, $this->_colors->getColoredString($message, "white", "black") . "\n");
    }
}