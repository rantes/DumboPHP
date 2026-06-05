<?php
namespace Migrations;

use DumboPHP\Migrations;

class CreateComments extends Migrations {
    public function _init_() {
        $this->_fields = [
            ['field' => 'id',         'type' => 'INTEGER', 'primary' => true, 'autoincrement' => true],
            ['field' => 'content',    'type' => 'TEXT', 'null' => false],
            ['field' => 'post_id',    'type' => 'INTEGER', 'null' => false, 'default' => 0],
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
