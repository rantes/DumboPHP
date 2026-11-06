# DumboPHP #
[![Build Status](https://travis-ci.com/rantes/DumboPHP.svg?branch=master)](https://travis-ci.com/rantes/DumboPHP)
[![Latest Stable Version](https://poser.pugx.org/rantes/dumbophp/v/stable)](https://packagist.org/packages/rantes/dumbophp) [![Total Downloads](https://poser.pugx.org/rantes/dumbophp/downloads)](https://packagist.org/packages/rantes/dumbophp) [![Monthly Downloads](https://poser.pugx.org/rantes/dumbophp/d/monthly)](https://packagist.org/packages/rantes/dumbophp) [![Daily Downloads](https://poser.pugx.org/rantes/dumbophp/d/daily)](https://packagist.org/packages/rantes/dumbophp) [![Latest Unstable Version](https://poser.pugx.org/rantes/dumbophp/v/unstable)](https://packagist.org/packages/rantes/dumbophp) [![License](https://poser.pugx.org/rantes/dumbophp/license)](https://packagist.org/packages/rantes/dumbophp)
![DumboPHP](./logo.png "DumboPHP")
### Summary ###

PHP Framework project built with MVC architecture, OOP paradigm and full ORM (native, not vendor).

### Setup ###

* Get the latest version, clone it or download the zip.
* Unzip if is needed.
* Go to the folder and run the install script:

```
cd /path/to/DumboPHP/
sudo ./install.php
```
#### via composer ####

```
composer require rantes/dumbophp
```

### Server configuration ###

* PHP: Enable short open tags.
* Apache: enable mod_rewrite.

* Consider to set a local domain up with a virtual host).
  - Remember to enable virtual host mod.
  - You can use this config as a sample:
    
```
#!apache

<VirtualHost *:80>
    ServerAdmin webmaster@localhos.com
    ServerName myproject.local
    ServerAlias myproject.local
    DocumentRoot /path/to/myproject
    <Directory /path/to/myproject/>
            Options Indexes FollowSymLinks
            AllowOverride All
            Order allow,deny
            allow from all
    </Directory>

</VirtualHost>
```

```
#!nginx

server {

    root /path/to/myproject/app/webroot;
    index index.php;

    server_name myproject.local;

    set $token "";

    if ($is_args) { # if the request has args update token to "&"
        set $token "&";
    }

    location / {
            set $args "${args}${token}url=${uri}";
            rewrite ^/(.*\.(png|gif|jpg|jpeg|js|pdf|css|ico|svg|json|webp|woff|ttf))$ /$1 break;
            try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/run/php/php7.4-fpm.sock;
    }

    location ~ /\.ht {
            deny all;
    }
}
```

### Testing the framework ###

The framework ships with a self-contained test suite under `tests/` that uses
DumboPHP to test DumboPHP (SQLite `:memory:`, no external dependencies).

```bash
php tests/verify_timothy.php   # Level 0: verify the Timothy test runner itself
php tests/run.php              # Level 1: run every suite
php tests/run.php --verbose    # show per-assertion progress
```

It exercises the ActiveRecord ORM, validations, model hooks, relations,
migrations, routing/controllers, the inflection helpers, and the Timothy
spy/stub/mock helpers. See [`tests/README.md`](tests/README.md) for the full
guide and [`.kiro/bugs/`](.kiro/bugs/) for issues it surfaced.

### Protected reads: `DUMBO_QUOTE_CONDITIONS` ###

By default the ORM builds the `WHERE` of `Find`, `Find_by_*` and `Paginate` by concatenating the values into the SQL
(`field = 'value'`), exactly as before. Writes (`Save`, `Insert`, `Update(data)`) and `Find($id)` have always used bound parameters.

Define the constant in your `config/host.php` to make **reads by value safe** (it is OFF unless you define it as `true`):

```php
define('DUMBO_QUOTE_CONDITIONS', true);
```

The application keeps calling the ORM exactly the same way — no change in `Find`, `Find_by_*` or the array conditions, and the
application must not escape or quote anything itself. With the constant on:

| Piece | Behaviour |
|---|---|
| Values (`[field, value]`, `Find_by_*`) | Bound as named parameters (`:__c0`, `:__c1`, … unique per query, native prepared statements). Quotes, backslashes, `%`, `_`, Unicode, empty strings and double spaces/newlines are matched literally. Numbers and numeric strings both work. |
| `null` | `[f, null]` / `[f, '=', null]` → `f IS NULL`; `[f, '!=', null]` / `'<>'` → `f IS NOT NULL`. `null` with any other operator (`<`, `LIKE`, …) or inside `BETWEEN` throws. **Before, `null` meant `= ''`.** |
| `IN` / `NOT IN` | One placeholder per item (`null` items are skipped; a scalar is a list of one). **Empty list: `IN` → false (`1=0`), `NOT IN` → true (`1=1`).** |
| `BETWEEN` | `[f, 'BETWEEN', a, b]`, both bound, neither may be `null`. |
| `LIKE` / `NOT LIKE` | The **whole pattern is bound**. `%` and `_` keep their wildcard meaning in `LIKE` (and are literal in `=`); to match them literally in a `LIKE`, escape them in the pattern (`\%`, `\_` in MySQL/PostgreSQL; SQLite has no default escape character). The ORM does not escape the pattern for you. |
| Field names | Must be a real column of the model's table (or its `pk`/`rowid`), or `table.column` for JOINs. Anything else throws `QueryConditionException`. |
| Operators | Whitelist: `= != <> < > <= >= LIKE NOT LIKE IN NOT IN BETWEEN` (case/space-insensitive). Anything else throws. |
| `sort` (ORDER BY) | Each term must be a model column, `table.column`, an alias declared as `AS alias` in `fields`, or a numeric position, with optional `ASC`/`DESC`. Functions/expressions (`RAND()`, …) throw. |
| `limit` | `n` or `offset,n` with non-negative integers (cast to int); anything else throws. |
| `Paginate` | The same bound parameters feed the `COUNT` and the page query. |
| `Save()` | The primary key of the `UPDATE` is always bound (even with the constant off). `validate['unique']` uses the array form. |

Still the caller's responsibility (raw SQL by design): string `conditions`, `and()` / `or()`, `join`, `group`, `fields`, `Find_by_SQL`.
A string condition that contains `?` or `:name` outside quotes must not be mixed with array conditions when the constant is on.

**Before turning it on in an app:** (1) every field used in array conditions / `Find_by_*` is a real column (or `table.column`);
(2) no code escapes values by hand before calling the ORM for those forms (double escaping would search for the escaped text);
(3) every `sort` is a plain column list; (4) anything that relied on `null` meaning `''` or on an empty `IN` list is reviewed;
(5) run the app's suite with the constant on, and run the framework suite on MySQL (see `tests/README.md`).

### Uncaught errors ###

The front controller (`DumboPHP\index`) catches any `Throwable` that escapes an action or its view — including `Error`s such as
`TypeError` or `DivisionByZeroError`, which `catch (Exception $e)` does not see — and answers a generic **500**:

- Body: `Ocurrió un error en el servidor.` as `text/plain`, or `{"message":"Ocurrió un error en el servidor."}` as JSON when the
  request sends `Accept: application/json` or `X-Requested-With: XMLHttpRequest`, or the action had already called `respondToAJAX()`.
  The original message, file and trace never reach the client.
- Detail: class, message, file, line and trace are written with PHP's `error_log()` (the `error_log` ini setting; in CLI, STDERR).
- Output the request had written to a buffer (a view that fails halfway, `ob_start()` in the action, `output_buffering`) is dropped; a
  pending `redirect()` is dropped too. Output already flushed to the client (no buffering, e.g. streaming) cannot be taken back.
- `$page->failure()` returns the `Throwable` (server side only).

Actions must keep catching `ControllerException` / `Exception` themselves to answer the right HTTP code: what an action
catches is handled by the action, exactly as before. The front controller is the safety net for what nobody caught.

**CLI:** `dumbo run controller/action` prints `Error: <class>: <message>` to STDERR and exits with code `1` (the full detail is in the
log, also on STDERR); a request that does not fail exits `0` as before.

**Tests (Timothy):** `_runAction()` no longer aborts the suite with a trace when an action lets an error escape: it returns the page
with status 500 and the test goes on. The failure stays visible: the full trace goes to the test log and to STDERR (when PHP's
`error_log` points to a file, Timothy writes STDERR itself; otherwise the front controller's log already goes there), and
`$page->failure()` returns the `Throwable`.

An unexpected failure **fails the test** unless the test acknowledges it: `_runAction()` registers it as pending and, when the test
method ends, any failure nobody read with `$page->failure()` counts as a failed test (`F`, exit code `1`, and with `--halt=true` the run
stops there). The message includes the `Throwable`'s class, message, file and line. Reading `failure()` is the acknowledgement, so a
test that expects the 500 does `$this->assertTrue($page->failure() instanceof \TypeError)`. Errors an action catches itself are not
affected. `$page->failure(false)` peeks without acknowledging.

### Schema cache ###

Every request used to run `SHOW COLUMNS` (MySQL) or `PRAGMA table_info` (SQLite) once per table it loads. The schema only changes when a
migration runs, so the normalized result of `Connection::getColumnFields()` (`Cast`, `Field`, `Type`, `Value`) can be read from a file instead.

- **What is cached:** that normalized array, per table; never the SQL, so it does not depend on the engine. `Connection::columnFieldsFor($table)`
  returns exactly what `getColumnFields()` returns. `$GLOBALS['models']` is still the in-request registry on top of it
  (`DB->resetModelRegistry()` empties it, for tests).
- **Where:** `tmp/cache/schema/` under the project (`INST_PATH`), outside the webroot. One PHP file per connection, stamp and table
  (`<connection>-<stamp>-<table>.php`, a `return [...];` so opcache keeps it in shared memory), written atomically (temporary file in the
  same directory and `rename`) with mode `0660`; the directory is `0770`. The connection part is a hash of engine, host and database name
  (never credentials), so instances with different databases never mix schemas. An in-memory SQLite database has no persistent cache.
- **Invalidation by stamp:** `tmp/cache/schema/stamp` holds a random value. It is part of every cache file name, so changing the stamp
  leaves all previous files unused even with `opcache.validate_timestamps` off. `dumbo migration <action>` (every action except `sow`) writes
  a new stamp at the end and deletes the cache files of older stamps; any schema statement run through `Migrations` also renews an
  *existing* stamp, so a migration run from a script or a failed command cannot leave a stale cache.
- **No stamp, no cache:** without the `stamp` file, or when the directory is not writable, nothing is read or written and every request queries
  the schema as before, with no error or warning. An unreadable, empty or malformed cache file is ignored and regenerated.
- **Permissions:** the web server and the user who runs `dumbo` must both write `tmp/cache/schema/` (share a group); otherwise there is simply
  no cache, never a wrong one. If `dumbo` cannot replace or remove an old stamp it prints a warning: delete the stamp by hand.
- **Clearing it by hand:** delete `tmp/cache/schema/stamp` (cache off) or the whole `tmp/cache/schema/` directory.
- **Deployment note:** after updating the framework, run `dumbo migration run <table>` (or `all`) once on each instance: it creates the first stamp.

### Go Further ###

For more info, please visite homepage [DumboPHP](http://www.dumbophp.com/).
