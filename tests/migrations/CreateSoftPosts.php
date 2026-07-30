<?php
namespace Migrations;

use DumboPHP\Migrations;

/**
 * Fixture table for TestSoftDelete — a has_many child of CascadeOwner whose
 * model declares soft_delete=true, used to verify cascade behavior
 * (dependents='destroy' calling the child's own Delete(), which respects
 * its own flag).
 */
class CreateSoftPosts extends Migrations {
    public function _init_() {
        $this->_fields = [
            ['field' => 'id',               'type' => 'INTEGER', 'primary' => true, 'autoincrement' => true],
            ['field' => 'title',            'type' => 'VARCHAR', 'limit' => 255, 'null' => false],
            ['field' => 'cascade_owner_id', 'type' => 'INTEGER', 'null' => false, 'default' => 0],
            ['field' => 'deleted_at',       'type' => 'INTEGER', 'default' => 0],
            ['field' => 'created_at',       'type' => 'INTEGER'],
            ['field' => 'updated_at',       'type' => 'INTEGER'],
        ];
    }

    public function up() {
        $this->Create_Table();
    }

    public function down() {
        $this->Drop_Table();
    }
}
