<?php
namespace tests;

use DumboPHP\index;
use DumboPHP\lib\Timothy\dumboTests;

/**
 * La rama «controlador inexistente» del front controller `index` debe responder
 * 404 en texto plano, sin lanzar un Error de PHP (antes llamaba a
 * $this->setResponseCode(), método que `index` no define).
 */
class TestMissingController extends dumboTests {

    public function beforeEach(): void {
        http_response_code(HTTP_200);
    }

    private function _dispatch(string $url): array {
        $_GET   = ['url' => $url];
        $caught = '';
        $buf    = '';

        ob_start();
        try {
            new index();
        } catch (\Throwable $e) {
            $caught = get_class($e) . ': ' . $e->getMessage();
        } finally {
            $buf = ob_get_clean();
        }

        return ['caught' => $caught, 'output' => $buf];
    }

    public function missingControllerDoesNotThrowTest(): void {
        $this->describe('Un controlador inexistente no debe lanzar Error (setResponseCode no existe en index)');

        $result = $this->_dispatch('no_such_controller/index');

        $this->assertEquals('', $result['caught'], 'No debe lanzarse ninguna excepción/Error');
    }

    public function missingControllerRespondsPlain404Test(): void {
        $this->describe('Un controlador inexistente responde 404 y el texto Missing Controller');

        $result = $this->_dispatch('no_such_controller/index');

        $this->assertEquals('Missing Controller', $result['output'], 'Cuerpo en texto plano');
        $this->assertEquals(HTTP_404, http_response_code(), 'Estado 404');
    }
}
