<?php
/**
 *
 * @author rantes <rantes.javier@gmail.com> https://latuteca.com
 *
 */
class dumboTests extends Page {
    public $_failed = 0;
    public $_passed = 0;
    private $_colors = null;
    private $_colorsPalete = ['red', 'green'];
    private $_textOutputs = ['Failed', 'Passed'];
    private $_logFile = '/tmp/dumbotests.log';
    public $testName = '';

    public function __construct($logFile) {
        parent::__construct();
        $GLOBALS['env'] = 'test';
        $this->_logFile = $logFile;
        require_once 'lib/DumboShellColors.php';
        $this->_colors = new DumboShellColors();
        $this->testName = get_class($this);
    }
    public function _init_() {}
    public function _end_() {}
    public function _runAction(string $action) {
        $_GET['url'] = $action;
        ob_start();
        $index = new Index();
        ob_get_clean();
        return $index->page;
    }
    /**
     * Attempts to run migration reset action over the given tables
     * for model testing purposes
     * @param array $tables
     */
    public function _migrateTables($tables = []) {
        $migrationsPath = INST_PATH.'migrations/';
        foreach ($tables as $table) {
            $file = "{$migrationsPath}create_{$table}.php";
            file_exists($file) or die('Migration file '.$table.', does not exists.'.PHP_EOL);
            require_once $file;
            $class = 'Create'.Camelize(Singulars($table));
            $obj = new $class();
            ob_start();
            $obj->reset();
            ob_get_clean();
        }
    }
    /**
     * Output for an error message
     * @param string $errorMessage
     */
    private function _showError($errorMessage) {
        echo "\n{$errorMessage}\n";
        return true;
    }
    /**
     * Output for a standard message
     * @param string $errorMessage
     */
    private function _showMessage($message) {
        echo "\n{$message}\n";
        return true;
    }
    /**
     * Displays the progress of each test: P - passed. F - failed.
     * @param boolean $passed
     */
    private function _progress($passed) {
        $text = $passed ? 'P' : 'F';

        echo $this->_colors->getColoredString($text, $this->_colorsPalete[$passed]);
        return true;
    }
    /**
     * Logs the process of each test
     * @param string $text
     */
    private function _log($text) {
        $date = date('d-m-Y H:i:s');
        $message = "[{$date}]: $text \n";

        file_put_contents($this->_logFile, $message, FILE_APPEND);
        return true;
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
        echo "\n{$output}\n";
        return true;
    }

