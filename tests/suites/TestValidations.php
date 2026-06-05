<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;

/**
 * Verifies validates['presence_of'] enforcement during Save() and the error
 * bag (_error) state on failure and on a fresh successful instance.
 */
class TestValidations extends dumboTests {

    public function beforeEach(): void {
        $this->_migrateTables(['users']);
    }

    public function presenceOfPassTest(): void {
        $u = $this->User->Niu(['name' => 'Ana', 'email' => 'ana@example.com']);
        $this->assertTrue($u->Save());
    }

    public function presenceOfNameFailTest(): void {
        $u = $this->User->Niu(['email' => 'ana@example.com']);
        $this->assertFalse($u->Save());
    }

    public function presenceOfEmailFailTest(): void {
        $u = $this->User->Niu(['name' => 'Ana']);
        $this->assertFalse($u->Save());
    }

    public function errorAfterFailTest(): void {
        $u = $this->User->Niu(['email' => 'ana@example.com']);
        $u->Save();
        $this->assertNotEmpty((string) $u->_error);
    }

    public function errFieldsTest(): void {
        $u = $this->User->Niu(['email' => 'ana@example.com']);
        $u->Save();
        $this->assertTrue(in_array('name', $u->_error->errFields()));
    }

    /**
     * A fresh, valid instance saves cleanly with no active error — the error
     * bag is per-instance and starts empty.
     */
    public function errorClearOnSuccessTest(): void {
        $bad = $this->User->Niu(['email' => 'ana@example.com']);
        $bad->Save();
        $this->assertTrue($bad->_error->isActived());

        $good = $this->User->Niu(['name' => 'Bob', 'email' => 'bob@example.com']);
        $this->assertTrue($good->Save());
        $this->assertFalse($good->_error->isActived());
    }
}
