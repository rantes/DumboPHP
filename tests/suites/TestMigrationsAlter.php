<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;
use Migrations\CreateGadgets;

/**
 * Schema-alteration operations of the Migrations base class (Add_Column, Alter_Column, Remove_Column,
 * Add_Single_Index). Runs on SQLite by default and on MySQL with DUMBO_TEST_MYSQL=1 (see config/db_settings.php).
 */
class TestMigrationsAlter extends dumboTests {

    public function beforeEach(): void {
        $this->_migrateTables(['gadgets']);
    }

    /** @return array<string, array{type: string, notnull: bool, default: ?string}> column => info */
    private function columns(): array {
        $cols = [];
        if (DB->engine === 'mysql') {
            $st = DB->prepare('SELECT column_name, column_type, is_nullable, column_default FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ?');
            $st->execute(['gadgets']);
            foreach ($st->fetchAll(\PDO::FETCH_NUM) as $r) {
                $cols[$r[0]] = ['type' => strtoupper($r[1]), 'notnull' => $r[2] === 'NO', 'default' => $r[3] === null ? null : (string) $r[3]];
            }
        } else {
            foreach (DB->query('PRAGMA table_info(`gadgets`)')->fetchAll(\PDO::FETCH_ASSOC) as $r) {
                $cols[$r['name']] = ['type' => strtoupper($r['type']), 'notnull' => (bool) $r['notnull'], 'default' => $r['dflt_value'] === null ? null : trim((string) $r['dflt_value'], "'")];
            }
        }
        return $cols;
    }

    private function indexNames(): array {
        if (DB->engine === 'mysql') {
            $st = DB->prepare("SELECT DISTINCT index_name FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'gadgets' AND index_name <> 'PRIMARY'");
            $st->execute();
            return $st->fetchAll(\PDO::FETCH_COLUMN);
        }
        return DB->query("SELECT name FROM sqlite_master WHERE type = 'index' AND tbl_name = 'gadgets' AND sql IS NOT NULL")->fetchAll(\PDO::FETCH_COLUMN);
    }

    /** Runs an operation on a fresh fixture; returns the error message, or null on success (\Error included: Timothy would die on it). */
    private function thrown(callable $f): ?string {
        $msg = null;
        ob_start();
        try {
            $f(new CreateGadgets());
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
        } finally {
            ob_end_clean();
        }
        return $msg;
    }

    /** Runs an operation that must succeed. */
    private function run(callable $f): void {
        $this->assertEquals(null, $this->thrown($f));
    }

    public function addColumnTest(): void {
        $this->run(fn($m) => $m->addColumn(['field' => 'qty', 'type' => 'INTEGER', 'default' => '3']));
        $cols = $this->columns();
        $this->assertTrue(isset($cols['qty']));
        $this->assertEquals('3', $cols['qty']['default']);
    }

    public function addColumnIsIdempotentTest(): void {
        $this->run(fn($m) => $m->addColumn(['field' => 'qty', 'type' => 'INTEGER']));
        $this->assertEquals(null, $this->thrown(fn($m) => $m->addColumn(['field' => 'qty', 'type' => 'INTEGER'])));
        $this->assertEquals(3, count($this->columns()));
    }

    public function addColumnNullableTest(): void {
        $this->run(fn($m) => $m->addColumn(['field' => 'note', 'type' => 'TEXT', 'null' => true]));
        $this->assertFalse($this->columns()['note']['notnull']);
    }

    public function alterColumnChangesDefaultTest(): void {
        $this->run(fn($m) => $m->addColumn(['field' => 'qty', 'type' => 'INTEGER', 'default' => '3']));
        $this->run(fn($m) => $m->alterColumn(['field' => 'qty', 'type' => 'INTEGER', 'default' => '9']));
        $this->assertEquals('9', $this->columns()['qty']['default']);
    }

    public function alterColumnKeepsDataAndIndexesTest(): void {
        $this->run(fn($m) => $m->addColumn(['field' => 'qty', 'type' => 'INTEGER', 'default' => '3']));
        $this->run(fn($m) => $m->addSingleIndex('name'));
        DB->exec("INSERT INTO gadgets (name, qty) VALUES ('a', 7)");
        $this->run(fn($m) => $m->alterColumn(['field' => 'qty', 'type' => 'INTEGER', 'default' => '9']));
        $this->assertEquals('7', (string) DB->query('SELECT qty FROM gadgets')->fetchColumn());
        $this->assertEquals(1, count($this->indexNames()));
    }

    public function alterColumnMissingFailsClearlyTest(): void {
        $msg = $this->thrown(fn($m) => $m->alterColumn(['field' => 'ghost', 'type' => 'INTEGER']));
        $this->assertTrue($msg !== null && str_contains($msg, 'ghost') && str_contains($msg, 'does not exist'));
    }

    public function removeColumnTest(): void {
        $this->run(fn($m) => $m->addColumn(['field' => 'qty', 'type' => 'INTEGER']));
        $this->run(fn($m) => $m->removeColumn('qty'));
        $this->assertFalse(isset($this->columns()['qty']));
    }

    public function removeColumnIsIdempotentTest(): void {
        $this->assertEquals(null, $this->thrown(fn($m) => $m->removeColumn('ghost')));
        $this->assertEquals(2, count($this->columns()));
    }

    public function addSingleIndexCreatesIndexTest(): void {
        $this->run(fn($m) => $m->addSingleIndex('name'));
        $this->assertEquals(1, count($this->indexNames()));
    }

    public function addSingleIndexIsIdempotentTest(): void {
        $this->run(fn($m) => $m->addSingleIndex('name'));
        $this->assertEquals(null, $this->thrown(fn($m) => $m->addSingleIndex('name')));
        $this->assertEquals(1, count($this->indexNames()));
    }
}
