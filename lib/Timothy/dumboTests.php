<?php
/**
 *
 * @author rantes <rantes.javier@gmail.com> http://rantes.info
 *
 */
class dumboTests extends Page{
    private $_failed = 0;
    private $_passed = 0;
    private $_assertions = 0;
    private $_result = '';

    public function __construct() {
        $this->colors = new Colors();
        file_exists(INST_PATH.'tests.log') and unlink('tests.log');
        fwrite(STDOUT, 'The very things that hold you down are going to lift you up!' . "\n");
    }
    /**
     * Output for an error message
     * @param string $errorMessage
     */
    private function _showError($errorMessage) {
        return fwrite(STDOUT, "\n". $this->colors->getColoredString($errorMessage, 'white', 'red') . "\n");
    }
    /**
     * Output for a standard message
     * @param string $errorMessage
     */
    private function _showMessage($errorMessage) {
        return fwrite(STDOUT, "\n". $this->colors->getColoredString($errorMessage, 'white', 'green') . "\n");
    }
    /**
     * Displays the progress of each test: P - passed. F - failed.
     * @param boolean $passed
     */
    private function _progress($passed) {
        $color = $passed ? 'green' : 'red';
        $text = $passed ? 'P' : 'F';

        fwrite(STDOUT, $this->colors->getColoredString($text, 'white', $color));
    }
    /**
     * Logs the process of each test
     * @param unknown $text
     */
    private function _log($text) {
        $date = date('d-m-Y H:i:s');
        $message = "[{$date}]: $text \n";

        file_put_contents(INST_PATH.'tests.log', $message, FILE_APPEND);
    }
    /**
     * Handle error for a test
     * @param string $additional
     */
    private function _triggerError($additional) {
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
        $this->_log('Assert if <' . gettype($param1) . '> ' . $param1 . ' is equals to <' . gettype($param2) . '> ' . $param2 . ': '.($passed ? 'Passed.' : 'Failed'));

        $this->_progress($passed);

        $passed or $this->_triggerError('Asserts Equals');
    }

    /**
     * Asserts if the array with field names provides fits the fields on the model
     * @param ActiveRecord $model
     * @param array $fields
     */
    public function assertHasFields(ActiveRecord $model) {
        $table = $model->_TableName();
        require_once INST_PATH."migrations/create_{$table}.php";
        $migrationName = 'Create'.Camelize(Singulars($table));
        $migration = new $migrationName();
        $fields = $migration->getFields();
        $expected = $model->getRawFields();
        $passed = !empty($fields) and !empty($expected) and empty(array_diff($fields, $expected));

        $this->_passed += $passed;
        $this->_log('Assert if ' . get_class($model) . ' has the fields ' . implode(',',$fields) . ': '.($passed ? 'Passed.' : 'Failed'));

        $this->_progress($passed);

        $passed or $this->_triggerError('Asserts Has Fields');
    }
    /**
     * What supposed to do whe the script ends.
     */
    public function __destruct() {
        ($this->_failed and $this->_showError('Test failed.')) or $this->_showMessage('Test Passed.');
        fwrite(STDOUT, 'Tests Success: ' . $this->_passed . "\n");
        fwrite(STDOUT, 'Tests failed: ' . $this->_failed . "\n");
        fwrite(STDOUT, file_get_contents(INST_PATH.'tests.log'));
    }
}
?>