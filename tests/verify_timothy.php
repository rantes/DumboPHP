<?php
/**
 * Phase 0 — Timothy self-check, written in PLAIN PHP (no Timothy assertions).
 *
 * This script must pass before any Timothy-based suite can be trusted. It drives
 * dumboTests directly and verifies, with native `===`, that:
 *   - each assertion increments the `assertions` counter,
 *   - passing inputs increment `_passed` and leave `_failed` untouched,
 *   - failing inputs increment `_failed`,
 *   - helper plumbing (invokeMethod, describe, resetSuperglobals, spies/mocks) works.
 *
 * Exit code 0 = Timothy behaves correctly; >0 = number of self-check failures.
 *
 * NOTE: the intentional "failing" assertions below print "ERROR Failed to ..."
 * lines to stdout — that is Timothy reporting the failure we asked for, not a
 * problem with this script. The final SELF-CHECK line is what matters.
 */
define('INST_PATH', __DIR__ . '/');
$GLOBALS['env'] = 'test';

set_include_path(
    dirname(__DIR__) . PATH_SEPARATOR .
    dirname(__DIR__) . '/bin' . PATH_SEPARATOR .
    dirname(__DIR__) . '/lib' . PATH_SEPARATOR .
    get_include_path()
);

require_once 'dumbophp.php';
require_once INST_PATH . 'config/host.php';

spl_autoload_register(function (string $class): void {
    $parts = explode('\\', $class);
    if ($parts[0] === 'DumboPHP') {
        require_once implode('/', array_slice($parts, 1)) . '.php';
    }
});

use DumboPHP\lib\Timothy\dumboTests;

$passed = 0;
$failed = 0;

/** Record one self-check result. */
$check = function (string $label, bool $ok) use (&$passed, &$failed): void {
    $ok ? $passed++ : $failed++;
    fwrite(STDOUT, ($ok ? "  ok   " : " FAIL  ") . $label . "\n");
};

$t = new dumboTests('/dev/null');

/** Run a Timothy assertion and return the deltas it produced. */
$probe = function (callable $fn) use ($t): array {
    $a = $t->assertions;
    $p = $t->_passed;
    $f = $t->_failed;
    $fn();
    return [
        'assertions' => $t->assertions - $a,
        'passed'     => $t->_passed - $p,
        'failed'     => $t->_failed - $f,
    ];
};

fwrite(STDOUT, "Timothy self-check (Phase 0)\n----------------------------\n");

// assertEquals
$d = $probe(fn() => $t->assertEquals('a', 'a'));
$check('assertEquals(equal): assertions+1, passed+1, failed+0', $d === ['assertions' => 1, 'passed' => 1, 'failed' => 0]);
$d = $probe(fn() => $t->assertEquals('a', 'b'));
$check('assertEquals(differ): assertions+1, passed+0, failed+1', $d === ['assertions' => 1, 'passed' => 0, 'failed' => 1]);

// assertTrue
$d = $probe(fn() => $t->assertTrue(true));
$check('assertTrue(true): passes', $d === ['assertions' => 1, 'passed' => 1, 'failed' => 0]);
$d = $probe(fn() => $t->assertTrue(false));
$check('assertTrue(false): fails', $d['failed'] === 1);
$d = $probe(fn() => $t->assertTrue(1));
$check('assertTrue(1): fails (strict)', $d['failed'] === 1);

// assertFalse
$d = $probe(fn() => $t->assertFalse(false));
$check('assertFalse(false): passes', $d === ['assertions' => 1, 'passed' => 1, 'failed' => 0]);
$d = $probe(fn() => $t->assertFalse(true));
$check('assertFalse(true): fails', $d['failed'] === 1);

// assertNotEmpty
$d = $probe(fn() => $t->assertNotEmpty('x'));
$check('assertNotEmpty("x"): passes', $d['passed'] === 1 && $d['failed'] === 0);
$d = $probe(fn() => $t->assertNotEmpty(''));
$check('assertNotEmpty(""): fails', $d['failed'] === 1);
$d = $probe(fn() => $t->assertNotEmpty([]));
$check('assertNotEmpty([]): fails', $d['failed'] === 1);

// assertGreaterThan(initial, val) -> val > initial
$d = $probe(fn() => $t->assertGreaterThan(1, 5));
$check('assertGreaterThan(1,5): passes', $d['passed'] === 1 && $d['failed'] === 0);
$d = $probe(fn() => $t->assertGreaterThan(5, 1));
$check('assertGreaterThan(5,1): fails', $d['failed'] === 1);

// assertArrayHasKey
$d = $probe(fn() => $t->assertArrayHasKey('k', ['k' => 1]));
$check('assertArrayHasKey(existing): passes', $d['passed'] === 1 && $d['failed'] === 0);
$d = $probe(fn() => $t->assertArrayHasKey('missing', ['k' => 1]));
$check('assertArrayHasKey(missing): fails', $d['failed'] === 1);

// assertNotFalse
$d = $probe(fn() => $t->assertNotFalse(0));
$check('assertNotFalse(0): passes (0 !== false)', $d['passed'] === 1 && $d['failed'] === 0);
$d = $probe(fn() => $t->assertNotFalse(false));
$check('assertNotFalse(false): fails', $d['failed'] === 1);

// invokeMethod — reach a private method and get its return value
$ref = $t;
$invokeResult = $t->invokeMethod($ref, '_progress', [true]);
$check('invokeMethod(private _progress) returns true', $invokeResult === true);

// describe — string ok, non-string throws
$ok = true;
try { $t->describe('a valid description'); } catch (\Throwable $e) { $ok = false; }
$check('describe(string): no exception', $ok === true);
$threw = false;
try { $t->describe(123); } catch (\Throwable $e) { $threw = true; }
$check('describe(non-string): throws', $threw === true);

// resetSuperglobals
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = ['x' => 1];
$_GET  = ['y' => 2];
$t->resetSuperglobals();
$check('resetSuperglobals: REQUEST_METHOD=GET, POST/GET cleared',
    $_SERVER['REQUEST_METHOD'] === 'GET' && $_POST === [] && $_GET === []);

// createMock + spy bookkeeping
$t->_spyCalls = [];
$mock = $t->createMock('App\\Models\\User', ['Find' => 'stubbed-result']);
$ret  = $mock->Find(['conditions' => '1=1']);
$check('createMock: stubbed return value', $ret === 'stubbed-result');
$check('createMock: call recorded in _spyCalls', ($t->_spyCalls['Find'] ?? null) === [[['conditions' => '1=1']]]);

$d = $probe(fn() => $t->assertMethodHasBeenCalled('Find', 1));
$check('assertMethodHasBeenCalled(Find,1): passes', $d['passed'] === 1 && $d['failed'] === 0);
$d = $probe(fn() => $t->assertMethodCalledWith('Find', [['conditions' => '1=1']]));
$check('assertMethodCalledWith(Find,args): passes', $d['passed'] === 1 && $d['failed'] === 0);

// assertions counter total moved by exactly 1 per assertion call above
$check('assertions counter advanced', $t->assertions > 0);

fwrite(STDOUT, "----------------------------\n");
fwrite(STDOUT, "SELF-CHECK: {$passed} passed, {$failed} failed\n");

// Prevent dumboTests::__destruct() from overriding our exit code with its own
// accumulated _failed count (we intentionally triggered failures above).
$t->_failed = 0;
$t->_passed = 0;

exit($failed);
