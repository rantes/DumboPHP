<?php
namespace App\Models;

use DumboPHP\ActiveRecord;

/**
 * Fixture model exercising validations, has_many, and the full hook lifecycle.
 * Not a domain model — it exists to drive the framework's own test suite.
 */
class User extends ActiveRecord {
    public ?string $name   = null;
    public ?string $email  = null;
    public ?string $age    = null;
    public ?string $active = null;

    // Hook instrumentation (not table columns — never persisted).
    public array $hookOrder = [];
    public bool $failOnSave = false;

    public function _init_(): void {
        $this->validate['presence_of'] = ['name', 'email'];
        $this->has_many                = ['Posts'];

        $this->before_save   = ['sanitizeName', 'firstBeforeSave', 'secondBeforeSave'];
        $this->before_insert = ['trackBeforeInsert'];
        $this->after_insert  = ['trackAfterInsert'];
        $this->after_update  = ['trackAfterUpdate'];
    }

    public function sanitizeName(): void {
        $this->name = htmlentities((string) $this->name);
    }

    public function firstBeforeSave(): void {
        $this->hookOrder[] = 'first';
        $this->failOnSave and $this->_error->add(['field' => 'name', 'message' => 'forced failure']);
    }

    public function secondBeforeSave(): void {
        $this->hookOrder[] = 'second';
    }

    public function trackBeforeInsert(): void {
        $this->hookOrder[] = 'before_insert';
    }

    public function trackAfterInsert(): void {
        $this->hookOrder[] = 'after_insert';
    }

    public function trackAfterUpdate(): void {
        $this->hookOrder[] = 'after_update';
    }
}
