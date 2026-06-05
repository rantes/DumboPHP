# DumboPHP — Test Suite & Timothy Improvements
## Spec v1.0

## Objetivo

Construir una suite de tests autocontenida dentro del repo DumboPHP que:
1. Verifique Timothy en PHP puro (sin depender de Timothy)
2. Verifique el core de DumboPHP usando Timothy
3. Mejore Timothy con mocks, stubs y spyOn

---

## Estructura de archivos

```
tests/
  config/
    host.php              ← constantes mínimas, env=test
    db_settings.php       ← SQLite :memory:
  migrations/
    CreateUsers.php       ← tabla para ActiveRecord tests
    CreatePosts.php       ← tabla para relaciones
    CreateComments.php    ← tabla para has_many anidado
  models/
    User.php              ← validates, belongs_to, has_many, before_save
    Post.php              ← validates, belongs_to user, has_many comments
    Comment.php           ← validates, belongs_to post
  controllers/
    TestController.php    ← acciones básicas para testear routing
  seeds/
    Seeds.php             ← datos mínimos de prueba
  suites/
    TestQueryCondition.php
    TestIrregularNouns.php
    TestHelperFunctions.php
    TestActiveRecord.php
    TestRelations.php
    TestHooks.php
    TestValidations.php
    TestMigrations.php
    TestController.php
    TestRouting.php
  verify_timothy.php      ← PHP puro, sin Timothy, verifica Timothy
  bootstrap.php           ← inicializa entorno, carga framework
  run.php                 ← entry point: dumboTest all
```

---

## Fase 0 — verify_timothy.php (PHP puro)

Este archivo NO usa Timothy. Verifica que las aserciones y el runner
funcionen correctamente antes de confiar en ellos para el resto.

Implementación: script PHP standalone que:
1. Instancia `dumboTests` directamente
2. Llama cada aserción con casos que DEBEN pasar y casos que DEBEN fallar
3. Verifica manualmente con `===` que el contador de assertions incrementó
4. Verifica que `_failed` incrementó cuando debía
5. Verifica que `_passed` incrementó cuando debía
6. Reporta resultado con `echo` y `exit($failed_count)`

Casos a verificar en PHP puro:

```
assertEquals:
  - valores iguales → _failed no incrementa, assertions++
  - valores distintos → _failed incrementa

assertTrue:
  - true exacto → pasa
  - false → falla
  - 1 → falla (estricto)

assertFalse:
  - false exacto → pasa
  - true → falla

assertNotEmpty:
  - string no vacío → pasa
  - string vacío → falla
  - array vacío → falla

assertGreaterThan:
  - assertGreaterThan(1, 5) → pasa (5 > 1)
  - assertGreaterThan(5, 1) → falla

assertArrayHasKey:
  - clave existente → pasa
  - clave inexistente → falla

assertNotFalse:
  - 0 → pasa (no es false)
  - false → falla

invokeMethod:
  - accede a método privado y retorna valor correcto

describe:
  - no lanza excepción con string válido
  - lanza excepción con no-string

beforeEach:
  - $_SERVER['REQUEST_METHOD'] reseteado a GET
  - $_POST reseteado a []
  - $_GET reseteado a []

assertions counter:
  - cada llamada a aserción incrementa $assertions en 1
```

Criterio de éxito: `php tests/verify_timothy.php` sale con código 0.

---

## Fase 1 — Infraestructura base

### tests/config/host.php
```php
define('INST_PATH', dirname(dirname(__FILE__)).'/');
define('TEST_PATH', dirname(__FILE__).'/');
define('APP_ENV', 'test');
define('DEF_CONTROLLER', 'test');
define('DEF_ACTION', 'index');
define('SALT', 'test_salt_dumbophp');
define('INST_URI', 'http://localhost/');
define('SITE_STATUS', 'LIVE');
define('LANDING_PAGE', 'test/index');
define('USE_ALTER_URL', false);
$GLOBALS['env'] = 'test';
```

### tests/config/db_settings.php
Solo entorno test: `driver=sqlite, schema=memory`

### tests/migrations/

**CreateUsers:**
id, name (VARCHAR 100, NOT NULL), email (VARCHAR 150, NOT NULL),
age (INTEGER, default 0), active (INTEGER, default 1),
created_at, updated_at

**CreatePosts:**
id, title (VARCHAR 255, NOT NULL), body (TEXT),
user_id (INTEGER, NOT NULL, default 0), created_at, updated_at

