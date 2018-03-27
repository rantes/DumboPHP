<?php
require_once 'lib/Timothy/dumboTests.php';
class Test extends dumboTests {

    public function assertionsTest() {
        $this->assertEquals('0', '0');
    }
}

?>