<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;

/**
 * Verifies URL -> controller/action/param routing performed by the index front
 * controller (via _runAction).
 */
class TestRouting extends dumboTests {

    public function defaultRouteTest(): void {
        // '/' falls back to DEF_CONTROLLER/DEF_ACTION (test/index); the action
        // ran if its property is set on the resolved page.
        $page = $this->_runAction('/');
        $this->assertEquals('hello', $page->message);
    }

    public function controllerActionRouteTest(): void {
        $page = $this->_runAction('test/index');
        $this->assertEquals('hello', $page->message);
    }

    public function paramRouteTest(): void {
        $page = $this->_runAction('test/param/99');
        $this->assertEquals('99', $page->received);
    }
}
