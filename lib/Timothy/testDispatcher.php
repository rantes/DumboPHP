<?php
class testDispatcher {
    public $assertions = 0;
    public $filename = '';
    public $filepathname = '';
    public $fails = 0;
    public $tests = 0;
    public $actions = [];
    public $testAssertions = 0;
    public $testFailures = 0;
    private $_testsPath = '';
    private $_failed = false;
    private $_halt = false;
    private $_logPath = '/tmp/';
    private $_logFile = 'dumbotests.log';
    /**
     *
     * @param array $tests
     */
    function __construct(array $tests, $path = INST_PATH.'tests/', $halt = false, $logPath = 'tmp/') {
        ($GLOBALS['env'] === 'test') || ($GLOBALS['env'] = 'test');
        try {
            $this->_logPath = $logPath;
            file_put_contents("{$this->_logPath}{$this->_logFile}", '');
            fwrite(STDOUT, "The very things that hold you down are going to lift you up!\n");

            $this->_halt = $halt;
            $this->_testsPath = $path;
            while (null !== ($test = array_shift($tests))):
                $file = "{$test}.php";
                $pathname = "{$this->_testsPath}{$file}";
                require $pathname;
                $this->{$test} = new $test("{$this->_logPath}{$this->_logFile}");
            endwhile;
        } catch (Throwable $e) {
            $this->_failed = true;
            fwrite(STDERR, (string)$e);
            exit(1);
        }
    }
    /**
     * Just execute a test
     *
     * @param [string] $test
     * @return void
     */
    function run(string $test) {
        $actions = [];

        try {
            $methods = get_class_methods($this->{$test});
            $this->tests = 0;
            while (null !== ($method = array_shift($methods))):
                preg_match('/[a-zA-Z0-9]+Test/', $method, $match);
                (sizeof($match) === 1) && ($actions[] = $method) && $this->tests++;
            endwhile;

            $objtest = $this->{$test};
            $GLOBALS['env'] = 'test';
            $objtest->_init_();

            while (null !== ($action = array_shift($actions))):
                $objtest->assertions = 0;
                $start = microtime(true);
                $objtest->beforeEach();
                $objtest->{$action}();
                $took = microtime(true) - $start;
                $this->_failed = ($this->_failed || $objtest->_failed > 0);
                $this->fails += $objtest->_failed;
                $this->actions[] = [
                    'test' => $action,
                    'time' => $took,
                    'assertions' => $objtest->assertions,
                    'fails' => $objtest->_failed
                ];

                if ($this->_halt && $this->_failed):
                    exit(1);
                endif;
            endwhile;

            $this->assertions += $objtest->assertions;
            $this->filepathname = "{$this->_testsPath}{$test}.php";
            $this->testAssertions = $objtest->assertions;
            $this->testFailures = $objtest->fails;

            $objtest->_end_();
            $objtest->_summary();
        } catch (Throwable $e) {
            $this->_failed = true;
            fwrite(STDERR, (string)$e);
            exit(1);
        }
    }
    /**
     * At the end of all tests will display results and return exit code (0: ok, 1: fail)
     * If fails a test, will output the log file
     */
    public function __destruct() {
        if ($this->_failed) {
            echo "\nRESULT: FAILED\nLOG:\n\n";
            echo file_get_contents("{$this->_logPath}{$this->_logFile}");
            echo "\n\nRESULT: FAILED\n";
            exit((integer)$this->_failed);
        } else {
            echo "\nRESULT: PASS\n";
        }
    }
}