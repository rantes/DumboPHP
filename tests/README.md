# DumboPHP — Self-Test Suite

This directory is a **self-contained mini-application** that uses DumboPHP to
test DumboPHP itself. It is not a normal project — it is the framework's own
verification harness.

`INST_PATH` is set to `tests/`, so the framework finds `app/models/`,
`app/controllers/`, `app/views/` and `migrations/` under here. The whole
environment runs against SQLite `:memory:` for speed and isolation.

## Running

```bash
# Level 0 — verify Timothy itself, in plain PHP (no Timothy). Run this first.
php tests/verify_timothy.php          # exit 0 = OK, exit code = number of failures

# Level 1 — run every suite
php tests/run.php

# Run specific suites
php tests/run.php TestActiveRecord TestRelations

# Options
php tests/run.php --verbose           # per-assertion P/F progress
php tests/run.php --halt               # stop at the first failure
```

`php tests/run.php` exits 0 when all suites pass, non-zero otherwise, and prints
the captured log on failure.

> **Why `run.php` and not the stock `dumboTest` binary?**
> `dumboTest` pins `INST_PATH` to the current working directory and loads the
> **system-installed** framework from `/etc/dumbophp`, not the working copy in
> this repo. `run.php` bootstraps `INST_PATH=tests/` and loads `bin/dumbophp.php`
> from the working tree, so it tests the code you are actually editing.

## Layout

```
tests/
  app/
    models/{user,post,comment}.php   fixture models (validations, relations, hooks)
    controllers/test_controller.php  fixture controller (routing, params, JSON)
    views/{layout.phtml,test/*.phtml}
  migrations/Create{Users,Posts,Comments}.php
  seeds/Seeds.php
  config/{host,db_settings}.php      test env: SQLite :memory:
  .env  .env.secrets
  suites/Test*.php                   the suites (namespace `tests`)
  bootstrap.php                      defines INST_PATH, loads framework + autoloader, builds DB
  run.php                            entry point (no XDebug required)
  verify_timothy.php                 Level 0 — plain-PHP Timothy self-check
```

## Suites

| Suite | Covers |
| ----- | ------ |
| `TestQueryCondition`  | `QueryCondition` connector validation, `__toString`, md5 key |
| `TestIrregularNouns`  | `Plurals`/`Singulars`/`Camelize`/`unCamelize` inflection |
| `TestHelperFunctions` | `uuidV4`, `getallheaders`, `cleanToSEO`, `strGenerate` |
| `TestActiveRecord`    | `Niu`/`Save` (insert+update), `Find` (id/conditions/sort/limit/:first), `Delete`, counting, audit timestamps, field introspection |
| `TestValidations`     | `validate['presence_of']` enforcement and the `_error` bag |
| `TestHooks`           | `before_save`/`before_insert`/`after_insert`/`after_update` order + short-circuit |
| `TestRelations`       | `belongs_to` and `has_many` (both work; FWK-001 fixed) |
| `TestMigrations`      | create/drop/reset tables, `getFields`/`getDefinitions`, schema-sync asserts |
| `TestController`      | dispatch via `_runAction`, lazy model load, params, missing action, JSON |
| `TestRouting`         | URL → controller/action/param resolution |
| `TestTimothy`         | Phase 3: spies, stubs, mocks, per-test spy-log isolation |

## Writing a suite

Suites live in `suites/`, use namespace `tests`, extend `dumboTests`, and run
every method whose name matches `…Test`. Use `beforeEach()` to (re)migrate
tables for a clean state:

```php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;

class TestExample extends dumboTests {
    public function beforeEach(): void {
        $this->_migrateTables(['users']);   // drop + create each test
    }

    public function savesAUserTest(): void {
        $u = $this->User->Niu(['name' => 'Ana', 'email' => 'ana@example.com']);
        $this->assertTrue($u->Save());
        $this->assertGreaterThan(0, $u->id);
    }
}
```

### Assertions

`assertEquals` (strict `===`), `assertTrue`, `assertFalse`, `assertNotEmpty`,
`assertNotFalse`, `assertGreaterThan($initial, $val)`, `assertArrayHasKey`,
and the ORM-aware `assertHasFields($model)` / `assertHasFieldTypes($model)`.

### Spies, stubs and mocks (Timothy, Phase 3)

```php
// Spy: record calls, still delegate to the real method
$this->spyOn($obj, 'doThing');
$obj->doThing('x');
$this->assertMethodHasBeenCalled('doThing', 1);
$this->assertMethodCalledWith('doThing', ['x']);

// Stub: force a return value for one method
$this->stubMethod($obj, 'compute', 42);

// Mock: duck-typed stand-in that records calls and returns configured values
$mock = $this->createMock('App\\Models\\User', ['Find' => $fakeResult]);
$mock->Find();                       // returns $fakeResult, call recorded
```

`spyOn`, `stubMethod` and `createMock` are reference-based proxies built on
`__call`; the recorded calls live in `$this->_spyCalls` and are reset by the
dispatcher before every test. Because they intercept through `__call`, they
cover duck-typed collaborators (DumboPHP's style) — a generic spy over an
already-defined concrete method is not possible without `uopz`.

## Known framework issues found by this suite

- **FWK-001** (Cerrado) — `has_many` was broken for namespaced models. Fixed in
  `Core_General_Class::__call()`. See
  [`.kiro/bugs/FWK-001-has-many-namespaced-models.md`](../.kiro/bugs/FWK-001-has-many-namespaced-models.md).
  `TestRelations` now locks in the fix with positive assertions.

Two smaller quirks are documented inline in the suites:

- Views and layouts are loaded with `include_once`, so a given view renders only
  once per process — output-based assertions test controller **state**
  (properties the action sets) instead, with one dedicated render test.
- `strGenerate()` is typed `?string $params` but reads `$params` as an array, so
  only the no-arg/default path is callable.
