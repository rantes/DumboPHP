<?php
namespace App\Models;

use DumboPHP\ActiveRecord;

class Post extends ActiveRecord {
    public ?string $title   = null;
    public ?string $body    = null;
    public ?string $user_id = null;

    public function _init_(): void {
        $this->validate['presence_of'] = ['title', 'user_id'];
        $this->belongs_to              = ['User'];
        $this->has_many                = ['Comments'];
    }
}
