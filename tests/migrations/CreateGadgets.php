<?php
namespace Migrations;

use DumboPHP\Migrations;

/**
 * Fixture for TestMigrationsAlter: exposes the protected schema-alteration operations.
 */
class CreateGadgets extends Migrations {
    public function _init_() {
        $this->_fields = [
            ['field' => 'id',   'type' => 'INTEGER', 'primary' => true, 'autoincrement' => true],
            ['field' => 'name', 'type' => 'VARCHAR', 'limit' => 100, 'null' => false],
        ];
    }

    public function up() {
        $this->Create_Table();
    }

    public function down() {
        $this->Drop_Table();
    }

    public function addColumn(array $params): void {
        $this->Add_Column($params);
    }

    public function alterColumn(array $params): void {
        $this->Alter_Column($params);
    }

    public function removeColumn(string $field): void {
        $this->Remove_Column($field);
    }

    public function addSingleIndex(string $field): void {
        $this->Add_Single_Index($field);
    }
}
