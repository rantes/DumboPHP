<?php
namespace App\Controllers;

use DumboPHP\Controller;

/**
 * Fixture controller used to exercise routing, params, lazy model load,
 * AJAX/JSON responses and redirects.
 */
class TestController extends Controller {
    public array $noTemplate = ['json', 'redirect'];

    public function indexAction(): void {
        $this->message = 'hello';
    }

    public function jsonAction(): void {
        $this->respondToAJAX(json_encode(['ok' => true]));
    }

    public function paramAction(): void {
        $this->received = $this->params['id'] ?? null;
    }

    public function redirectAction(): void {
        $this->redirect(INST_URI . 'test/index');
    }

    public function lazyAction(): void {
        // Touch the lazy-loaded model so its instance becomes a property.
        $this->users = $this->User->Find();
    }

    public function renderAction(): void {
        // Dedicated action whose view is rendered by exactly one test. Layout is
        // disabled because the framework loads views/layouts with include_once,
        // so the shared layout.phtml only renders for the first action in the
        // whole process; without a layout the output is just this unique view.
        $this->layout  = null;
        $this->payload = 'RENDERED_OK';
    }
}
