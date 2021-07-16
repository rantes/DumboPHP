<?php
preg_match('@\/([a-z0-9]+)\.php@i', $argv[0], $matches);
$test = new $matches[1]();

$actions = [];

try {
    $methods = get_class_methods($test);
    while (null !== ($method = array_shift($methods))) {
        preg_match('/[a-zA-Z0-9]+Test/', $method, $match);
        (sizeof($match) === 1) && ($actions[] = $method);
    }
    $test = $test;
    $GLOBALS['env'] = 'test';
    $test->_init_();

    xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
    while (null !== ($action = array_shift($actions))) {
        $test->beforeEach();
        $test->{$action}();
        // $this->_failed = ($this->_failed || $test->_failed > 0);

        // if ($this->_halt && $this->_failed):
        //     exit(1);
        // endif;
    }
    // $this->assertions += $test->assertions;
    $files = [];
    $restult = xdebug_get_code_coverage();
    foreach($restult as $entry => $lines):
        if(preg_match('@dumbophp@i', $entry) !== 1):
            $files[] = ['file' => $entry, 'lines' => $lines];
            var_dump($entry);
        endif;
    endforeach;

    $test->_end_();
    $test->_summary();
} catch (Throwable $e) {
    // $this->_failed = true;
    fwrite(STDERR, (string)$e);
    exit(1);
}