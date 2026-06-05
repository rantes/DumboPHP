<?php
namespace App\Models;

use DumboPHP\ActiveRecord;

class Comment extends ActiveRecord {
    public ?string $content = null;
    public ?string $post_id = null;

    public function _init_(): void {
        $this->validate['presence_of'] = ['content', 'post_id'];
        $this->belongs_to              = ['Post'];
    }
}
