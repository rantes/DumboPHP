<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;

/**
 * Verifies the ActiveRecord ORM: Niu/Save (insert & update), the Find query
 * builder (id, conditions, sort, limit, :first), Delete, counting, automatic
 * audit timestamps and field introspection.
 *
 * Models are reached through lazy load ($this->User), which also exercises the
 * Controller::__get model resolution.
 */
class TestActiveRecord extends dumboTests {

    public function beforeEach(): void {
        $this->_migrateTables(['users']);
    }

    /** Insert a user and return the saved instance. */
    private function makeUser(string $name, string $email, int $age = 20, int $active = 1) {
        $u = $this->User->Niu([
            'name'   => $name,
            'email'  => $email,
            'age'    => (string) $age,
            'active' => (string) $active,
        ]);
        $u->Save();
        return $u;
    }

    public function niuEmptyTest(): void {
        $u = $this->User->Niu();
        $this->assertTrue($u instanceof \App\Models\User);
    }

    public function niuWithDataTest(): void {
        $u = $this->User->Niu(['name' => 'Ana', 'email' => 'ana@example.com']);
        $this->assertEquals('Ana', $u->name);
    }

    public function saveInsertTest(): void {
        $u = $this->User->Niu(['name' => 'Ana', 'email' => 'ana@example.com']);
        $this->assertTrue($u->Save());
        $this->assertGreaterThan(0, $u->id);
    }

    public function saveUpdateTest(): void {
        $u = $this->makeUser('Ana', 'ana@example.com');
        $id = $u->id;
        $u->name = 'Anita';
        $this->assertTrue($u->Save());
        // No new row was created and the value was updated.
        $this->assertEquals(1, $this->User->Find()->counter());
        $this->assertEquals('Anita', $this->User->Find($id)->name);
    }

    public function findAllTest(): void {
        $this->makeUser('Ana', 'ana@example.com');
        $this->makeUser('Bob', 'bob@example.com');
        $all = $this->User->Find();
        $this->assertEquals(2, $all->counter());
    }

    public function findByIdIntTest(): void {
        $u = $this->makeUser('Ana', 'ana@example.com');
        $found = $this->User->Find($u->id);
        $this->assertEquals($u->id, $found->id);
    }

    public function findByConditionsTest(): void {
        $this->makeUser('Ana', 'ana@example.com', 20, 1);
        $this->makeUser('Bob', 'bob@example.com', 20, 0);
        $active = $this->User->Find(['conditions' => "`active`=1"]);
        $this->assertEquals(1, $active->counter());
    }

    public function findWithSortTest(): void {
        $this->makeUser('Zoe', 'zoe@example.com');
        $this->makeUser('Ana', 'ana@example.com');
        $sorted = $this->User->Find(['sort' => 'name ASC']);
        $this->assertEquals('Ana', $sorted[0]->name);
    }

    public function findWithLimitTest(): void {
        $this->makeUser('Ana', 'ana@example.com');
        $this->makeUser('Bob', 'bob@example.com');
        $this->makeUser('Cid', 'cid@example.com');
        $limited = $this->User->Find(['limit' => 2]);
        $this->assertEquals(2, $limited->counter());
    }

    public function findFirstTest(): void {
        $this->makeUser('Ana', 'ana@example.com');
        $this->makeUser('Bob', 'bob@example.com');
        $first = $this->User->Find([':first']);
        $this->assertEquals(1, $first->counter());
    }

    public function deleteByIdTest(): void {
        $u = $this->makeUser('Ana', 'ana@example.com');
        $this->assertTrue($this->User->Delete($u->id));
        $this->assertEquals(0, $this->User->Find($u->id)->counter());
    }

    public function countTest(): void {
        $this->makeUser('Ana', 'ana@example.com');
        $this->makeUser('Bob', 'bob@example.com');
        $this->assertEquals(2, $this->User->Find()->count());
    }

    public function autoAuditCreatedAtTest(): void {
        $u = $this->makeUser('Ana', 'ana@example.com');
        $this->assertGreaterThan(0, (int) $u->created_at);
    }

    public function autoAuditUpdatedAtTest(): void {
        $u = $this->makeUser('Ana', 'ana@example.com');
        $u->name = 'Anita';
        $u->Save();
        $this->assertGreaterThan(0, (int) $u->updated_at);
    }

    public function rawFieldsTest(): void {
        $fields = $this->User->getRawFields();
        $this->assertTrue(in_array('name', $fields) && in_array('email', $fields) && in_array('age', $fields));
    }

    public function tableNameTest(): void {
        $this->assertEquals('users', $this->User->_TableName());
    }

    public function sqlQueryTest(): void {
        $result = $this->User->Find();
        $this->assertTrue(str_contains($result->_sqlQuery, 'SELECT'));
    }

    public function toStringTest(): void {
        $u = $this->makeUser('Ana', 'ana@example.com');
        $found = $this->User->Find($u->id);
        $this->assertNotEmpty((string) $found);
    }
}
