# DumboPHP — Testing con Timothy

## Estructura de un test

**Archivo**: `tests/<NombreClase>Test.php`  
**Clase**: `<NombreClase>Test extends dumboTests`  
**Namespace**: `tests`

Los métodos de test deben terminar en `Test` (ej: `createProductTest`).

```php
<?php
namespace tests;

use DumboPHP\lib\Timothy\dumboTests;

class ProductTest extends dumboTests {

    // Se ejecuta una vez al inicio del suite
    public function _init_(): void {
        // Resetear tablas necesarias para el test
        $this->_migrateTables(['products', 'categories']);
        // Poblar con datos de prueba
        $this->_sow();
    }

    // Se ejecuta antes de CADA test
    public function beforeEach(): void {
        // Setup por test si es necesario
    }

    // Se ejecuta una vez al final del suite
    public function _end_(): void {
        // Limpieza post-suite si es necesario
    }

    // --- Tests (deben terminar en "Test") ---

    public function createProductTest(): void {
        $this->describe('Debe crear un producto correctamente');

        $product = $this->Product->Niu();
        $product->name  = 'Test Product';
        $product->price = '9.99';
        $result = $product->Save();

        $this->assertTrue($result, 'Save() debe retornar true');
        $this->assertNotEmpty($product->id, 'El producto debe tener id después de Save()');
    }

    public function findProductTest(): void {
        $this->describe('Debe encontrar productos');

        $products = $this->Product->Find();
        $this->assertNotEmpty($products, 'Find() debe retornar resultados');
        $this->assertGreaterThan(0, count($products));
    }

    public function findByIdTest(): void {
        $this->describe('Debe encontrar un producto por ID');

        $product = $this->Product->Find(1);
        $this->assertNotFalse($product);
        $this->assertEquals('1', $product->id);
    }

    public function validationTest(): void {
        $this->describe('Debe fallar si falta campo requerido');

        $product = $this->Product->Niu();
        // Sin name — debe fallar validación
        $result = $product->Save();
        $this->assertFalse($result, 'Save() sin name debe retornar false');
    }

    public function controllerActionTest(): void {
        $this->describe('La acción index debe renderizar correctamente');

        $page = $this->_runAction('product/index');
        $this->assertNotEmpty($page->_rawOutput);
    }
}
```

## Aserciones disponibles

| Método | Descripción |
|--------|-------------|
| `assertEquals($a, $b, $msg?)` | Compara con `===` |
| `assertTrue($val, $msg?)` | Verifica que sea `true` exacto |
| `assertFalse($val, $msg?)` | Verifica que sea `false` exacto |
| `assertNotEmpty($val)` | Verifica que no esté vacío |
| `assertNotFalse($val)` | Verifica que no sea `false` |
| `assertGreaterThan($min, $val)` | Verifica que `$val > $min` |
| `assertArrayHasKey($key, $arr)` | Verifica que la clave exista |
| `assertHasFields($model)` | Verifica que el modelo tenga los campos de la migración |
| `assertHasFieldTypes($model)` | Verifica que los tipos de campo coincidan con la BD |
| `describe($mensaje)` | Documenta el propósito del test en el log |

## Helpers de test

### `_runAction(string $url)` — Simular petición HTTP

```php
// Simula GET a product/index
$page = $this->_runAction('product/index');
$this->assertNotEmpty($page->_rawOutput);

// Con parámetros GET
$page = $this->_runAction('product/show?id=1');
$this->assertNotEmpty($page->_rawOutput);
```

### `_migrateTables(array $tablas)` — Resetear tablas

Ejecuta `reset` (down + up) en las tablas indicadas. Usar en `_init_()` para garantizar estado limpio:

```php
$this->_migrateTables(['products', 'categories', 'users']);
```

### `_sow()` — Ejecutar seeds

```php
$this->_sow(); // Ejecuta migrations/seeds.php → Seeds::sow()
```

### `invokeMethod($obj, $method, $params)` — Invocar método privado/protegido

```php
$result = $this->invokeMethod($this->Product, '_validateEmail', ['test@example.com']);
$this->assertTrue($result);
```

### `setSysconfigValue($obj, $key, $val)` — Modificar sysConfig en tests

```php
$this->setSysconfigValue($this->Product, 'some_config', 'test_value');
```

## Entorno de test

- El entorno se fuerza a `test` automáticamente (`$GLOBALS['env'] = 'test'`).
- La BD de test usa la configuración `test` de `config/db_settings.php` (SQLite en memoria por defecto).
- Los tests son **aislados de producción** por diseño.

## Ejecutar tests

```bash
# Todos los tests
dumboTest all

# Test específico
dumboTest ProductTest

# Varios tests (separados por espacio)
dumboTest ProductTest CategoryTest

# Detener en primer fallo
dumboTest all --halt=true

# Modo watch (re-ejecuta al guardar)
dumboTest all --watch=true

# Directorio personalizado
dumboTest all --dir=tests/unit/
```

## Salida y reportes

- **Consola**: `P` (passed) o `F` (failed) por cada aserción, en verde/rojo.
- **Exit code**: `0` si todos pasan, número de fallos si hay errores.
- **`test-result.xml`**: Reporte JUnit compatible con CI/CD.
- **`coverage.xml`**: Reporte de cobertura Clover (requiere XDebug).
- **`tmp/logs/unit_testing.log`**: Log detallado de cada aserción.

## Integración CI/CD

El exit code no-cero en fallos permite integración directa con pipelines:

```yaml
# .travis.yml / GitHub Actions
- dumboTest all --halt=true
```
