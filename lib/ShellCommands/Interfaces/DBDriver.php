<?php
namespace DumboPHP\lib\ShellCommands\Interfaces;

interface DBDriver {
    public function AddColumn(string $table, array $params): string;
    public function AddIndex(string $table, string $name, string $fields): string;
    public function AddPrimaryKey(string $table, string $fields): string;
    public function AddSingleIndex(string $table, string $field): string;
    public function AlterColumn(string $table, array $params): string;
    public function CreateTable(string $table, array $fields): string;
    public function Delete(array | string | int $conditions, string $table, string $pk = 'id'): string;
    public function DropTable(string $table): string;
    public function GetAllIndexes(string $table, string $schema): string;
    public function getColumns(string $table): string;
    public function Insert(array $params, string $table, bool $replace = false): array;
    public function RemoveColumn(string $table, string $field): string;
    public function RemoveIndex(string $table, string $index): string;
    public function RowCountOnQuery(string $query): string;
    public function Select(array | string | int $params, string $table, string $pk = 'id'): array;
    public function Update(array $params, string $table, string $pk = 'id'): array;
    public function validateField(string $table, string $field, string $schema): string;
    public function ValidateIndex(string $table, string $index, string $schema): string;
}