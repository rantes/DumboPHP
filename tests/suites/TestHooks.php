<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;

/**
 * Verifies the model lifecycle hooks (before_save, before_insert, after_insert,
 * after_update) including ordering and short-circuiting when a hook activates
 * an error. The User fixture records each hook into $hookOrder.
 */
class TestHooks extends dumboTests {

    public function beforeEach(): void {
        $this->_migrateTables(['users']);
    }

    private function newUser() {
        return $this->User->Niu(['name' => 'Ana', 'email' => 'ana@example.com']);
    }

    public function beforeSaveExecutedTest(): void {
        $u = $this->User->Niu(['name' => '<b>Ann</b>', 'email' => 'ann@example.com']);
        $u->Save();
        // sanitizeName ran htmlentities() on the name before insert.
        $this->assertEquals(htmlentities('<b>Ann</b>'), $u->name);
    }

    public function beforeInsertOnlyTest(): void {
        $u = $this->newUser();
        $u->Save();
        $this->assertTrue(in_array('before_insert', $u->hookOrder));

        $u->hookOrder = [];
        $u->name      = 'Anita';
        $u->Save();
        $this->assertFalse(in_array('before_insert', $u->hookOrder));
        $this->assertTrue(in_array('after_update', $u->hookOrder));
    }

    public function hookErrorStopsSaveTest(): void {
        $u             = $this->newUser();
        $u->failOnSave = true;
        $this->assertFalse($u->Save());
    }

    public function multipleHooksOrderTest(): void {
        $u = $this->newUser();
        $u->Save();
        $this->assertEquals(['first', 'second'], array_slice($u->hookOrder, 0, 2));
    }

    public function afterInsertExecutedTest(): void {
        $u = $this->newUser();
        $u->Save();
        $this->assertTrue(in_array('after_insert', $u->hookOrder));
    }

    public function afterUpdateExecutedTest(): void {
        $u = $this->newUser();
        $u->Save();
        $u->hookOrder = [];
        $u->name      = 'Anita';
        $u->Save();
        $this->assertTrue(in_array('after_update', $u->hookOrder));
    }
}
