<?php
class testDispatcher {
    private $_testsPath = 'tests/';
    private $_failed = false;
    private $_halt = false;
    /**
     *
     * @param array $tests
     */
    function __construct(array $tests, $halt = false) {
        defined('INST_PATH') or define('INST_PATH', './');
        file_put_contents(INST_PATH.'tests.log', '');
        fwrite(STDOUT, 'The very things that hold you down are going to lift you up!' . "\n");

        $this->_halt = $halt;

        foreach ($tests as $test):
            include_once $this->_testsPath.$test.'.php';
            $this->{$test} = new $test();
        endforeach;
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
        $methods = get_class_methods($this->{$test});
        foreach ($methods as $method):
            preg_match('/[a-zA-Z0-9]+Test/', $method, $match);
            if (sizeof($match) === 1):
                $actions[] = $method;
            endif;
        endforeach;

        $this->{$test}->_init_();
        foreach ($actions as $action):
            $this->{$test}->beforeEach();
            $this->{$test}->{$action}();
            $this->_failed = ($this->_failed || $this->{$test}->_failed > 0);
            if ($this->_halt && $this->_failed):
                exit(1);
            endif;
        endforeach;
        $this->{$test}->_end_();
        $this->{$test}->_summary();
    }
    /**
     * At the end of all tests will display results and return exit code (0: ok, 1: fail)
     * If fails a test, will output the log file
     */
    public function __destruct() {
        if ($this->_failed) {
            echo "\nRESULT: FAILED\nLOG:\n\n";
            echo file_get_contents(INST_PATH.'tests.log');
            echo "\n\nRESULT: FAILED\n";
        } else {
            echo "\nRESULT: PASS\n";
        }
        exit((integer)$this->_failed);
    }
}