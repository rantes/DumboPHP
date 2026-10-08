<?php
namespace tests;

use DumboPHP\Controller;
use DumboPHP\index;
use DumboPHP\lib\Timothy\dumboTests;

/**
 * Secuencia completa del front controller (la de app/webroot/index.php y de
 * `dumbo run`): new index() y luego $index->page->display().
 *
 * - Controlador inexistente: antes llamaba a $this->setResponseCode() (método que
 *   `index` no define) y luego dejaba $page sin inicializar. Ahora asigna un
 *   controlador mínimo y display() responde 404 «Missing Controller».
 * - Acción inexistente: comportamiento previo, debe seguir igual.
 */
class TestMissingController extends dumboTests {

    public function beforeEach(): void {
        http_response_code(HTTP_200);
    }

    /** @return array{caught:string,output:string,status:int|bool,page:mixed} */
    private function _front(string $url): array {
        $_GET   = ['url' => $url];
        $caught = '';
        $page   = null;
        $buf    = '';

        ob_start();
        try {
            $index = new index();
            $page  = $index->page;
            $index->page->display();
        } catch (\Throwable $e) {
            $caught = get_class($e) . ': ' . $e->getMessage();
        } finally {
            $buf = ob_get_clean();
        }

        return ['caught' => $caught, 'output' => $buf, 'status' => http_response_code(), 'page' => $page];
    }

    public function missingControllerFullSequenceDoesNotThrowTest(): void {
        $this->describe('Controlador inexistente: new index() + page->display() no lanza ningún Error');

        $result = $this->_front('no_such_controller/index');

        $this->assertEquals('', $result['caught'], 'No debe lanzarse ningún Error/excepción');
        $this->assertTrue($result['page'] instanceof Controller, '$index->page debe ser un Controller');
    }

    public function missingControllerRespondsPlain404Test(): void {
        $this->describe('Controlador inexistente: estado 404 y cuerpo «Missing Controller» (una sola vez)');

        $result = $this->_front('no_such_controller/index');

        $this->assertEquals(HTTP_404, $result['status'], 'Estado 404');
        $this->assertEquals('Missing Controller', $result['output'], 'Cuerpo esperado, sin duplicar ni agregar texto');
    }

    public function missingControllerWithParamsStillRespondsPlain404Test(): void {
        $this->describe('Controlador inexistente con acción y parámetros también responde 404');

        $result = $this->_front('no_such_controller/algo/99');

        $this->assertEquals('', $result['caught'], 'Sin Error');
        $this->assertEquals(HTTP_404, $result['status'], 'Estado 404');
        $this->assertEquals('Missing Controller', $result['output'], 'Cuerpo esperado');
    }

    public function missingActionBranchUnchangedTest(): void {
        $this->describe('Acción inexistente en un controlador existente: sigue respondiendo 404 «Missing Action»');

        $result = $this->_front('test/no_such_action');

        $this->assertEquals('', $result['caught'], 'Sin Error');
        $this->assertEquals(HTTP_404, $result['status'], 'Estado 404');
        $this->assertEquals('Missing Action', $result['output'], 'Cuerpo esperado');
        $this->assertEquals('App\Controllers\TestController', get_class($result['page']), 'La página sigue siendo el controlador real, no el mínimo');
    }

    public function existingRouteUnchangedTest(): void {
        $this->describe('Una ruta válida sigue ejecutando su acción (el controlador mínimo no interviene)');

        $result = $this->_front('test/index');

        $this->assertEquals('', $result['caught'], 'Sin Error');
        $this->assertEquals('App\Controllers\TestController', get_class($result['page']), 'Controlador real');
        $this->assertEquals(HTTP_200, $result['status'], 'Estado 200');
    }
}
