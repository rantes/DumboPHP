<?php
namespace tests;

use DumboPHP\index;
use DumboPHP\lib\Timothy\dumboTests;

/**
 * Errores sin atrapar en el controlador frontal: un Throwable que escapa de la
 * acción o de la vista responde 500 genérico, queda completo en el log y nunca
 * llega al cliente. Lo que las acciones ya atrapan no cambia.
 */
class TestUncaughtErrors extends dumboTests {
    private const GENERIC = 'Ocurrió un error en el servidor.';

    private string $_logTmp = '';

    public function beforeEach(): void {
        $this->_cleanLog();
        http_response_code(HTTP_200);
        unset($_SERVER['HTTP_ACCEPT'], $_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->_logTmp = tempnam(sys_get_temp_dir(), 'dumbo_log_');
        ini_set('error_log', $this->_logTmp);
    }

    public function _end_(): void {
        $this->_cleanLog();
    }

    private function _cleanLog(): void {
        ini_restore('error_log');
        is_file($this->_logTmp) && unlink($this->_logTmp);
    }

    /** @return array{caught:string,output:string,status:int|bool,page:mixed,log:string} */
    private function _front(string $url): array {
        $_GET   = ['url' => $url];
        $caught = '';
        $page   = null;

        ob_start();
        try {
            $index = new index();
            $page  = $index->page;
            $page->display();
        } catch (\Throwable $e) {
            $caught = get_class($e) . ': ' . $e->getMessage();
        }
        $buf = ob_get_clean();

        return ['caught' => $caught, 'output' => $buf, 'status' => http_response_code(), 'page' => $page, 'log' => (string) file_get_contents($this->_logTmp)];
    }

    private function _assertNoLeak(string $body): void {
        foreach (['SECRET_DETAIL_123', '_needsInt', 'fail_controller', 'must be of type', 'Division by zero', 'Stack trace', 'intdiv'] as $needle) {
            $this->assertFalse(str_contains($body, $needle), "El cuerpo no debe contener «{$needle}»");
        }
    }

    public function typeErrorRespondsGeneric500Test(): void {
        $this->describe('TypeError sin atrapar: 500 genérico, sin mensaje ni ruta');
        $r = $this->_front('fail/type');

        $this->assertEquals('', $r['caught'], 'No debe escapar nada del frontal');
        $this->assertEquals(HTTP_500, $r['status'], 'Estado 500');
        $this->assertEquals(self::GENERIC, $r['output'], 'Cuerpo genérico');
        $this->_assertNoLeak($r['output']);
    }

    public function divisionByZeroRespondsGeneric500Test(): void {
        $this->describe('DivisionByZeroError sin atrapar: 500 genérico');
        $r = $this->_front('fail/div');

        $this->assertEquals('', $r['caught'], 'No debe escapar nada del frontal');
        $this->assertEquals(HTTP_500, $r['status'], 'Estado 500');
        $this->assertEquals(self::GENERIC, $r['output'], 'Cuerpo genérico');
        $this->_assertNoLeak($r['output']);
    }

    public function uncaughtExceptionRespondsGeneric500Test(): void {
        $this->describe('Exception que la acción no atrapa: también 500 genérico');
        $r = $this->_front('fail/exception');

        $this->assertEquals(HTTP_500, $r['status'], 'Estado 500');
        $this->assertEquals(self::GENERIC, $r['output'], 'Cuerpo genérico');
        $this->_assertNoLeak($r['output']);
    }

    public function caughtByActionIsUnchangedTest(): void {
        $this->describe('Lo que la acción ya atrapa se comporta igual: su código y su cuerpo, sin log');
        $r = $this->_front('fail/caught');

        $this->assertEquals(HTTP_422, $r['status'], 'Estado de la acción');
        $this->assertEquals('{"d":[],"message":"caught: negocio"}', $r['output'], 'Cuerpo de la acción');
        $this->assertEquals('', $r['log'], 'Nada se registra');
        $this->assertTrue($r['page']->failure() === null, 'Sin fallo');
    }

    public function successfulActionIsUnchangedTest(): void {
        $this->describe('Una acción sin errores no registra nada');
        $r = $this->_front('test/json');

        $this->assertEquals(HTTP_200, $r['status'], 'Estado 200');
        $this->assertEquals('{"ok":true}', $r['output'], 'Cuerpo');
        $this->assertEquals('', $r['log'], 'Nada se registra');
    }

    public function logKeepsFullDetailTest(): void {
        $this->describe('El log conserva clase, mensaje, archivo, línea y traza');
        $r = $this->_front('fail/type');

        $this->assertTrue(str_contains($r['log'], 'TypeError'), 'Clase');
        $this->assertTrue(str_contains($r['log'], 'SECRET_DETAIL_123') || str_contains($r['log'], '_needsInt'), 'Mensaje');
        $this->assertTrue(preg_match('#fail_controller\.php:\d+#', $r['log']) === 1, 'Archivo y línea');
        $this->assertTrue(str_contains($r['log'], 'Stack trace'), 'Traza');
        $this->assertTrue(str_contains($r['log'], 'fail/type'), 'URL de la petición');
        $this->assertTrue($r['page']->failure() instanceof \TypeError, 'failure() devuelve el Throwable');
    }

    public function ajaxByAcceptHeaderReturnsJsonMessageTest(): void {
        $this->describe('Petición con Accept: application/json recibe JSON con message');
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $r = $this->_front('fail/div');

        $this->assertEquals(HTTP_500, $r['status'], 'Estado 500');
        $this->assertEquals(['message' => self::GENERIC], json_decode($r['output'], true), 'JSON con message');
        $this->_assertNoLeak($r['output']);
    }

    public function ajaxByRequestedWithHeaderReturnsJsonMessageTest(): void {
        $this->describe('Petición con X-Requested-With: XMLHttpRequest recibe JSON con message');
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $r = $this->_front('fail/div');

        $this->assertEquals(HTTP_500, $r['status'], 'Estado 500');
        $this->assertEquals(['message' => self::GENERIC], json_decode($r['output'], true), 'JSON con message');
    }

    public function ajaxAfterRespondToAjaxReturnsJsonMessageTest(): void {
        $this->describe('Acción que ya llamó respondToAJAX y luego falla: JSON genérico, no la respuesta a medias');
        $r = $this->_front('fail/ajax');

        $this->assertEquals(HTTP_500, $r['status'], 'Estado 500');
        $this->assertEquals(['message' => self::GENERIC], json_decode($r['output'], true), 'JSON con message');
        $this->assertFalse(str_contains($r['output'], 'HALF_DONE'), 'Sin la respuesta a medias');
    }

    public function partialOutputIsNotMixedTest(): void {
        $this->describe('La salida parcial (con y sin buffer propio) no se mezcla con el 500');
        $r = $this->_front('fail/partial');

        $this->assertEquals(HTTP_500, $r['status'], 'Estado 500');
        $this->assertEquals(self::GENERIC, $r['output'], 'Solo el cuerpo genérico');
    }

    public function partialViewOutputIsNotMixedTest(): void {
        $this->describe('Una vista que falla a mitad del render no deja su salida ni sus buffers abiertos');
        $level = ob_get_level();
        $r     = $this->_front('fail/view');

        $this->assertEquals(HTTP_500, $r['status'], 'Estado 500');
        $this->assertEquals(self::GENERIC, $r['output'], 'Solo el cuerpo genérico');
        $this->assertEquals($level, ob_get_level(), 'Mismo nivel de buffers que antes');
    }

    public function pendingRedirectIsDroppedTest(): void {
        $this->describe('Una redirección pedida antes del fallo no gana al 500');
        $r = $this->_front('fail/redirect');

        $this->assertEquals(HTTP_500, $r['status'], 'Estado 500, no 302');
        $this->assertEquals(self::GENERIC, $r['output'], 'Cuerpo genérico');
    }

    public function timothyShowsUnexpectedFailureOnStderrTest(): void {
        $this->describe('Timothy: un fallo inesperado no se vuelve invisible (traza por STDERR) y la prueba continúa');
        // Con error_log de PHP apuntando a un archivo (php.ini), la traza igual debe llegar por STDERR.
        $toFile = $this->_child(['-d', 'error_log=' . $this->_logTmp]);
        $this->assertEquals(0, $toFile['exit'], 'El proceso termina bien');
        $this->assertEquals('CONTINUED code=500 failure=TypeError', $toFile['out'], 'La prueba continúa y ve el 500');
        $this->assertTrue(str_contains($toFile['err'], 'TypeError') && str_contains($toFile['err'], 'Stack trace'), 'La traza completa llega por STDERR');
        $this->assertTrue(preg_match('#fail_controller\.php:\d+#', $toFile['err']) === 1, 'Con archivo y línea');

        // Sin error_log configurado el log de PHP ya va a STDERR: una sola vez, sin duplicar.
        $default = $this->_child([]);
        $this->assertEquals(1, substr_count($default['err'], 'Stack trace'), 'La traza aparece una sola vez');
    }

    /** @return array{exit:int,out:string,err:string} */
    private function _child(array $phpOptions): array {
        $cmd  = array_merge([PHP_BINARY], $phpOptions, [INST_PATH . 'support/run_failing_action.php', 'fail/type']);
        $proc = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $out  = stream_get_contents($pipes[1]);
        $err  = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return ['exit' => proc_close($proc), 'out' => $out, 'err' => $err];
    }
}
