<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;

/**
 * Verifies soft-delete: a per-model opt-in (public $soft_delete on
 * ActiveRecord, default false) that makes Delete() set deleted_at instead of
 * removing the row, and makes Find() exclude deleted_at>0 rows unless
 * withDeleted=true is passed. 100% backward compatible — models that never
 * set soft_delete=true keep the exact hard-delete behavior from before this
 * feature (see hardDeleteStillWorksTest).
 *
 * CascadeOwner/SoftPost/HardPost are a dedicated fixture set, isolated from
 * the shared User/Post/Comment fixtures used by the rest of the framework's
 * own tests — dependents='destroy' on a shared model would make every
 * Delete() of that model try to cascade-check tables other suites never
 * migrate (confirmed: this broke TestActiveRecord::deleteByIdTest() during
 * development, since it only migrates 'users').
 */
class TestSoftDelete extends dumboTests {

    public function beforeEach(): void {
        $this->_migrateTables(['cascade_owners', 'soft_posts', 'hard_posts']);
    }

    private function makeOwner(string $name) {
        $o = $this->CascadeOwner->Niu(['name' => $name]);
        $o->Save();
        return $o;
    }

    private function makeSoftPost(string $title, $ownerId) {
        $p = $this->SoftPost->Niu(['title' => $title, 'cascade_owner_id' => (string) $ownerId]);
        $p->Save();
        return $p;
    }

    private function makeHardPost(string $title, $ownerId) {
        $p = $this->HardPost->Niu(['title' => $title, 'cascade_owner_id' => (string) $ownerId]);
        $p->Save();
        return $p;
    }

    /** A freshly-constructed model defaults soft_delete to false. */
    public function softDeleteFlagDefaultFalseTest(): void {
        $o = $this->CascadeOwner->Niu();
        $this->assertFalse($o->soft_delete);
    }

    /** Delete() on a soft_delete=true model sets deleted_at instead of removing the row. */
    public function softDeleteSetsTimestampTest(): void {
        $o  = $this->makeOwner('Ana');
        $p  = $this->makeSoftPost('Hello', $o->id);
        $id = $p->id;
        $this->assertTrue($p->Delete());
        $this->assertGreaterThan(0, (int) $p->deleted_at);

        // The row must still physically exist — only withDeleted=true can see it.
        $raw = $this->SoftPost->Find(['conditions' => "`id`={$id}", 'withDeleted' => true]);
        $this->assertEquals(1, $raw->counter());
    }

    /** A soft-deleted record disappears from a normal Find(). */
    public function softDeleteExcludedFromFindTest(): void {
        $o  = $this->makeOwner('Ana');
        $p  = $this->makeSoftPost('Hello', $o->id);
        $id = $p->id;
        $p->Delete();

        $found = $this->SoftPost->Find($id);
        $this->assertEquals(0, $found->counter());

        $all = $this->SoftPost->Find();
        $this->assertEquals(0, $all->counter());
    }

    /** Find(withDeleted: true) is the explicit escape hatch to see soft-deleted rows. */
    public function softDeleteWithDeletedParamTest(): void {
        $o  = $this->makeOwner('Ana');
        $p  = $this->makeSoftPost('Hello', $o->id);
        $id = $p->id;
        $p->Delete();

        $withDeleted = $this->SoftPost->Find([
            'conditions'  => "`id`={$id}",
            'withDeleted' => true,
        ]);
        $this->assertEquals(1, $withDeleted->counter());
        $this->assertGreaterThan(0, (int) $withDeleted->deleted_at);
    }

    /**
     * CascadeOwner.dependents='destroy' cascades to its SoftPost children by
     * calling each child's own Delete() — since SoftPost.soft_delete=true,
     * the child ends up soft-deleted (deleted_at set), not physically
     * removed.
     */
    public function softDeleteCascadesToChildrenTest(): void {
        $o = $this->makeOwner('Ana');
        $p = $this->makeSoftPost('Hello', $o->id);
        $childId = $p->id;

        $o->has_many = ['SoftPosts'];
        $this->assertTrue($o->Delete());

        $raw = $this->SoftPost->Find(['conditions' => "`id`={$childId}", 'withDeleted' => true]);
        $this->assertEquals(1, $raw->counter(), 'The child row must still exist physically');
        $this->assertGreaterThan(0, (int) $raw->deleted_at, 'The child must be marked as soft-deleted');
    }

    /**
     * Same cascade (dependents='destroy'), but the child (HardPost) does NOT
     * declare soft_delete — it must be physically removed, exactly like
     * before this feature existed.
     */
    public function softDeleteChildHardDeleteTest(): void {
        $o = $this->makeOwner('Ana');
        $p = $this->makeHardPost('Hello', $o->id);
        $childId = $p->id;

        $o->has_many = ['HardPosts'];
        $this->assertTrue($o->Delete());

        $this->assertEquals(0, $this->HardPost->Find($childId)->counter());
    }

    /** A model without soft_delete=true keeps hard-deleting exactly as before this feature. */
    public function hardDeleteStillWorksTest(): void {
        $o  = $this->makeOwner('Ana');
        $id = $o->id;
        $o->has_many = []; // no cascade needed for this assertion
        $this->assertTrue($o->Delete());
        $this->assertEquals(0, $this->CascadeOwner->Find($id)->counter());
    }
}
