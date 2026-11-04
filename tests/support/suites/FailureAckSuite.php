<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;

/**
 * Suite de apoyo (no la corre run.php): cada caso se elige con la variable de entorno ACK_CASE.
 * markerTest corre al final y delata si el dispatcher siguió (o se detuvo por --halt).
 */
class FailureAckSuite extends dumboTests {
    public function unacknowledgedTest(): void {
        getenv('ACK_CASE') === 'unacknowledged' && $this->_runAction('fail/type');
    }

    public function acknowledgedTest(): void {
        if (getenv('ACK_CASE') === 'acknowledged'):
            $page = $this->_runAction('fail/type');
            $this->assertTrue($page->failure() instanceof \TypeError, 'El fallo se lee con failure()');
        endif;
    }

    public function caughtByActionTest(): void {
        if (getenv('ACK_CASE') === 'caught'):
            $page = $this->_runAction('fail/caught');
            $this->assertEquals(HTTP_422, http_response_code(), 'La acción respondió su propio código');
            $this->assertTrue($page->failure() === null, 'Sin fallo');
        endif;
    }

    public function markerTest(): void {
        fwrite(STDOUT, 'MARKER_RAN');
    }
}
