<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;
use Migrations\CreateUsers;

/**
 * Verifies the Migrations base class: table create/drop/reset and the field /
 * definition introspection used by the schema-sync assertions.
 */
class TestMigrations extends dumboTests {

    public function beforeEach(): void {
        $this->_migrateTables(['users']);
    }

    private function tableExists(string $table): bool {
        $stmt = DB->query("SELECT name FROM sqlite_master WHERE type='table' AND name='{$table}'");
        return $stmt->fetchColumn() === $table;
    }

    public function createTableTest(): void {
        $m = new CreateUsers();
        ob_start();
        $m->down();
        $m->up();
        ob_get_clean();
        $this->assertTrue($this->tableExists('users'));
    }

    public function dropTableTest(): void {
        $m = new CreateUsers();
        ob_start();
        $m->down();
        ob_get_clean();
        $this->assertFalse($this->tableExists('users'));
    }

    public function resetTest(): void {
        $this->User->Niu(['name' => 'Ana', 'email' => 'ana@example.com'])->Save();
        $m = new CreateUsers();
        ob_start();
        $m->reset();
        ob_get_clean();
        $this->assertEquals(0, $this->User->Find()->counter());
    }

    public function getFieldsTest(): void {
        $fields = (new CreateUsers())->getFields();
        $this->assertTrue(in_array('name', $fields) && in_array('email', $fields));
    }

    public function getDefinitionsTest(): void {
        $defs = (new CreateUsers())->getDefinitions();
        $this->assertArrayHasKey('type', $defs[0]);
    }

    public function assertHasFieldsTest(): void {
        $this->assertHasFields($this->User);
    }

    public function assertHasFieldTypesTest(): void {
        $this->assertHasFieldTypes($this->User);
    }
}
