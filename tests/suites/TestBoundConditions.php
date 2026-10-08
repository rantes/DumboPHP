<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;
use DumboPHP\QueryConditionException;

/**
 * Lecturas por valor con PARÁMETROS ENLAZADOS (fase 1): Find_by_*, condiciones en array, IN/BETWEEN/LIKE/NULL,
 * ORDER BY y LIMIT validados, Paginate compartiendo parámetros entre el conteo y la página, y la validación
 * `unique` de Save().
 *
 * Solo aplica con DUMBO_QUOTE_CONDITIONS activa:   php tests/run.php --protected
 * Con la constante apagada (por defecto) cada prueba se registra como "bandera apagada" y no afirma nada.
 *
 * Inyección: se prueba con la comilla simple (SQLite) y con la barra invertida. SQLite NO trata la barra invertida
 * como escape, así que con la barra invertida estas pruebas verifican que el valor llega LITERAL y sin error de
 * sintaxis, pero solo en MySQL (DUMBO_TEST_MYSQL=1, ver tests/README.md) demuestran que no hay inyección.
 * Con parámetros enlazados el valor nunca se mezcla con el SQL, así que el resultado debe ser el mismo en ambos.
 */
class TestBoundConditions extends dumboTests {

    public function beforeEach(): void {
        $this->_migrateTables(['users', 'posts', 'unique_tags', 'soft_posts']);
    }

    private function flagOn(): bool {
        return defined('DUMBO_QUOTE_CONDITIONS') && DUMBO_QUOTE_CONDITIONS;
    }

    private function user(string $name, string $email, int $age = 20) {
        $u = $this->User->Niu(['name' => $name, 'email' => $email, 'age' => (string) $age]);
        $u->Save() or trigger_error((string) $u->_error, E_USER_ERROR);
        return $u;
    }

    private function post(string $title, ?string $body = null, int $userId = 1) {
        $data = ['title' => $title, 'user_id' => (string) $userId];
        $body === null or $data['body'] = $body;
        $p = $this->Post->Niu($data);
        $p->Save() or trigger_error((string) $p->_error, E_USER_ERROR);
        return $p;
    }

    private function thrown(callable $fn): ?\Throwable {
        $caught = null;
        try {
            $fn();
        } catch (\Throwable $e) {
            $caught = $e;
        }
        return $caught;
    }

    private function emails(object $set): array {
        $out = [];
        foreach ($set as $row) {
            $out[] = $row->email;
        }
        sort($out);
        return $out;
    }

    // ===================== inyección: comilla simple y barra invertida =====================

    public function singleQuotePayloadsMatchNothingTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('Ana', 'ana@example.com');
        foreach (["x' OR '1'='1", "x' OR 1=1 -- ", "' OR ''='", "x'; DROP TABLE users; --", "') OR ('1'='1"] as $payload) {
            $this->assertEquals(0, $this->User->Find(['conditions' => [['email', $payload]]])->counter(), 'array: ' . $payload);
            $this->assertEquals(0, $this->User->Find_by_email($payload)->counter(), 'Find_by_: ' . $payload);
            $this->assertEquals(0, $this->User->Find(['conditions' => [['email', 'LIKE', $payload]]])->counter(), 'LIKE: ' . $payload);
            $this->assertEquals(0, $this->User->Find(['conditions' => [['email', 'IN', [$payload, 'z']]]])->counter(), 'IN: ' . $payload);
            $this->assertEquals(0, $this->User->Find(['conditions' => [['email', 'BETWEEN', $payload, $payload]]])->counter(), 'BETWEEN: ' . $payload);
        }
        $this->assertEquals(1, $this->User->Find()->counter(), 'La tabla sigue intacta.');
    }

    public function backslashPayloadsMatchNothingTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('Ana', 'ana@example.com');
        // SOLO MySQL demuestra la ausencia de inyección con la barra invertida (aquí valida que no hay error de sintaxis).
        foreach (["x\\' OR 1=1 -- -", "\\", "\\\\", "x\\\\' OR '1'='1", "x\\", "\\'"] as $payload) {
            $this->assertEquals(0, $this->User->Find(['conditions' => [['email', $payload]]])->counter(), 'array: ' . $payload);
            $this->assertEquals(0, $this->User->Find_by_email($payload)->counter(), 'Find_by_: ' . $payload);
            $this->assertEquals(0, $this->User->Find(['conditions' => [['email', 'IN', [$payload]]]])->counter(), 'IN: ' . $payload);
        }
    }

    public function backslashValuesRoundTripTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        foreach (['dom\\user@x.com', 'trailing\\', '\\\\double@x.com', "q'\\@x.com"] as $email) {
            $this->user('Ana', $email);
            $this->assertEquals(1, $this->User->Find_by_email($email)->counter(), 'Se encuentra literal: ' . $email);
        }
    }

    // ===================== datos legítimos =====================

    public function legitimateSpecialValuesAreFoundTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        foreach (["o'brien@example.com", 'a%b@x.com', 'a_b@x.com', 'ñandú@日本.jp', 'emoji😀@x.com', 'tab	sep@x.com'] as $email) {
            $this->user('Ana', $email);
            $this->assertEquals(1, $this->User->Find_by_email($email)->counter(), 'Exacto: ' . $email);
            $this->assertEquals(1, $this->User->Find(['conditions' => [['email', '=', $email]]])->counter(), 'Array: ' . $email);
        }
    }

    public function percentAndUnderscoreAreLiteralInEqualsTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('A', 'a%b@x.com');
        $this->user('B', 'axb@x.com');
        $this->user('C', 'a_b@x.com');
        // En igualdad `%` y `_` NO son comodines.
        $this->assertEquals(['a%b@x.com'], $this->emails($this->User->Find_by_email('a%b@x.com')));
        $this->assertEquals(['a_b@x.com'], $this->emails($this->User->Find_by_email('a_b@x.com')));
    }

    public function likeKeepsWildcardsBoundPatternTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('A', 'a%b@x.com');
        $this->user('B', 'axb@x.com');
        $this->user('C', 'a_b@x.com');
        // Documentado: en LIKE el patrón se enlaza ÍNTEGRO y `%`/`_` siguen siendo comodines (el llamador decide el patrón).
        $this->assertEquals(3, $this->User->Find(['conditions' => [['email', 'LIKE', 'a%b@x.com']]])->counter());
        $this->assertEquals(3, $this->User->Find(['conditions' => [['email', 'like', 'a_b@x.com']]])->counter());
        $this->assertEquals(3, $this->User->Find(['conditions' => [['email', 'LIKE', '%']]])->counter());
        $this->assertEquals(0, $this->User->Find(['conditions' => [['email', 'NOT LIKE', '%']]])->counter());
        $this->assertEquals(0, $this->User->Find(['conditions' => [['email', 'LIKE', "%'%"]]])->counter(), 'Un patrón con comilla es solo un patrón (sin coincidencias, sin error).');
    }

    public function emptyStringIsAValueNotNullTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->post('con cuerpo vacío', '');
        $this->post('sin cuerpo');
        $this->assertEquals(['con cuerpo vacío'], $this->titles($this->Post->Find(['conditions' => [['body', '']]])));
        $this->assertEquals(['sin cuerpo'], $this->titles($this->Post->Find(['conditions' => [['body', null]]])));
    }

    private function titles(object $set): array {
        $out = [];
        foreach ($set as $row) {
            $out[] = $row->title;
        }
        sort($out);
        return $out;
    }

    public function nullBecomesIsNullTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->post('p1', 'texto');
        $this->post('p2');
        $this->post('p3', '');

        $isNull = $this->Post->Find(['conditions' => [['body', null]]]);
        $this->assertEquals(['p2'], $this->titles($isNull));
        $this->assertTrue(str_contains($isNull->_sqlQuery, 'body IS NULL'), 'SQL: ' . $isNull->_sqlQuery);

        $notNull = $this->Post->Find(['conditions' => [['body', '!=', null]]]);
        $this->assertEquals(['p1', 'p3'], $this->titles($notNull));
        $this->assertTrue(str_contains($notNull->_sqlQuery, 'body IS NOT NULL'));
        $this->assertEquals(['p1', 'p3'], $this->titles($this->Post->Find(['conditions' => [['body', '<>', null]]])));
        $this->assertEquals(['p2'], $this->titles($this->Post->Find_by_body(null)));
    }

    public function nullWithOtherOperatorsThrowsTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        foreach (['<', '>', 'LIKE', '>='] as $op) {
            $e = $this->thrown(fn() => $this->Post->Find(['conditions' => [['body', $op, null]]]));
            $this->assertTrue($e instanceof QueryConditionException, "NULL con {$op} debe lanzar.");
        }
        $e = $this->thrown(fn() => $this->Post->Find(['conditions' => [['user_id', 'BETWEEN', 1, null]]]));
        $this->assertTrue($e instanceof QueryConditionException, 'BETWEEN con null debe lanzar.');
    }

    public function numbersAsStringsAndIntsMatchTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('A', 'a@x.com', 17);
        $this->user('B', 'b@x.com', 30);
        $this->assertEquals(1, $this->User->Find(['conditions' => [['age', '30']]])->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['age', 30]]])->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['age', '>', '18']]])->counter());
        $this->assertEquals(2, $this->User->Find(['conditions' => [['age', 'BETWEEN', 10, '40']]])->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['id', '=', true]]])->counter(), 'bool → 1');
    }

    public function unicodeAndDoubleSpacesAreExactTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->post('Ñandú 日本語 😀');
        $this->post('a  b');
        $this->post("a\nb");
        $this->post('a b');
        $this->assertEquals(['Ñandú 日本語 😀'], $this->titles($this->Post->Find_by_title('Ñandú 日本語 😀')));
        $this->assertEquals(['Ñandú 日本語 😀'], $this->titles($this->Post->Find(['conditions' => [['title', 'LIKE', '%日本%']]])));
        // Los espacios dobles y saltos de línea del VALOR no se normalizan (el valor no viaja dentro del SQL).
        $this->assertEquals(['a  b'], $this->titles($this->Post->Find_by_title('a  b')));
        $this->assertEquals(["a\nb"], $this->titles($this->Post->Find_by_title("a\nb")));
        $this->assertEquals(['a b'], $this->titles($this->Post->Find_by_title('a b')));
    }

    // ===================== IN / BETWEEN / conectores =====================

    public function inExpandsOnePlaceholderPerItemTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('A', "o'brien@x.com");
        $this->user('B', 'b@x.com');
        $this->user('C', 'c@x.com');
        $found = $this->User->Find(['conditions' => [['email', 'IN', ["o'brien@x.com", 'c@x.com', 'nadie@x.com']]]]);
        $this->assertEquals(['c@x.com', "o'brien@x.com"], $this->emails($found));
        $this->assertEquals(2, $found->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['email', 'NOT IN', ["o'brien@x.com", 'c@x.com']]]])->counter());
        $this->assertEquals(2, $this->User->Find(['conditions' => [['id', 'IN', [1, '3']]]])->counter());
        $this->assertEquals(1, $this->User->Find(['conditions' => [['email', 'IN', 'b@x.com']]])->counter(), 'Un escalar se trata como lista de uno.');
    }

    public function emptyInIsFalseAndEmptyNotInIsTrueTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('A', 'a@x.com');
        $this->user('B', 'b@x.com');
        $in = $this->User->Find(['conditions' => [['email', 'IN', []]]]);
        $this->assertEquals(0, $in->counter(), 'IN vacío = condición falsa (sin error de sintaxis).');
        $this->assertTrue(str_contains($in->_sqlQuery, '1=0'), $in->_sqlQuery);
        $this->assertEquals(2, $this->User->Find(['conditions' => [['email', 'NOT IN', []]]])->counter(), 'NOT IN vacío = verdadero.');
        $this->assertEquals(0, $this->User->Find(['conditions' => [['email', 'IN', [null]]]])->counter(), 'Los null del listado se omiten.');
        $this->assertEquals(1, $this->User->Find(['conditions' => [['email', 'IN', [null, 'a@x.com']]]])->counter());
    }

    public function placeholdersAreUniquePerQueryTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('A', 'a@x.com');
        $r = $this->User->Find(['conditions' => [
            ['name', 'A'], ['email', 'IN', ['a@x.com', 'a@x.com']], ['age', 'BETWEEN', 1, 99], ['name', 'LIKE', 'A%'], ['email', 'a@x.com'],
        ]]);
        $this->assertEquals(1, $r->counter());
        preg_match_all('/:__c\d+/', $r->_sqlQuery, $m);
        $this->assertEquals(sizeof($m[0]), sizeof(array_unique($m[0])), 'Ningún placeholder se repite: ' . $r->_sqlQuery);
        $this->assertEquals(7, sizeof($m[0]));
    }

    public function stateIsCleanBetweenQueriesAndAfterErrorsTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('A', 'a@x.com');
        $this->user('B', 'b@x.com');
        $m = $this->User;
        $this->assertEquals(1, $m->Find(['conditions' => [['name', 'A']]])->counter());
        $this->assertEquals(1, $m->Find(['conditions' => [['name', 'B']]])->counter(), 'No acumula condiciones de la consulta anterior.');
        // Condición inválida a mitad de la lista: ni condiciones ni parámetros quedan a medias.
        $e = $this->thrown(fn() => $m->Find(['conditions' => [['name', 'A'], ['no_such_col', 'x']]]));
        $this->assertTrue($e instanceof QueryConditionException);
        $this->assertEquals(2, $m->Find()->counter(), 'Tras el error la instancia está limpia.');
        $e = $this->thrown(fn() => $m->Find(['conditions' => [['name', 'A']], 'sort' => 'RAND()']));
        $this->assertTrue($e instanceof QueryConditionException);
        $this->assertEquals(2, $m->Find()->counter());
    }

    public function orConnectorStillWorksTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('A', 'a@x.com');
        $this->user('B', 'b@x.com');
        $this->user('C', 'c@x.com');
        $r = $this->User->Find(['conditions' => [['name', 'A'], 'OR' => ['name', 'B']]]);
        $this->assertEquals(2, $r->counter());
    }

    public function andFragmentsMixWithBoundConditionsTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('A', 'a@x.com', 10);
        $this->user('B', 'b@x.com', 50);
        // `and()` (SQL fijo del llamador) convive con condiciones enlazadas.
        $r = $this->User->and('`age` > 20')->Find(['conditions' => [['email', 'b@x.com']]]);
        $this->assertEquals(1, $r->counter());
        $this->assertEquals(0, $this->User->and('`age` > 20')->Find(['conditions' => [['email', 'a@x.com']]])->counter());
    }

    // ===================== ORDER BY y LIMIT =====================

    public function orderByAcceptsColumnsAliasesAndQualifiedNamesTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('B', 'b@x.com', 5);
        $this->user('A', 'a@x.com', 9);
        $first = fn(array $p) => $this->User->Find($p)->email;
        $this->assertEquals('a@x.com', $first([':first', 'sort' => 'name ASC']));
        $this->assertEquals('b@x.com', $first([':first', 'sort' => '`name` desc']));
        $this->assertEquals('b@x.com', $first([':first', 'sort' => 'users.age ASC']));
        $this->assertEquals('a@x.com', $first([':first', 'sort' => '`age` DESC, `id` ASC']));
        $this->assertEquals('a@x.com', $first([':first', 'sort' => '1 ASC', 'fields' => 'email, name']));
        $this->assertEquals('a@x.com', $first([':first', 'fields' => 'id, name AS who, email', 'sort' => 'who ASC']));
    }

    public function orderByRejectsExpressionsAndInjectionTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('A', 'a@x.com');
        foreach (['RAND()', 'name; DROP TABLE users', 'name, (SELECT 1)', 'name ASC, ', 'no_such_col', "name' ", 'name`', '', 'FIELD(name,1)', 'name ASC LIMIT 1'] as $sort) {
            $e = $this->thrown(fn() => $this->User->Find(['sort' => $sort]));
            $this->assertTrue($e instanceof QueryConditionException, 'ORDER BY inválido debe lanzar: ' . json_encode($sort));
        }
        $this->assertEquals(1, $this->User->Find()->counter());
    }

    public function limitIsCastToIntegerOrRejectedTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        for ($i = 1; $i <= 5; $i++) {
            $this->user("U{$i}", "u{$i}@x.com");
        }
        $this->assertEquals(2, $this->User->Find(['limit' => 2])->counter());
        $this->assertEquals(2, $this->User->Find(['limit' => '2'])->counter());
        $this->assertEquals(3, $this->User->Find(['limit' => ' 2 , 3 ', 'sort' => 'id ASC'])->counter());
        $this->assertEquals('u3@x.com', $this->User->Find(['limit' => '2,1', 'sort' => 'id ASC'])->email);
        foreach (['2; DROP TABLE users', '1 OR 1', '-1', '1,2,3', 'a', '', '1.5', '2 OFFSET 1'] as $limit) {
            $e = $this->thrown(fn() => $this->User->Find(['limit' => $limit]));
            $this->assertTrue($e instanceof QueryConditionException, 'LIMIT inválido debe lanzar: ' . json_encode($limit));
        }
    }

    // ===================== Paginate =====================

    public function paginateSharesBoundParametersBetweenCountAndPageTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        for ($i = 1; $i <= 25; $i++) {
            $this->user("User{$i}", $i <= 17 ? "match{$i}@x.com" : "other{$i}@x.com", $i);
        }
        $_GET['page'] = 2;
        $page = $this->User->Paginate('http://localhost/users', [
            'per_page'   => 10,
            'conditions' => [['email', 'LIKE', 'match%'], ['name', '!=', "x' OR '1'='1"], ['age', 'BETWEEN', 1, 100]],
            'sort'       => 'id ASC',
        ]);
        unset($_GET['page']);

        $this->assertEquals(17, (int) $page->PaginateTotalItems, 'El conteo usa los mismos parámetros.');
        $this->assertEquals(2, (int) $page->PaginateTotalPages);
        $this->assertEquals(7, $page->counter(), 'La segunda página trae los 7 restantes.');
        $this->assertEquals('match11@x.com', $page[0]->email);
    }

    public function paginateRejectsInvalidSortAndLeavesInstanceCleanTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('A', 'a@x.com');
        $m = $this->User;
        $e = $this->thrown(fn() => $m->Paginate('http://localhost/users', ['conditions' => [['name', 'A']], 'sort' => 'RAND()']));
        $this->assertTrue($e instanceof QueryConditionException);
        $this->assertEquals(1, $m->Find()->counter());
    }

    public function paginateInjectionValueDoesNotChangeTotalsTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $this->user('A', 'a@x.com');
        $this->user('B', 'b@x.com');
        $page = $this->User->Paginate('http://localhost/users', ['conditions' => [['name', "x' OR '1'='1"]]]);
        $this->assertEquals(0, (int) $page->PaginateTotalItems);
        $page = $this->User->Paginate('http://localhost/users', ['conditions' => [['name', "x\\' OR 1=1 -- -"]]]);
        $this->assertEquals(0, (int) $page->PaginateTotalItems);
    }

    // ===================== Save(): validate['unique'] =====================

    public function uniqueValidationHandlesQuotesAndBlocksInjectionTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $first = $this->UniqueTag->Niu(['name' => "O'Brien"]);
        $this->assertTrue($first->Save(), 'Un nombre con comilla se guarda.');

        $dup = $this->UniqueTag->Niu(['name' => "O'Brien"]);
        $this->assertFalse($dup->Save(), 'El duplicado con comilla se detecta (sin excepción SQL).');
        $this->assertTrue(in_array('name', $dup->_error->errFields()));

        $inj = $this->UniqueTag->Niu(['name' => "x' OR '1'='1"]);
        $this->assertTrue($inj->Save(), 'Un valor de inyección NO coincide con el existente: es único.');
        $bs = $this->UniqueTag->Niu(['name' => "x\\' OR 1=1 -- -"]);
        $this->assertTrue($bs->Save());

        $first->name = "O'Brien";
        $this->assertTrue($first->Save(), 'Re-guardar el mismo registro no choca consigo mismo.');
    }

    // ===================== soft delete =====================

    public function softDeleteFilterStillAppliesWithBoundConditionsTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $keep = $this->SoftPost->Niu(['title' => "O'Brien", 'cascade_owner_id' => '1']);
        $this->assertTrue($keep->Save());
        $gone = $this->SoftPost->Niu(['title' => "O'Brien", 'cascade_owner_id' => '1']);
        $this->assertTrue($gone->Save());
        $this->assertTrue($this->SoftPost->Find((int) $gone->id)->Delete((int) $gone->id));

        $this->assertEquals(1, $this->SoftPost->Find_by_title("O'Brien")->counter(), 'El soft-deleted queda fuera.');
        $this->assertEquals(2, $this->SoftPost->Find(['withDeleted' => true, 'conditions' => [['title', "O'Brien"]]])->counter());
    }

    // ===================== campos =====================

    public function joinQualifiedFieldsStayBoundTest(): void {
        if (! $this->flagOn()) { $this->assertTrue(true, 'bandera apagada'); return; }
        $u = $this->user('Ana', 'ana@x.com');
        $this->post("O'Brien's post", 'b', (int) $u->id);
        $r = $this->User->Find([
            'fields'     => 'users.*',
            'join'       => 'INNER JOIN posts ON posts.user_id = users.id',
            'conditions' => [['posts.title', "O'Brien's post"], ['users.email', 'ana@x.com']],
        ]);
        $this->assertEquals(1, $r->counter());
    }
}
