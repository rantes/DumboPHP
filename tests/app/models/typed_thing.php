<?php
namespace App\Models;

use DumboPHP\ActiveRecord;

/**
 * Fixture model with properties typed after their columns (?int, ?float, ?string).
 * Not a domain model — it exists to drive the framework's own test suite.
 */
class TypedThing extends ActiveRecord {
    public ?int $qty      = null;
    public ?float $price  = null;
    public ?string $name  = null;
    public ?float $amount = null;

    public function _init_(): void {
    }
}
