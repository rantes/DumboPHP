<?php
namespace App\Controllers;

use DumboPHP\Controller;

/**
 * Fixture: acciones que dejan escapar (o atrapan) errores, para probar el
 * manejo de Throwable sin atrapar del controlador frontal (DumboPHP\index).
 */
class FailController extends Controller {
    public array $noTemplate = ['type', 'div', 'caught', 'partial', 'ajax', 'redirect', 'exception'];

    private function _needsInt(int $n): int {
        return $n;
    }

    /** TypeError natural: el mensaje contiene el nombre del método y la ruta de este archivo. */
    public function typeAction(): void {
        $this->_needsInt('SECRET_DETAIL_123');
    }

    public function divAction(): void {
        echo intdiv(1, 0);
    }

    /** Exception que la acción NO atrapa. */
    public function exceptionAction(): void {
        throw new \RuntimeException('SECRET_DETAIL_123');
    }

    /** Patrón de las aplicaciones: la acción atrapa y responde con su propio código. */
    public function caughtAction(): void {
        try {
            throw new \DomainException('negocio');
        } catch (\Exception $e) {
            $this->setResponseCode(HTTP_422);
            $this->respondToAJAX(json_encode(['d' => [], 'message' => 'caught: ' . $e->getMessage()]));
        }
    }

    /** Salida parcial, con y sin buffer propio, antes del fallo. */
    public function partialAction(): void {
        echo 'PARTIAL_UNBUFFERED';
        ob_start();
        echo 'PARTIAL_BUFFERED';
        intdiv(1, 0);
    }

    /** Ya preparó una respuesta AJAX y luego falla. */
    public function ajaxAction(): void {
        $this->respondToAJAX(json_encode(['message' => 'HALF_DONE']));
        intdiv(1, 0);
    }

    /** Pidió una redirección y luego falla. */
    public function redirectAction(): void {
        $this->redirect(INST_URI . 'test/index');
        intdiv(1, 0);
    }

    /** La acción va bien; la vista falla a mitad del render. */
    public function viewAction(): void {
        $this->layout = null;
    }
}
