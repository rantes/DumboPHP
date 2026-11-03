<?php
namespace App\Models;

use DumboPHP\ActiveRecord;

/**
 * Fixture model over the typed_things table that redeclares only one of the messages of the type validation.
 * Not a domain model — it exists to drive the framework's own test suite.
 */
class TypedThingPartial extends ActiveRecord {
    protected string $_ObjTable = 'typed_things';
    public $_typeMessages       = ['int' => 'Solo enteros.'];
    public ?int $qty            = null;
    public ?float $price        = null;
    public ?string $name        = null;

    public function _init_(): void {
    }
}
