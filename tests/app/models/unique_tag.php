<?php
namespace App\Models;

use DumboPHP\ActiveRecord;

/**
 * Fixture: valida unicidad de `name` (validate['unique'] arma una consulta con el valor del usuario dentro de Save()).
 */
class UniqueTag extends ActiveRecord {
    public ?string $name = null;

    public function _init_(): void {
        $this->validate['unique'] = [['field' => 'name', 'message' => 'duplicated']];
    }
}
