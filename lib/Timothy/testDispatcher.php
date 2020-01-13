<?php
class testDispatcher {
    private $_testsPath = 'tests/';
    /**
     *
     * @param array $tests
     */
    function __construct(array $tests) {
        defined('INST_PATH') or define('INST_PATH', './');
        file_put_contents(INST_PATH.'tests.log', '');
        fwrite(STDOUT, 'The very things that hold you down are going to lift you up!' . "\n");

        foreach ($tests as $test):
            include_once $this->_testsPath.$test.'.php';
            $this->{$test} = new $test();
        endforeach;
    }

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
        endforeach;
        $this->{$test}->_summary();
        $this->{$test}->_end_();
    }

    public function __destruct() {
        fwrite(STDOUT, "\n");
        fwrite(STDOUT, file_get_contents(INST_PATH.'tests.log'));
    }
}