**CreateComments:**
id, content (TEXT, NOT NULL),
post_id (INTEGER, NOT NULL, default 0), created_at, updated_at

### tests/models/

**User:**
- validates_presence_of: name, email
- has_many: posts
- before_save: sanitizeName (htmlentities sobre name)

**Post:**
- validates_presence_of: title, user_id
- belongs_to: user
- has_many: comments

**Comment:**
- validates_presence_of: content, post_id
- belongs_to: post

### tests/controllers/TestController.php
```php
indexAction:   $this->message = 'hello'; // renderiza vista
jsonAction:    respondToAJAX(json_encode(['ok'=>true])), noTemplate
paramAction:   $this->received = $this->params['id']
redirectAction: $this->redirect(INST_URI.'test/index')
```

### tests/bootstrap.php
```php
define('INST_PATH', dirname(dirname(__FILE__)).'/');
define('TEST_PATH', __DIR__.'/');

set_include_path(
    INST_PATH.'bin'.PATH_SEPARATOR.
    INST_PATH.PATH_SEPARATOR.
    TEST_PATH.PATH_SEPARATOR.
    get_include_path()
);

require_once 'dumbophp.php';
require_once TEST_PATH.'config/host.php';
require_once TEST_PATH.'config/db_settings.php';

spl_autoload_register(function($class) {
    $map = [
        'App\\Models\\'      => TEST_PATH.'models/',
        'App\\Controllers\\' => TEST_PATH.'controllers/',
        'Migrations\\'       => TEST_PATH.'migrations/',
        'tests\\'            => TEST_PATH.'suites/',
    ];
    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = $dir.basename(str_replace('\\','/',$class)).'.php';
            file_exists($file) and require_once $file;
        }
    }
});
```

---

## Fase 2 — Suites de tests (usan Timothy)

### TestQueryCondition
- connectorAndValidTest: 'AND' es aceptado
- connectorOrValidTest: 'OR' es aceptado
- connectorInvalidTest: conector inválido lanza QueryConditionException
- connectorCaseInsensitiveTest: 'and' se normaliza a 'AND'
- toStringTest: __toString retorna "AND condicion"
- getKeyTest: getKey retorna md5 consistente
- duplicateKeyTest: misma condición produce mismo key
- whitespaceNormalizationTest: espacios múltiples se normalizan a uno

### TestIrregularNouns
- pluralizeRegularTest: user → users
- pluralizeIrregularTest: person → people
- singularizeRegularTest: users → user
- singularizeIrregularTest: people → person
- camelizeTest: user_profile → UserProfile
- unCamelizeTest: UserProfile → user_profile
- pluralizeAlreadyPluralTest: users no se doble-pluraliza

### TestHelperFunctions
- uuidV4FormatTest: formato correcto xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
- uuidV4UniqueTest: dos llamadas generan valores distintos
- getallheadersTest: retorna array (no lanza excepción)

### TestActiveRecord
beforeEach: _migrateTables(['users'])

- niuEmptyTest: Niu() retorna instancia de User
- niuWithDataTest: Niu(['name'=>'Ana','email'=>'a@b.com']) popula propiedades
- saveInsertTest: Save() inserta, retorna true, id > 0
- saveUpdateTest: modificar campo + Save() hace UPDATE, no INSERT
- findAllTest: Find() retorna ArrayObject iterable
- findByIdIntTest: Find(1) retorna objeto con id=1
- findByConditionsTest: Find(['conditions'=>"`active`=1"]) filtra
- findWithSortTest: Find(['sort'=>'name ASC']) ordena
- findWithLimitTest: Find(['limit'=>2]) retorna máximo 2
- findFirstTest: Find([':first']) retorna exactamente 1 registro
- deleteByIdTest: Delete(id) elimina, Find(id) queda vacío
- countTest: count() retorna entero correcto
- autoAuditCreatedAtTest: created_at > 0 después de Save()
- autoAuditUpdatedAtTest: updated_at > 0 después de UPDATE
- rawFieldsTest: getRawFields() incluye 'name', 'email', 'age'
- tableNameTest: _TableName() retorna 'users'
- sqlQueryTest: _sqlQuery contiene SELECT después de Find()
- inspectTest: inspect() retorna string no vacío

### TestValidations
beforeEach: _migrateTables(['users'])

- presenceOfPassTest: Save() con name y email retorna true
- presenceOfNameFailTest: Save() sin name retorna false
- presenceOfEmailFailTest: Save() sin email retorna false
- errorAfterFailTest: _error no está vacío después de fallo
- errFieldsTest: _error->errFields() contiene el campo fallido
- errorClearOnSuccessTest: segundo Save() válido limpia errores previos

