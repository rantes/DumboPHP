<?php
class dumboTests {
    private $_failed = 0;
    private $_passed = 0;
    private $_assertions = 0;
    private $_described = false;

    public function __construct() {
        $this->colors = new Colors();
        fwrite(STDOUT, 'The very things that hold you down are going to lift you up!' . "\n");
    }
    private function _showError($errorMessage) {
        fwrite(STDOUT, "\n". $this->colors->getColoredString($errorMessage, "white", "red") . "\n");
    }

    private function _showMessage($errorMessage) {
        fwrite(STDOUT, "\n". $this->colors->getColoredString($errorMessage, "white", "green") . "\n");
    }
    /**
     * Sets a description to show when runs the unit test
     * @param string $message
     */
    private function description(string $message) {
        empty($message) and $this->_described = true;
        $this->_showMessage($message);
    }
    /**
     * Compare two params and asserts if are equals
     * @param any $param1
     * @param any $param2
     */
    public function assertEquals($param1, $param2) {
        if ($param1 === $param2):
            $this->_passed++;
            $this->_showMessage('Tests Success. ');
        else:
            $this->_failed++;
            $this->_showError('Tests Failed. ');
        endif;
    }

    public function __destruct() {
        fwrite(STDOUT, 'Tests Success: ' . $this->_passed . "\n");
        fwrite(STDOUT, 'Tests failed: ' . $this->_failed . "\n");
    }
}
?>