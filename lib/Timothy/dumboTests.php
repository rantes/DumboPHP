<?php
/**
 *
 * @author rantes <rantes.javier@gmail.com> http://rantes.info
 *
 */
class dumboTests extends Page {
    private $_failed = 0;
    private $_passed = 0;
    private $_assertions = 0;
    private $_result = '';

    public function __construct() {
        file_put_contents(INST_PATH.'tests.log', '');
        fwrite(STDOUT, 'The very things that hold you down are going to lift you up!' . "\n");
    }
    /**
     * Output for an error message
     * @param string $errorMessage
     */
    private function _showError($errorMessage) {
        return fwrite(STDERR, "\n{$errorMessage}\n");
    }
    /**
     * Output for a standard message
     * @param string $errorMessage
     */
    private function _showMessage($message) {
        return fwrite(STDOUT, "\n{$message}\n");
    }
    /**
     * Displays the progress of each test: P - passed. F - failed.
     * @param boolean $passed
     */
    private function _progress($passed) {
        $color = $passed ? 'green' : 'red';
        $text = $passed ? 'P' : 'F';

        fwrite(STDOUT, $text);
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
    public function assertEquals($param1, $param2, $message = false) {
        $message || ($message = 'Assert if <' . gettype($param1) . '> ' . $param1 . ' is equals to <' . gettype($param2) . '> ' . $param2);
        $passed = $param1 === $param2;
        $this->_passed += $passed;
        $this->_log($message. ': '.($passed ? 'Passed.' : 'Failed'));

        $this->_progress($passed);

        $passed or $this->_triggerError('Asserts Equals');
    }

    public function assertTrue($param, $message = false) {
        $message || ($message = 'Assert if <' . gettype($param) . '> ' . $param . ' is true ');
        $passed = $param === true;
        $this->_passed += $passed;
        $this->_log($message. ': '.($passed ? 'Passed.' : 'Failed'));
        $this->_progress($passed);
        !$passed && $this->_log('Expectig `true` but found <' . gettype($param) . '> ' . $param) && $this->_triggerError('Asserts True');
    }

    /**
     * Asserts if the array with field names provides fits the fields on the model
     * @param ActiveRecord $model
     * @param array $fields
     */
    public function assertHasFields(ActiveRecord $model, $message = '') {
        $table = $model->_TableName();
        require_once INST_PATH."migrations/create_{$table}.php";
        $migrationName = 'Create'.Camelize(Singulars($table));
        $migration = new $migrationName();
        $fields = $migration->getFields();
        $expected = $model->getRawFields();
        $passed = !empty($fields) & !empty($expected) & empty(array_diff($fields, $expected));

        $this->_passed += $passed;
        $this->_log('Assert if ' . get_class($model) . ' has the fields ' . implode(',',$fields) . ': '.($passed ? 'Passed.' : 'Failed'));

        !$passed && $this->_log('Missing fields: '.implode(',', array_diff($fields, $expected)));

        $this->_progress($passed);

        $passed or $this->_triggerError('Asserts Has Fields');
    }
    /**
     * Asserts if the array with field names provides fits the fields on the model
     * @param ActiveRecord $model
     * @param array $fields
     */
    public function assertHasFieldTypes(ActiveRecord $model, $message = '') {
        $table = $model->_TableName();
        require_once INST_PATH."migrations/create_{$table}.php";
        $migrationName = 'Create'.Camelize(Singulars($table));
        $migration = new $migrationName();
        $fields = $migration->getDefinitions();
        $expected = $GLOBALS['driver']->getColumns($table);
        foreach ($GLOBALS['driver']->getColumns($table) as $i => $field) {
            if (strcmp($field['Field'], $fields[$i]['field']) === 0) {
                $migrationType = explode(' ', $fields[$i]['type']);
                $migrationType = $migrationType[0];
                $passed = strcmp($migrationType, $field['Type']) === 0;
                $this->_log('Assert if `' . $field['Field'] . '` is the same as defined at migration: ' . $migrationType. ': '.($passed ? 'Passed.' : 'Failed'));
                !$passed && $this->_log("Field `{$fields[$i]['field']}` is not synced with database. Expected: {$migrationType}, found: {$field['Type']}");
            } else {
                $passed = false;
                $this->_log('Assert if `' . $field['Field'] . '` is in the same order as defined at migration: Failed');
                !$passed && $this->_log("Field `{$fields[$i]['field']}` is not synced with database. Expected: {$field['Field']}, found: {$fields[$i]['field']}");
            }

            $this->_passed += $passed;
            $this->_progress($passed);
            $passed or $this->_triggerError('Asserts Has FieldTypes');
        }
    }
    public function describe($message) {
        if (!is_string($message)) {
            throw new Exception('The message for the description must be string.');
        }

        $this->_log($message);
    }
    /**
     * What supposed to do whe the script ends.
     */
    public function __destruct() {
        $result = $this->_failed? 'TESTS FAILED!' : 'TESTS PASSED!';
        fwrite(STDOUT, file_get_contents(INST_PATH.'tests.log'));
        fwrite(STDOUT, 'Tests Success: ' . $this->_passed . "\n");
        fwrite(STDOUT, 'Tests failed: ' . $this->_failed . "\n");
        ($this->_failed and $this->_showError($result)) or $this->_showMessage($result);
        exit($result);
    }
}
?>
