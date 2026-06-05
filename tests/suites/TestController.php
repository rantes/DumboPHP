<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;

/**
 * Verifies controller dispatch through _runAction: lazy model load, action
 * resolution, params, missing-action handling and noTemplate/JSON responses.
 */
class TestController extends dumboTests {

    public function beforeEach(): void {
        $this->_migrateTables(['users']);
    }

    public function lazyLoadTest(): void {
        // Controller::__get instantiates the model on first access.
        $this->assertTrue($this->User instanceof \App\Models\User);
    }

    public function actionExistsTest(): void {
        // The action ran if the property it sets is present on the page.
        $page = $this->_runAction('test/index');
        $this->assertEquals('hello', $page->message);
    }

    public function actionNotFoundTest(): void {
        $page = $this->_runAction('test/nonexistent');
        $this->assertTrue(str_contains($page->_rawOutput, 'Missing'));
    }

    public function paramsTest(): void {
        $page = $this->_runAction('test/param/42');
        $this->assertEquals('42', $page->received);
    }

    public function noTemplateTest(): void {
        $page = $this->_runAction('test/json');
        $this->assertTrue(str_contains($page->_rawOutput, '"ok"'));
    }

    /**
     * Verifies the view/layout rendering pipeline. Uses a dedicated action whose
     * view is rendered by no other test, so the framework's include_once view
     * loading produces output exactly here.
     */
    public function rawOutputRendersViewTest(): void {
        $page = $this->_runAction('test/render');
        $this->assertTrue(str_contains($page->_rawOutput, 'RENDERED_OK'));
    }
}
