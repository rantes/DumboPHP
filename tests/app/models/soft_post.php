<?php
namespace App\Models;

use DumboPHP\ActiveRecord;

/**
 * Fixture model with soft_delete=true, exclusively used by TestSoftDelete —
 * standalone (Delete()/Find() behavior) and as a has_many child of
 * CascadeOwner (dependents='destroy' cascade respecting its own flag).
 */
class SoftPost extends ActiveRecord {
    public ?string $title            = null;
    public ?string $cascade_owner_id = null;

    public $soft_delete = true;

    public function _init_(): void {
        $this->validate['presence_of'] = ['title', 'cascade_owner_id'];
        $this->belongs_to              = ['CascadeOwner'];
    }
}
