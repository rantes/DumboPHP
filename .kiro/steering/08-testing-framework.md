---
inclusion: always
---

# DumboPHP — Testing del Framework

## Arquitectura de tests

El directorio `tests/` actúa como una **mini-aplicación autocontenida**
que usa DumboPHP para testear DumboPHP mismo. No es un proyecto normal —
es la infraestructura de verificación del framework.

## Dos niveles de testing

### Nivel 0 — verify_timothy.php (PHP puro)
- Verifica que Timothy funciona correctamente
- NO usa Timothy — usa `===` y `echo` nativos de PHP
- Debe pasar ANTES de confiar en cualquier otra suite
- Ejecutar con: `php tests/verify_timothy.php`
- Exit code 0 = OK, >0 = número de fallos

### Nivel 1 — suites/ (usan Timothy)
- Verifican el core de DumboPHP usando Timothy
- Solo son confiables si el Nivel 0 pasa
- Ejecutar con: `dumboTest all --dir=tests/suites/`

## Estructura de tests/

```
tests/
  config/
    host.php        ← constantes mínimas del entorno test
    db_settings.php ← SQLite :memory: únicamente
  migrations/       ← fixtures de BD para los tests
  models/           ← modelos de prueba (User, Post, Comment)
  controllers/      ← controladores de prueba (TestController)
  seeds/            ← Seeds.php con datos mínimos
  suites/           ← clases de test que usan Timothy
  verify_timothy.php← PHP puro, verifica Timothy
  bootstrap.php     ← inicializa entorno, registra autoloader
```

## Convenciones específicas de tests/

### config/host.php
Define constantes mínimas para el entorno de test:
- `APP_ENV = 'test'`
- `DEF_CONTROLLER = 'test'`
- `INST_PATH` apunta a la raíz del repo
- `TEST_PATH` apunta a `tests/`
- NO define INST_URI ni constantes de producción

### config/db_settings.php
SOLO entorno test con SQLite en memoria:
```php
$databases = ['test' => ['driver' => 'sqlite', 'schema' => 'memory']];
```

### models/ — modelos de fixture
Son modelos simples que ejercitan las funcionalidades del ORM:
- `User` — validate['presence_of'], has_many, before_save
- `Post` — validate['presence_of'], belongs_to, has_many
- `Comment` — validate['presence_of'], belongs_to

No representan dominio de negocio — son fixtures del framework.
Namespace: `App\Models`

### migrations/ — migraciones de fixture
Tablas mínimas para los tests: `users`, `posts`, `comments`.
Namespace: `Migrations`

### controllers/ — controladores de fixture
`TestController` con acciones básicas para testear routing y rendering.
Namespace: `App\Controllers`

### suites/ — clases de test
Extienden `dumboTests`. Namespace: `tests`.
Cada suite tiene `beforeEach` que hace `_migrateTables()` para
garantizar estado limpio en cada test.

## bootstrap.php

Punto de entrada para las suites. Carga el framework y registra
el autoloader que mapea namespaces a los directorios de `tests/`:

```
App\Models\     → tests/models/
App\Controllers\→ tests/controllers/
Migrations\     → tests/migrations/
tests\          → tests/suites/
```

## verify_timothy.php

Script PHP standalone. Patrón de verificación:

```php
$passed = 0; $failed = 0;

// Capturar output de Timothy para no contaminar la consola
$t = new dumboTests('/dev/null');

// Test que DEBE pasar
$before = $t->assertions;
ob_start(); $t->assertEquals('a', 'a'); ob_end_clean();
($t->assertions === $before + 1 && $t->_failed === 0)
    ? $passed++
    : $failed++;

// Test que DEBE fallar
$beforeFailed = $t->_failed;
ob_start(); $t->assertEquals('a', 'b'); ob_end_clean();
($t->_failed === $beforeFailed + 1)
    ? $passed++
    : $failed++;

echo "Timothy self-check: {$passed} passed, {$failed} failed\n";
exit($failed);
```

## Mejoras a Timothy en scope

Las siguientes mejoras a `lib/Timothy/dumboTests.php` son parte
de este proyecto:

### Reset automático en beforeEach
`testDispatcher` debe resetear antes de cada test:
```php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_POST = []; $_GET = []; $_FILES = [];
```

### spyOn real
```php
spyOn(object &$obj, string $method): void
assertMethodHasBeenCalled(string $method, int $times = 1): void
assertMethodCalledWith(string $method, array $args): void
```
Implementado con Closure binding y ReflectionClass.
Registra llamadas en `$this->_spyCalls[$method][]`.

### stubMethod
```php
stubMethod(object &$obj, string $method, mixed $returnValue): void
```

### createMock
```php
createMock(string $className, array $methods = []): object
```

## Orden de implementación obligatorio

1. `verify_timothy.php` — siempre primero
2. Mejoras a Timothy (reset, spyOn, stub, mock)
3. bootstrap.php + config/ + migrations/ + models/ + controllers/
4. Suites sin BD: TestQueryCondition, TestIrregularNouns, TestHelperFunctions
5. Suites con BD: TestActiveRecord, TestValidations, TestHooks
6. TestRelations, TestMigrations
7. TestController, TestRouting
8. README.md

## Criterio de éxito

```bash
php tests/verify_timothy.php       # exit 0
dumboTest all --dir=tests/suites/  # 0 fallos
```
