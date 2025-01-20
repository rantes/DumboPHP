<?php
/**
 *
 * @author rantes <rantes.javier@gmail.com> https://latuteca.com
 *
 */
class dumboTests extends Page {
    public $assertions = 0;
    public $testName = '';
    public $_failed = 0;
    public $_passed = 0;
    private $_colors = null;
    private $_colorsPalete = ['red', 'green'];
    private $_textOutputs = ['Failed', 'Passed'];
    private $_logFile = '';
    private $_data = [
        'filename' => '',
        'filepathname' => ''
    ];
    private $_actionContent = null;

    public function __construct($logFile = INST_PATH.'tmp/dumbotests.log') {
        parent::__construct();
        ($GLOBALS['env'] === 'test') || ($GLOBALS['env'] = 'test');
        $this->_logFile = $logFile;
        is_dir(INST_PATH.'tmp') or mkdir(INST_PATH.'tmp', 0775);
        $this->_colors = new DumboShellColors();
        $this->testName = get_class($this);
        $this->_data['filename'] = __FILE__;
        $this->_data['filepathname'] = __FILE__;
    }

    public function _init_() {}

    public function _end_() {}

    public function _set_data_(array $data) {
        !empty($data['filename']) and ($this->_data['filename'] = $data['filename']);
        !empty($data['filepathname']) and ($this->_data['filepathname'] = $data['filepathname']);
    }

    public function _get_data() {
        return $this->_data;
    }

    public function _getActionContent() {
        return $this->_actionContent;
    }

    public function _registerContent($content) {
        $this->_actionContent = $content;
    }

    public function _runAction(string $action) {
        $_GET = [];
        $action = explode('?', $action);
        $_GET['url'] = $action[0];
        if(!empty($action[1])):
            $params = explode('&', $action[1]);
            while(null !== ($param = array_shift($params))):
                $param = explode('=', $param);
                $_GET[$param[0]] = $param[1];
            endwhile;
        endif;
        session_status() === PHP_SESSION_DISABLED and ($_SESSION = []);
        isset($_SESSION) or (session_status() === PHP_SESSION_NONE) and session_start();

        ob_start();
        $index = new index();
        $index->page->display();
        $buf = ob_get_clean();
        $index->page->_rawOutput = $buf;
        return $index->page;
    }

    /**
     * Attempts to run migration reset action over the given tables
     * for model testing purposes
     * @param array $tables
     */
    public function _migrateTables($tables = []) {
        $migrationsPath = INST_PATH.'migrations/';
        foreach ($tables as $table):
            $file = "{$migrationsPath}create_{$table}.php";
            file_exists($file) or die('Migration file '.$file.', does not exists.'.PHP_EOL);
            require_once $file;
            $class = 'Create'.Camelize(Singulars($table));
            $obj = new $class();
            ob_start();
            $obj->reset();
            ob_get_clean();
        endforeach;
    }
    public function _sow() {
        require_once INST_PATH.'migrations/seeds.php';
        $seeds = new Seed();
        $seeds->sow();
    }
    private function _setConfigValue(string $key, $value) {
        $this->__sys_conf_values__[$key] = $value;
    }
    /**
     * Output for an error message
     * @param string $errorMessage
     */
    private function _showError($errorMessage) {
        fwrite(STDERR, "\n{$errorMessage}\n");
        return true;
    }
    /**
     * Output for a standard message
     * @param string $errorMessage
     */
    private function _showMessage($message) {
        fwrite(STDOUT, "\n{$message}\n");
        return true;
    }
    /**
     * Displays the progress of each test: P - passed. F - failed.
     * @param boolean $passed
     */
    private function _progress($passed) {
        $text = $passed ? 'P' : 'F';

        fwrite(STDOUT, $this->_colors->getColoredString($text, $this->_colorsPalete[$passed]));
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
        fwrite(STDOUT, "\n{$output}\n");
        return true;
    }