### TestHooks
beforeEach: _migrateTables(['users'])

- beforeSaveExecutedTest: sanitizeName ejecutado, name tiene htmlentities
- beforeInsertOnlyTest: before_insert NO se ejecuta en UPDATE
- hookErrorStopsSaveTest: hook que activa _error → Save() retorna false
- multipleHooksOrderTest: hooks en before_save ejecutados en orden declarado
- afterInsertExecutedTest: after_insert corre después del INSERT
- afterUpdateExecutedTest: after_update corre después del UPDATE

### TestRelations
beforeEach: _migrateTables(['users','posts','comments'])

- hasManyTest: $user->Posts() retorna posts del usuario
- belongsToTest: $post->User() retorna el usuario correcto
- hasManyNestedTest: $post->Comments() retorna comentarios del post
- belongsToNestedTest: $comment->Post() retorna el post correcto
- hasManyEmptyTest: usuario sin posts → Posts() retorna colección vacía
- hasManyCountTest: usuario con 3 posts → Posts()->count() === 3

### TestMigrations
- createTableTest: Create_Table() no lanza excepción, tabla existe
- dropTableTest: Drop_Table() elimina la tabla
- resetTest: down + up recrea tabla vacía (count = 0)
- getFieldsTest: getFields() retorna array con 'name', 'email'
- getDefinitionsTest: getDefinitions() retorna array con type de cada campo
- assertHasFieldsTest: assertHasFields() pasa con modelo sincronizado
- assertHasFieldTypesTest: assertHasFieldTypes() pasa con tipos correctos

### TestController
beforeEach: _migrateTables(['users']), REQUEST_METHOD=GET

- lazyLoadTest: $this->User en acción instancia modelo automáticamente
- actionExistsTest: _runAction('test/index') → _rawOutput no vacío
- actionNotFoundTest: _runAction('test/nonexistent') → HTTP 404
- paramsTest: _runAction('test/param/42') → $page->received == 42
- noTemplateTest: jsonAction → _rawOutput contiene JSON
- rawOutputNotEmptyTest: indexAction → _rawOutput contiene 'hello'

### TestRouting
- defaultRouteTest: _runAction('/') resuelve sin error
- controllerActionRouteTest: _runAction('test/index') → TestController::indexAction
- paramRouteTest: _runAction('test/param/99') → params['id'] = 99

---

## Fase 3 — Mejoras a Timothy

### Reset automático en beforeEach
Agregar al inicio de cada ejecución de test en testDispatcher:
```php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_POST    = [];
$_GET     = [];
$_FILES   = [];
```
$_SESSION se preserva — el test lo controla.

### spyOn real
```php
// Registra un spy sobre un método de un objeto
public function spyOn(object &$obj, string $method): void

// Verifica que el método fue llamado N veces
public function assertMethodHasBeenCalled(string $method, int $times = 1): void

// Verifica que fue llamado con argumentos específicos
public function assertMethodCalledWith(string $method, array $args): void
```

Implementación: Closure binding con ReflectionClass.
Registra llamadas en `$_spyCalls[$method][]`.

### Stub de métodos
```php
// Sobreescribe método para retornar valor fijo
public function stubMethod(object &$obj, string $method, mixed $returnValue): void
```

### Mock de clases
```php
// Crea instancia con métodos que retornan null por defecto
// o los valores definidos en $methods
public function createMock(string $className, array $methods = []): object
```

---

## Fase 4 — Documentación

README.md actualizado con:
- Instalación en 3 pasos
- Estructura de proyecto
- Guía rápida: modelo, controlador, vista, migración
- Referencia CLI completa
- Guía de Timothy con ejemplos de mocks/stubs/spyOn
- Ejemplos de tests — las suites son documentación viva

---

## Orden de implementación

1. verify_timothy.php — PHP puro, sin Timothy
2. Mejoras a Timothy (reset beforeEach, spyOn, stub, mock)
3. bootstrap.php + config/ + migrations/ + models/ + controllers/
4. TestQueryCondition, TestIrregularNouns, TestHelperFunctions
5. TestActiveRecord, TestValidations, TestHooks
6. TestRelations, TestMigrations
7. TestController, TestRouting
8. Documentación README.md

---

## Criterio de éxito

- `php tests/verify_timothy.php` → exit 0
- `dumboTest all --dir=tests/suites/` → 0 fallos
- Cobertura de clases core de dumbophp.php > 80%
