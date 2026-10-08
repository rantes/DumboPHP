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

### Go Further ###

For more info, please visite homepage [DumboPHP](http://www.dumbophp.com/).
