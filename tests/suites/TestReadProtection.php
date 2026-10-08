<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;
use DumboPHP\QueryConditionException;

/**
 * Protecciones de lectura por valor (fase 1, solo seguridad): nombre de campo y operador validados en
 * `_buildConditions`, y el pk de `Save()` enlazado (nunca interpolado).
 *
 * Las pruebas de campo/operador dependen de la constante DUMBO_QUOTE_CONDITIONS (apagada por defecto, como un
 * proyecto sin ella): con la constante apagada se registran como "bandera apagada" y no afirman nada.
 * Correr toda la suite con la protección:  DUMBO_QUOTE_CONDITIONS=1 php tests/run.php
 *
 * Los payloads usan la comilla simple porque SQLite no trata la barra invertida como escape; la barra invertida
 * solo se verifica en MySQL (ver tests/README.md, "Verificación en MySQL").
 */
class TestReadProtection extends dumboTests {

    public function beforeEach(): void {
        $this->_migrateTables(['users']);
    }

    private function flagOn(): bool {
        return defined('DUMBO_QUOTE_CONDITIONS') && DUMBO_QUOTE_CONDITIONS;
    }

    private function makeUser(string $name, string $email) {
        $u = $this->User->Niu(['name' => $name, 'email' => $email]);
        $u->Save();
        return $u;
    }

    /** Ejecuta $fn y retorna la excepción lanzada (o null). */
    private function thrown(callable $fn): ?\Throwable {
        $caught = null;
        try {
            $fn();
        } catch (\Throwable $e) {
            $caught = $e;
        }
        return $caught;
    }

    // ----- Save(): el pk se enlaza (sin depender de la bandera) -----

    public function savePkIsBoundNotInterpolatedTest(): void {
        $a = $this->makeUser('Ana', 'ana@example.com');
        $b = $this->makeUser('Bob', 'bob@example.com');

        // `id` está tipado ?int: el pk solo sería inyectable con un pk personalizado de tipo string. La garantía
        // que se verifica es estructural: el UPDATE enlaza el pk (:__pk) y el valor no aparece dentro del SQL.
        $a->name = 'Anita';
        $this->assertTrue($a->Save());
        $sql = $a->_sqlQuery();
        $this->assertTrue(str_contains($sql, ':__pk'), 'El UPDATE debe enlazar el pk: ' . $sql);
        $this->assertFalse(str_contains($sql, "= '{$a->id}'"), 'El pk no debe interpolarse en el WHERE: ' . $sql);
        $this->assertEquals('Bob', $this->User->Find((int) $b->id)->name);
        $this->assertEquals('Anita', $this->User->Find((int) $a->id)->name);
    }

    public function savePkLegitUpdateStillWorksTest(): void {
        $a = $this->makeUser('Ana', 'ana@example.com');
        $a->email = "o'brien@example.com";
        $this->assertTrue($a->Save());
        $this->assertEquals("o'brien@example.com", $this->User->Find((int) $a->id)->email);
    }

    public function publicUpdateDoesNotExposeBindingsTest(): void {
        $a = $this->makeUser('Ana', 'ana@example.com');
        // 'bindings' no es API pública: se ignora aunque se pase.
        $ok = $this->User->Update(['data' => ['name' => 'Zed'], 'conditions' => '`id`=' . (int) $a->id, 'bindings' => [':x' => 1]]);
        $this->assertTrue($ok);
        $this->assertEquals('Zed', $this->User->Find((int) $a->id)->name);
    }

    // ----- Campo y operador (con la bandera activa) -----

    public function invalidFieldNameThrowsTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->makeUser('Ana', 'ana@example.com');
        foreach (["name = 'x' OR 1=1 --", 'name`', 'no_such_column', '', "a.b.c", 'name; DROP TABLE users', "na\nme"] as $field) {
            $e = $this->thrown(fn() => $this->User->Find(['conditions' => [[$field, 'x']]]));
            $this->assertTrue($e instanceof QueryConditionException, 'Campo inválido debe lanzar: ' . json_encode($field));
        }
    }

    public function invalidFieldViaFindByThrowsTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $e = $this->thrown(fn() => $this->User->Find_by_no_such_column('x'));
        $this->assertTrue($e instanceof QueryConditionException, 'Find_by_<columna inexistente> debe lanzar.');
    }

    public function validFieldNamesAreAcceptedTest(): void {
        $this->makeUser('Ana', 'ana@example.com');
        // Columna real, calificada con la tabla del modelo y pk: siempre válidas (con o sin bandera).
        $this->assertEquals(1, $this->User->Find(['conditions' => [['name', 'Ana']]])->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['users.name', 'Ana']]])->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['NAME', 'Ana']]])->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['id', '>', 0]]])->counter());
    }

    public function joinQualifiedFieldFromOtherTableIsAcceptedTest(): void {
        $this->_migrateTables(['users', 'posts']);
        $u = $this->makeUser('Ana', 'ana@example.com');
        $r = $this->User->Find([
            'fields'     => 'users.*',
            'join'       => 'INNER JOIN posts ON posts.user_id = users.id',
            'conditions' => [['posts.user_id', (int) $u->id]],
        ]);
        $this->assertEquals(0, $r->counter());
    }

    public function invalidOperatorThrowsTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->makeUser('Ana', 'ana@example.com');
        foreach (["= 'x' OR 1=1 --", 'OR 1=1', ';', 'REGEXP', 'IS NOT', "= 1 UNION SELECT 1"] as $op) {
            $e = $this->thrown(fn() => $this->User->Find(['conditions' => [['name', $op, 'x']]]));
            $this->assertTrue($e instanceof QueryConditionException, 'Operador inválido debe lanzar: ' . json_encode($op));
        }
    }

    public function whitelistedOperatorsWorkTest(): void {
        $this->makeUser('Ana', 'ana@example.com');
        $this->makeUser('Bob', 'bob@example.com');
        $this->assertEquals(2, $this->User->Find(['conditions' => [['id', '>=', 1]]])->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['name', '!=', 'Ana']]])->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['name', '<>', 'Ana']]])->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['name', 'like', 'An%']]])->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['name', 'not  like', 'An%']]])->counter());
        $this->assertEquals(2, $this->User->Find(['conditions' => [['name', 'IN', ['Ana', 'Bob']]]])->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['name', 'NOT IN', ['Ana']]]])->counter());
    }

    public function connectorStillValidatedTest(): void {
        $e = $this->thrown(fn() => $this->User->Find(['conditions' => ['XOR' => ['name', 'x']]]));
        $this->assertTrue($e instanceof \Throwable, 'Un conector que no sea AND/OR sigue lanzando.');
    }
}