    /**
     * Compare two params and asserts if are equals
     * @param any $param1
     * @param any $param2
     */
    public function assertEquals($param1, $param2, $message = null) {
        $message = $message ?? 'Assert if <' . gettype($param1) . '> ' . var_export($param1, true) . ' is equals to <' . gettype($param2) . '> ' . var_export($param2, true);
        $passed = $param1 === $param2;
        $this->_passed += $passed;
        $this->_progress($passed);
        $this->_log($message. ': '.$this->_colors->getColoredString($this->_textOutputs[$passed], $this->_colorsPalete[$passed]));

        $passed or $this->_triggerError("Assert {$param1} is equals to {$param2}");
    }
    /**
     * Asserts if the Value is true.
     * @param any $value
     * @param string $message
     */
    public function assertTrue($value, $message = null) {
        $message = $message ?? 'Assert if <' . gettype($value) . '> ' . $value . ' is true ';
        $passed = $value === true;
        $this->_passed += $passed;
        $this->_log($message. ': '.$this->_colors->getColoredString($this->_textOutputs[$passed], $this->_colorsPalete[$passed]));
        $this->_progress($passed);
        !$passed && $this->_log('Expectig `true` but found <' . gettype($value) . '> ' . $value) && $this->_triggerError('Asserts True');
    }
    /**
     * Asserts if the Value is false.
     * @param any $value
     * @param string $message
     */
    public function assertFalse($value, $message = null) {
        $message = $message ?? 'Assert if <' . gettype($value) . '> ' . $value . ' is false ';
        $passed = $value === false;
        $this->_passed += $passed;
        $this->_log($message. ': '.$this->_colors->getColoredString($this->_textOutputs[$passed], $this->_colorsPalete[$passed]));
        $this->_progress($passed);
        !$passed && $this->_log('Expectig `false` but found <' . gettype($value) . '> ' . $value) && $this->_triggerError('Asserts False');
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
        $passed = !empty($fields) & !empty($expected) & empty(array_diff($fields, $expected));

        $this->_passed += $passed;
        $this->_log('Assert if ' . get_class($model) . ' has the fields ' . implode(',',$fields) . ': '.$this->_colors->getColoredString($this->_textOutputs[$passed], $this->_colorsPalete[$passed]));

        !$passed && $this->_log('Missing fields: '.implode(',', array_diff($fields, $expected)));

        $this->_progress($passed);

        $passed or $this->_triggerError('Asserts Has Fields');
    }
    /**
     * Asserts if the array with field names provides fits the fields on the model
     * @param ActiveRecord $model
     * @param array $fields
     */
    public function assertHasFieldTypes(ActiveRecord $model) {
        $table = $model->_TableName();
        require_once INST_PATH."migrations/create_{$table}.php";
        $migrationName = 'Create'.Camelize(Singulars($table));
        $migration = new $migrationName();
        $fields = $migration->getDefinitions();
        foreach ($GLOBALS['Connection']->getColumnFields($GLOBALS['driver']->getColumns($table)) as $i => $field) {
            if (strcmp($field['Field'], $fields[$i]['field']) === 0) {
                $migrationType = explode(' ', $fields[$i]['type']);
                $migrationType = $migrationType[0];
                $passed = strcmp($migrationType, $field['Type']) === 0;
                $color = $passed ? 'green' : 'red';
                $text = $passed ? 'Passed.' : 'Failed';
                $this->_log("{$table}: Assert if `{$field['Field']}` is the same as defined at migration: {$migrationType}: ".$this->_colors->getColoredString($this->_textOutputs[$passed], $this->_colorsPalete[$passed]));
                !$passed && $this->_log("Field `{$fields[$i]['field']}` in {$table} is not synced with database. Expected: {$migrationType}, found: {$field['Type']}");
            } else {
                $passed = false;
                $this->_log("{$table}: Assert if `{$field['Field']}` is the same as defined at migration: ".$this->_colors->getColoredString('Failed', 'red'));
                !$passed && $this->_log("Field `{$fields[$i]['field']}` in {$table} is not synced with database. Expected: {$field['Field']}, found: {$fields[$i]['field']}");
            }

            $this->_passed += $passed;
            $this->_progress($passed);
            $passed or $this->_triggerError('Asserts Has FieldTypes');
        }
    }
    /**
     * Add description for a set of tests
     *
     * @param [string] $message
     * @return void
     */
    public function describe($message) {
        if (!is_string($message)) {
            throw new Exception('The message for the description must be string.');
        }

        $this->_log($message);
    }
    /**
     * What supposed to do whe the script ends.
     */
    public function _summary() {
        $text = $this->_failed ? 'TESTS FAILED!' : 'TESTS PASSED';
        $result = $this->_colors->getColoredString($text, $this->_colorsPalete[!$this->_failed]);
        $this->_log($result);
    }

    public function __destruct() {
        // exit(0 + !!$this->_failed);
    }

    public function beforeEach() {}

    /**
     * Redefines a method to set an spy
     *
     * @param Page $controller
     * @param string $method
     * @param Closure $closure
     * @return void
     */
    public function spyOn(Page $controller, $method) {
       /**NOOP */
    }
    /**
     * Undocumented function
     *
     * @param [type] $method
     * @param string $message
     * @return void
     */
    public function assertMethodHasBeenCalled($method, $message = null) {
        // $backtrace = debug_backtrace();
        // var_dump($backtrace);

        // while (($trace = array_pop($backtrace)) != null) {
        //     $passed = $trace['function'] === $method;
        //     if ($passed) break;
        // }

        // $this->_passed += $passed;
        // $this->_log("Expecting {$method} to have been called: {$this->_colors->getColoredString($this->_textOutputs[$passed], $this->_colorsPalete[$passed])}");
        // $this->_progress($passed);
        // !$passed && $this->_log("Expecting {$method} to have been called") && $this->_triggerError('Asserts Method Have Been Called');
    }
}
?>
