<?php
namespace App\Models;

use DumboPHP\ActiveRecord;

/**
 * Fixture model over the typed_things table that redeclares the messages of the type validation.
 * Not a domain model — it exists to drive the framework's own test suite.
 */
class TypedThingMsg extends ActiveRecord {
    protected string $_ObjTable = 'typed_things';
    public $_typeMessages       = [
        'int'    => 'Solo enteros.',
        'float'  => 'Solo números.',
        'string' => 'Solo texto.',
    ];
    public ?int $qty            = null;
    public ?float $price        = null;
    public ?string $name        = null;

    public function _init_(): void {
    }
}
