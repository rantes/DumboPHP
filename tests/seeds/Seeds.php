<?php
namespace Migrations;

use App\Models\User;

/**
 * Minimal seeder for the self-test environment. Reached via dumboTests::_sow().
 * Tables must already exist (migrate first); seeds are optional — no suite
 * depends on them, they are provided as a usage example.
 */
class Seeds {
    public function sow(?array $actions = []): void {
        $user = (new User())->Niu(['name' => 'Seed User', 'email' => 'seed@example.com']);
        $user->Save();
    }
}
