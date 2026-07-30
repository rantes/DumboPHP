<?php
namespace Migrations;

use DumboPHP\Migrations;

/**
 * Fixture table for TestSoftDelete — a dependents='destroy' parent, isolated
 * from the shared User/Post fixtures so this suite cannot regress the rest
 * of the framework's own tests (see the beforeEach() tables they migrate).
 */
class CreateCascadeOwners extends Migrations {
    public function _init_() {
        $this->_fields = [
            ['field' => 'id',         'type' => 'INTEGER', 'primary' => true, 'autoincrement' => true],
            ['field' => 'name',       'type' => 'VARCHAR', 'limit' => 100, 'null' => false],
            ['field' => 'created_at', 'type' => 'INTEGER'],
            ['field' => 'updated_at', 'type' => 'INTEGER'],
        ];
    }

    public function up() {
        $this->Create_Table();
    }

    public function down() {
        $this->Drop_Table();
    }
}
