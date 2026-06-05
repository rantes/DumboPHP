<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;

/**
 * Verifies model relations.
 *
 * belongs_to works correctly. has_many is currently broken for namespaced
 * models (every DumboPHP model is namespaced) — see .kiro/bugs/BUG-001. The
 * has_many cases below are characterization tests that assert the present
 * broken behavior; when BUG-001 is fixed they will start failing and should be
 * rewritten as positive assertions ($user->Posts()->counter() === N).
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
     * BUG-001: $user->Posts() resolves the related class as App\Models\Posts
     * (plural) which does not exist, so an Error is thrown.
     */
    public function hasManyThrowsBug001Test(): void {
        $this->describe('has_many broken for namespaced models — see .kiro/bugs/BUG-001');
        [$user] = $this->makeUserWithPost();
        $threw = false;
        try {
            $user->Posts();
        } catch (\Throwable $e) {
            $threw = true;
        }
        $this->assertTrue($threw);
    }

    /**
     * BUG-001: same root cause from the Post side ($post->Comments()).
     */
    public function hasManyNestedThrowsBug001Test(): void {
        [, $post] = $this->makeUserWithPost();
        $threw = false;
        try {
            $post->Comments();
        } catch (\Throwable $e) {
            $threw = true;
        }
        $this->assertTrue($threw);
    }
}
