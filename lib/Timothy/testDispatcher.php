<?php
class testDispatcher {
    private $_testsPath = '';
    private $_failed = false;
    private $_halt = false;
    private $_logPath = '/tmp/';
    private $_logFile = 'dumbotests.log';
    /**
     *
     * @param array $tests
     */
    function __construct(array $tests, $path = 'tests/', $halt = false, $logPath = '/tmp/') {
        $this->_logPath = $logPath;
        file_put_contents("{$this->_logPath}{$this->_logFile}", '');
        echo 'The very things that hold you down are going to lift you up!', "\n";

        $this->_halt = $halt;
        $this->_testsPath = $path;

        while (null !== ($test = array_shift($tests))) {
            include_once $this->_testsPath.$test.'.php';
            $this->{$test} = new $test("{$this->_logPath}{$this->_logFile}");
        }
    }
    /**
     * Just execute a test
     *
     * @param [string] $test
     * @return void
     */
    function run($test) {
        $test = (string) $test;
        $actions = [];

        try {
            $methods = get_class_methods($this->{$test});
            while (null !== ($method = array_shift($methods))) {
                preg_match('/[a-zA-Z0-9]+Test/', $method, $match);
                (sizeof($match) === 1) && ($actions[] = $method);
            }
            $test = $this->{$test};
            $GLOBALS['env'] = 'test';
            $test->_init_();

            while (null !== ($action = array_shift($actions))) {
                $test->beforeEach();
                $test->{$action}();
                $this->_failed = ($this->_failed || $test->_failed > 0);
                if ($this->_halt && $this->_failed):
                    exit(1);
                endif;
            }

            $test->_end_();
            $test->_summary();
        } catch (Throwable $e) {
            $this->_failed = true;
            var_dump($e->getMessage());
            var_dump($e->getTrace());
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
        } else {
            echo "\nRESULT: PASS\n";
        }
        exit((integer)$this->_failed);
    }
}