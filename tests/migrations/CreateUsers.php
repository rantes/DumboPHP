<?php
namespace Migrations;

use DumboPHP\Migrations;

class CreateUsers extends Migrations {
    public function _init_() {
        $this->_fields = [
            ['field' => 'id',         'type' => 'INTEGER', 'primary' => true, 'autoincrement' => true],
            ['field' => 'name',       'type' => 'VARCHAR', 'limit' => 100, 'null' => false],
            ['field' => 'email',      'type' => 'VARCHAR', 'limit' => 150, 'null' => false],
            ['field' => 'age',        'type' => 'INTEGER', 'default' => 0],
            ['field' => 'active',     'type' => 'INTEGER', 'default' => 1],
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
