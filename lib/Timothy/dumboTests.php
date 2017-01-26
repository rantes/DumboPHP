<?php
class dumboTests {
    private $_failed = 0;
    private $_passed = 0;
    private $_assertions = 0;
    private $_described = false;
    private $_result = '';

    public function __construct() {
        $this->colors = new Colors();
        file_exists('tests.log') and unlink('tests.log');
        fwrite(STDOUT, 'The very things that hold you down are going to lift you up!' . "\n");
    }
    /**
     * Output for an error message
     * @param string $errorMessage
     */
    private function _showError(string $errorMessage) {
        return fwrite(STDOUT, "\n". $this->colors->getColoredString($errorMessage, 'white', 'red') . "\n");
    }
    /**
     * Output for a standard message
     * @param string $errorMessage
     */
    private function _showMessage(string $errorMessage) {
        return fwrite(STDOUT, "\n". $this->colors->getColoredString($errorMessage, 'white', 'green') . "\n");
    }
    /**
     * Displays the progress of each test: P - passed. F - failed.
     * @param boolean $passed
     */
    private function _progress(bool $passed) {
        $color = $passed ? 'green' : 'red';
        $text = $passed ? 'P' : 'F';

        fwrite(STDOUT, $this->colors->getColoredString($text, 'white', $color));
    }
    /**
     * Sets a description to show when runs the unit test
     * @param string $message
     */
    private function description(string $message) {
        empty($message) and $this->_described = true;
        $this->_showMessage($message);
    }
    private function _log($text) {
        $date = date('d-m-Y H:i:s');
        $message = "[{$date}]: $text \n";

        file_put_contents('tests.log', $message, FILE_APPEND);
    }
    private function _triggerError(string $additional) {
        $track = debug_backtrace();
        $this->_failed++;

        $output = <<<DUMBO
ERROR Failed to {$additional}, on {$track[1]['file']} at line {$track[1]['line']}.
DUMBO;
        $this->_log($output);
    }
    /**
     * Compare two params and asserts if are equals
     * @param any $param1
     * @param any $param2
     */
    public function assertEquals($param1, $param2) {
        $passed = $param1 === $param2;
        $this->_passed += $passed;
        $this->_log('Assert if ' . gettype($param1) . ' is equals to ' . gettype($param2) . ': '.($passed ? 'Passed.' : 'Failed'));

        $this->_progress($passed);

        $passed or $this->_triggerError('Asserts Equals');
    }

    public function __destruct() {
        ($this->_failed and $this->_showError('Test failed.')) or $this->_showMessage('Test Passed.');
        fwrite(STDOUT, 'Tests Success: ' . $this->_passed . "\n");
        fwrite(STDOUT, 'Tests failed: ' . $this->_failed . "\n");
        fwrite(STDOUT, file_get_contents('tests.log'));
    }
}
?>