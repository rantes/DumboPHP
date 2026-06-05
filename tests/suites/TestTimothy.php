<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;

/**
 * Verifies the Timothy improvements (Phase 3) through the real dispatcher:
 * spies, stubs, duck-typed mocks, the interaction assertions, and per-test
 * isolation of the spy call log (reset by testDispatcher before each test).
 */
class TestTimothy extends dumboTests {

    public function spyOnRecordsAndDelegatesTest(): void {
        $obj = new class {
            public function greet($name) {
                return "hi {$name}";
            }
        };
        $this->spyOn($obj, 'greet');

        $ret = $obj->greet('Ana');
        $this->assertEquals('hi Ana', $ret);          // delegated to the real method
        $this->assertMethodHasBeenCalled('greet', 1);  // and recorded
        $this->assertMethodCalledWith('greet', ['Ana']);
    }

    public function stubMethodTest(): void {
        $obj = new class {
            public function compute() {
                return 1;
            }
        };
        $this->stubMethod($obj, 'compute', 42);
        $this->assertEquals(42, $obj->compute());
    }

    public function createMockReturnsStubTest(): void {
        $mock = $this->createMock('App\\Models\\User', ['Find' => 'stubbed']);
        $this->assertEquals('stubbed', $mock->Find(['conditions' => '1=1']));
        $this->assertMethodHasBeenCalled('Find', 1);
        $this->assertMethodCalledWith('Find', [['conditions' => '1=1']]);
    }

    /**
     * Proves the dispatcher clears _spyCalls before each test: this test runs
     * after the recording tests above, yet starts with an empty call log.
     */
    public function spyCallsAreIsolatedPerTestTest(): void {
        $this->assertEquals([], $this->_spyCalls);
    }
}
