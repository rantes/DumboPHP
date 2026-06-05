<?php
namespace Migrations;

use DumboPHP\Migrations;

class CreatePosts extends Migrations {
    public function _init_() {
        $this->_fields = [
            ['field' => 'id',         'type' => 'INTEGER', 'primary' => true, 'autoincrement' => true],
            ['field' => 'title',      'type' => 'VARCHAR', 'limit' => 255, 'null' => false],
            ['field' => 'body',       'type' => 'TEXT'],
            ['field' => 'user_id',    'type' => 'INTEGER', 'null' => false, 'default' => 0],
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
