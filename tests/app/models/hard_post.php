<?php
namespace App\Models;

use DumboPHP\ActiveRecord;

/**
 * Fixture model WITHOUT soft_delete (default false) — a has_many child of
 * CascadeOwner used to verify the cascade still hard-deletes children that
 * don't opt in to soft-delete.
 */
class HardPost extends ActiveRecord {
    public ?string $title            = null;
    public ?string $cascade_owner_id = null;

    public function _init_(): void {
        $this->validate['presence_of'] = ['title', 'cascade_owner_id'];
        $this->belongs_to              = ['CascadeOwner'];
    }
}
