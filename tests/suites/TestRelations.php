<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;

/**
 * Verifies model relations.
 *
 * belongs_to and has_many both work correctly, including for namespaced models
 * (every DumboPHP model is namespaced). has_many was previously broken — see
 * .kiro/bugs/BUG-001, now Cerrado — and the cases below are the positive
 * assertions that lock in the fix.
 */
class TestRelations extends dumboTests {

    public function beforeEach(): void {
        $this->_migrateTables(['users', 'posts', 'comments']);
    }

    /** Create a user + a post that belongs to it, returning [user, post]. */
    private function makeUserWithPost(): array {
        $user = $this->User->Niu(['name' => 'Ana', 'email' => 'ana@example.com']);
        $user->Save();
        $post = $this->Post->Niu(['title' => 'Hello', 'user_id' => (string) $user->id]);
        $post->Save();
        return [$user, $post];
    }

    public function belongsToTest(): void {
        [$user, $post] = $this->makeUserWithPost();
        $owner = $post->User();
        $this->assertEquals('Ana', $owner->name);
    }

    public function belongsToNestedTest(): void {
        [, $post] = $this->makeUserWithPost();
        $comment  = $this->Comment->Niu(['content' => 'Nice', 'post_id' => (string) $post->id]);
        $comment->Save();
        $owner = $comment->Post();
        $this->assertEquals('Hello', $owner->title);
    }

    /**
     * BUG-001 (Cerrado): $user->Posts() resolves App\Models\Post and filters by
     * user_id, returning the user's posts.
     */
    public function hasManyTest(): void {
        [$user] = $this->makeUserWithPost();
        $posts  = $user->Posts();
        $this->assertEquals(1, $posts->counter());
        $this->assertEquals('Hello', $posts->first()->title);
    }

    /**
     * BUG-001 (Cerrado): same path from the Post side ($post->Comments()).
     */
    public function hasManyNestedTest(): void {
        [, $post] = $this->makeUserWithPost();
        $comment  = $this->Comment->Niu(['content' => 'Nice', 'post_id' => (string) $post->id]);
        $comment->Save();
        $comments = $post->Comments();
        $this->assertEquals(1, $comments->counter());
        $this->assertEquals('Nice', $comments->first()->content);
    }
}
