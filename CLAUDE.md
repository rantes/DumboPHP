# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this repository is

This is the **source of the DumboPHP framework itself**, not an application built with it.
The global rules in `~/.claude/CLAUDE.md` and `~/.claude/rules/*` describe how to *use* DumboPHP
to build apps (controllers, models, scaffolding, conventions). Those still apply to any fixture
code under `tests/`, but most work in this repo is *developing the framework internals* described below.

Released as a Composer package (`rantes/dumbophp`, MIT). PHP 8.1+, only `ext-pdo` and `ext-json`.
No runtime dependencies — never add Composer packages the framework loads at runtime.

## Repository layout

- `bin/dumbophp.php` — **the monolithic framework core** (~3000 lines, namespace `DumboPHP`).
  Contains every core class: `Connection` (PDO wrapper), `Config`/`Secrets`, `Core_General_Class`
  (extends `ArrayObject`), `ActiveRecord` (the ORM, lines ~942–2313), `Controller`, `Migrations`,
  `QueryCondition`, `IrregularNouns`, the `index` front controller, plus global helper functions
  (`Plurals`, `Singulars`, `Camelize`, `unCamelize`, `cleanToSEO`, `strGenerate`, `uuidV4`).
  Almost all framework behavior lives in this one file — search here first.
- `bin/dumbo` — CLI entry point (project management). Sets the include path, registers the
  namespace autoloader, then runs `DumboPHP\lib\Shell`.
- `bin/dumboTest` — test runner entry point. Forces `APP_ENV=test`, requires the XDebug extension
  (for coverage), and drives `lib/Timothy/testDispatcher`.
- `lib/Shell.php` + `lib/ShellCommands/` — the CLI command layer. Each subcommand
  (`create`, `generate`, `destroy`, `migration`, `db`, `run`, `help`, `autocomplete`) is a class
  implementing `Interfaces/DumboCommand` (`execute(array $args, array $options): void` + `help()`),
  instantiated and dispatched by `Shell`. Add a new CLI command by creating a `*Command` class here
  and wiring it into `Shell::__construct` / the dispatch switch.
- `lib/db_drivers/` — per-driver SQL generation (`mysql.php`, `postgresql.php`, `sqlite.php`),
  implementing `Interfaces/DBDriver`. This is where dialect-specific DDL/DML differences live.
- `lib/DumboGeneratorClass.php` — code generation used by `generate`/scaffold (models, migrations,
  controllers, views) via heredoc templates.
- `lib/Timothy/` — the in-house test framework: `dumboTests` (assertions, extends `Controller`)
  and `testDispatcher` (discovers/runs suites, emits JUnit XML + Clover coverage).
- `src/` — **project skeleton templates** copied verbatim when a user runs `dumbo create`
  (`host.php`, `db_settings.php`, `index.php`, `layout.phtml`, `.htaccess`, `.env.example`).
  Editing these changes what new projects are scaffolded with.
- `install.php` — system installer: copies `src/`, `bin/`, `lib/` into `/etc/dumbophp` and symlinks
  `dumbo`/`dumboTest` into `/usr/local/bin` (run `sudo ./install.php`).
- `.kiro/steering/` — the authoritative spec/convention docs (mirror of the global rules, plus
  `08-testing-framework.md`). `.kiro/specs/` — active work specs (e.g. the test-suite buildout).

## Autoloading and namespaces

The autoloader (defined inline in `bin/dumbo` and `bin/dumboTest`) maps namespaces to paths:

- `DumboPHP\...` → resolved on the include path (`lib/`, `bin/`, `/etc/dumbophp`, `vendor/...`).
- `App\Models\X` / `App\Controllers\X` → `app/models/x.php` etc., applying `unCamelize()` to each
  segment (so `UserProfile` → `user_profile.php`).
- `Migrations\CreateX` → `migrations/create_x.php`.

When running inside this repo's own test environment, the autoloader instead maps `App\...`,
`Migrations\`, and `tests\` into the corresponding `tests/` subdirectories.

## Testing the framework

Testing is **two-level** (see `.kiro/steering/08-testing-framework.md`). The `tests/` directory is a
self-contained mini-app that uses DumboPHP to test DumboPHP, with its own `config/` (SQLite
`:memory:` only), `migrations/`, `models/`, and `controllers/` as fixtures.

```bash
# Level 0 — verify Timothy itself, using pure PHP (no Timothy). Must pass first.
php tests/verify_timothy.php          # exit 0 = OK, exit code = number of failures

# Level 1 — run the suites (require XDebug installed for coverage)
./bin/dumboTest all --dir=tests/suites/
./bin/dumboTest SomeSuite             # run specific suite class(es), space-separated
./bin/dumboTest all --halt=true       # stop at first failure
./bin/dumboTest all --watch=true      # re-run on file save
```

`dumboTest` writes `test-result.xml` (JUnit), `coverage.xml` (Clover), and
`tmp/logs/unit_testing.log`. Note that parts of the `tests/` scaffolding described in the steering
doc (`suites/`, `config/`, `models/`, `bootstrap.php`) are part of an in-progress spec and may not
all exist yet — check before assuming.

Test suites extend `dumboTests` (namespace `tests`). Assertions available include `assertEquals`,
`assertTrue/False`, `assertArrayHasKey`, `assertNotEmpty`, `assertNotFalse`, `assertGreaterThan`,
plus ORM-aware `assertHasFields(ActiveRecord)` / `assertHasFieldTypes(ActiveRecord)`. Use
`_init_()` / `beforeEach()` (not `__construct`), and `_migrateTables()` / `_dropTables()` /
`_truncateTables()` to manage fixture state per test.

## Core conventions when editing the framework

- Use `_init_()` instead of `__construct()` for `ActiveRecord`, `Controller`, `Migrations`, and test
  subclasses — `Core_General_Class` calls `_init_()` for you; overriding `__construct` breaks setup.
- The CLI must run from a project root (where `config/host.php` exists). Use `--standalone=true` to
  run without a project's `vendor/`.
- Short-circuit operator idiom (`cond and $this->do()`, `cond or die()`) is used throughout the core;
  match it. Views use PHP short tags and alternative syntax (`<? ... ?>`, `<?= ?>`, `endforeach`).
- Pluralization/singularization is convention-critical (table ↔ model name mapping). Irregular cases
  live in the `IrregularNouns` class; extend it there rather than special-casing call sites.
- The framework detects CLI vs HTTP via `_IN_SHELL_` and parses `$argv` into `$_GET` for shell runs.
