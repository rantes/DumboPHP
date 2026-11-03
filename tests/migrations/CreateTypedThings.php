<?php
namespace Migrations;

use DumboPHP\Migrations;

class CreateTypedThings extends Migrations {
    public function _init_() {
        $this->_fields = [
            ['field' => 'id',         'type' => 'INTEGER', 'primary' => true, 'autoincrement' => true],
            ['field' => 'qty',        'type' => 'INTEGER', 'default' => 0],
            ['field' => 'price',      'type' => 'FLOAT', 'default' => 0],
            ['field' => 'name',       'type' => 'VARCHAR', 'limit' => 100],
            ['field' => 'amount',     'type' => 'DECIMAL', 'limit' => '10,2', 'default' => 0],
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