    /**
     * Compare two params and asserts if are equals
     * @param $param1
     * @param $param2
     */
    public function assertEquals($param1, $param2, $message = null) {
        $this->assertions++;
        $message = $message ?? 'Assert if <' . gettype($param1) . '> ' . var_export($param1, true) . ' is equals to <' . gettype($param2) . '> ' . var_export($param2, true);
        $passed = $param1 === $param2;
        $this->_passed += $passed;
        $this->_progress($passed);
        $this->_log($message. ': '.$this->_colors->getColoredString($this->_textOutputs[$passed], $this->_colorsPalete[$passed]));

        $passed or $this->_triggerError("Assert {$param1} is equals to {$param2}");
    }
    /**
     * Asserts if the Value is true.
     * @param $value
     * @param string $message
     */
    public function assertTrue($value, $message = null) {
        $this->assertions++;
        $message = $message ?? 'Assert if <' . gettype($value) . '> ' . $value . ' is true ';
        $passed = $value === (boolean)true;
        $this->_passed += $passed;
        $this->_log($message. ': '.$this->_colors->getColoredString($this->_textOutputs[$passed], $this->_colorsPalete[$passed]));
        $this->_progress($passed);
        $trueFalse = ['False','True'];
        !$passed && $this->_log('Expectig `true` but found <' . gettype($value) . '> ' . (is_bool($value) ? $trueFalse[$value] : $value)) && $this->_triggerError('Asserts True');
    }
    /**
     * Asserts if the Value is false.
     * @param $value
     * @param string $message
     */
    public function assertFalse($value, $message = null) {
        $this->assertions++;
        $message = $message ?? 'Assert if <' . gettype($value) . '> ' . $value . ' is false ';
        $passed = $value === (boolean)false;
        $this->_passed += $passed;
        $this->_log($message. ': '.$this->_colors->getColoredString($this->_textOutputs[$passed], $this->_colorsPalete[$passed]));
        $this->_progress($passed);
        $trueFalse = ['False','True'];
        !$passed && $this->_log('Expectig `false` but found <' . gettype($value) . '> ' . (is_bool($value) ? $trueFalse[$value] : $value)) && $this->_triggerError('Asserts False');
    }

    /**
     * Asserts if the array with field names provides fits the fields on the model
     * @param ActiveRecord $model
     * @param array $fields
     */
    public function assertHasFields(ActiveRecord $model) {
        $this->assertions++;
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
        $this->assertions++;
        $table = $model->_TableName();
        require_once INST_PATH."migrations/create_{$table}.php";
        $migrationName = 'Create'.Camelize(Singulars($table));
        $migration = new $migrationName();
        $fields = $migration->getDefinitions();
        $tblDefinitions = $GLOBALS['Connection']->getColumnFields($GLOBALS['driver']->getColumns($table));

        foreach ($tblDefinitions as $i => $field) {
            if (strcmp($field['Field'], $fields[$i]['field']) === 0):
                $migrationType = explode(' ', $fields[$i]['type']);
                $migrationType = $migrationType[0];
                $passed = strcmp($migrationType, $field['Type']) === 0;
                $color = $passed ? 'green' : 'red';
                $text = $passed ? 'Passed.' : 'Failed';
                $this->_log("{$table}: Assert if `{$field['Field']}` is the same as defined at migration: {$migrationType}: ".$this->_colors->getColoredString($this->_textOutputs[$passed], $this->_colorsPalete[$passed]));
                !$passed && $this->_log("Field `{$fields[$i]['field']}` in {$table} is not synced with database. Expected: {$migrationType}, found: {$field['Type']}");
            else:
                $passed = false;
                $this->_log("{$table}: Assert if `{$field['Field']}` is the same as defined at migration: ".$this->_colors->getColoredString('Failed', 'red'));
                !$passed && $this->_log("Field `{$fields[$i]['field']}` in {$table} is not synced with database. Expected: {$field['Field']}, found: {$fields[$i]['field']}");
            endif;

            $this->_passed += $passed;
            $this->_progress($passed);
            $passed or $this->_triggerError('Asserts Has FieldTypes');
        }
    }
    /**
     * Add description for a set of tests
     *
     * @param string $message
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
    /**
     * Will exit and return an integer code to the console
     * if no failed any test, the return code will be 0.
     */
    public function __destruct() {
        if ($this->_failed):
            exit((int)$this->_failed);
        endif;
    }
    /**
     * Will performs any action before each test
     * @return void
     */
    public function beforeEach() {}

    /**
     * Redefines a method to set an spy
     * @todo Implements object spy
     * @param Page $controller
     * @param string $method
     * @return void
     */
    public function spyOn(Page $controller, $method) {
       /**NOOP */
    }
    /**
     * Undocumented function
     *
     * @param string $method
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
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = []) {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->isPublic() or $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
    /**
     * Set a sysConfig value
     * 
     * @param object &$object    Instantiated object that sysConf will change.
     * @param string $property   SysConf property to change
     * @param mixed  $value      Value to set
     */
    public function setSysconfigValue(&$object, $property, $value) {
        $current = [];
        $reflection = new ReflectionObject($object);
        $confs = $reflection->getProperty('__sys_conf_values__');
        $confs->setAccessible(true);
        $current = $confs->getValue($object);
        $current[$property] = $value;
        $confs->setValue($object, $current);
        return true;
    }
}
?>
