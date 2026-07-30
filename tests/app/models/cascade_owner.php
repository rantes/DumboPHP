<?php
namespace App\Models;

use DumboPHP\ActiveRecord;

/**
 * Fixture model exclusively used by TestSoftDelete to exercise
 * dependents='destroy' cascade — isolated from User/Post/Comment so this
 * suite cannot regress the rest of the framework's own tests.
 */
class CascadeOwner extends ActiveRecord {
    public ?string $name = null;

    public function _init_(): void {
        $this->validate['presence_of'] = ['name'];
        $this->has_many                = ['SoftPosts', 'HardPosts'];
        $this->dependents              = 'destroy';
    }
}
