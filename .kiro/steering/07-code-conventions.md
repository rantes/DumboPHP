# DumboPHP — Convenciones de Código y Anti-patrones

## Estilo de código PHP

DumboPHP usa sintaxis alternativa de PHP para estructuras de control en vistas y código orientado a legibilidad en lógica de negocio.

### Operadores de cortocircuito en lugar de if triviales

```php
// Correcto — idiomático en DumboPHP
empty($this->tblName) and $this->setNames($params[0]);
file_exists($path) or die('Archivo no encontrado.');
$replace && ($action = 'REPLACE');

// Evitar para lógica simple
if (empty($this->tblName)) {
    $this->setNames($params[0]);
}
```

### Sintaxis alternativa en vistas (.phtml)

```php
// Correcto en vistas
<? foreach($this->data as $row): ?>
    <tr><td><?= $row->name; ?></td></tr>
<? endforeach; ?>

<? if (!empty($this->data)): ?>
    <p>Hay datos</p>
<? else: ?>
    <p>Sin datos</p>
<? endif; ?>

// Incorrecto en vistas
<? foreach($this->data as $row) { ?>
    <tr><td><?= $row->name; ?></td></tr>
<? } ?>
```

### Heredoc para strings multilínea en generadores/migraciones

```php
$fileContent = <<<DUMBOPHP
<?php
namespace App\Models;
use DumboPHP\ActiveRecord;

class {$className} extends ActiveRecord {
    public function _init_(): void {
    }
}
DUMBOPHP;
```

### Declaraciones de tipo

- PHP 8.1+: usar tipos en parámetros y retornos de métodos de framework y modelos.
- En propiedades de modelos: siempre `?string $campo = null` (el framework castea internamente).
- En controladores: `void` para acciones, tipos específicos para helpers.

```php
// Correcto
public function indexAction(): void { ... }
public function _formatPrice(float $price): string { ... }
public ?string $name = null;

// Incorrecto — no tipar propiedades de modelo con int/float/bool
public int $price = 0;  // el ORM trabaja con strings internamente
```

---

## Anti-patrones — Lo que NO hacer

### ❌ Instanciar modelos manualmente en controladores

```php
// MAL — rompe Lazy Load
public function indexAction(): void {
    $model = new \App\Models\Product();
    $this->data = $model->Find();
}

// BIEN — Lazy Load vía __get()
public function indexAction(): void {
    $this->data = $this->Product->Find();
}
```

### ❌ Sobreescribir `__construct()` en modelos o controladores

```php
// MAL — rompe la inicialización del framework
class Product extends ActiveRecord {
    public function __construct() {
        parent::__construct();
        $this->name = 'default';
    }
}

// BIEN — usar _init_()
class Product extends ActiveRecord {
    public function _init_(): void {
        $this->validates_presence_of('name');
    }
}
```

### ❌ Lógica de negocio en vistas

```php
// MAL — lógica en la vista
<? $products = $this->Product->Find(['conditions' => 'active=1']); ?>
<? foreach($products as $row): ?>

// BIEN — lógica en el controlador, datos en $this->data
// En el controlador:
$this->data = $this->Product->Find(['conditions' => 'active=1']);
// En la vista:
<? foreach($this->data as $row): ?>
```

### ❌ Queries SQL directas fuera del modelo o migración

```php
// MAL — SQL crudo en el controlador
public function indexAction(): void {
    $result = DB->query("SELECT * FROM products WHERE active=1");
    $this->data = $result->fetchAll();
}

// BIEN — usar el Active Record
public function indexAction(): void {
    $this->data = $this->Product->Find(['conditions' => 'active=1']);
}
```

### ❌ Agregar dependencias de Composer innecesarias

El framework es intencionalmente sin dependencias en runtime. Antes de agregar un paquete, verificar si la funcionalidad ya existe en el framework o en PHP nativo. Solo se permiten dependencias de desarrollo (XDebug, etc.).

### ❌ Archivos de rutas o configuración de rutas

DumboPHP no tiene router configurable. El routing es por convención URL → controlador/acción. No crear archivos de rutas ni sistemas de routing adicionales.

### ❌ Motor de plantillas externo

Las vistas son PHP puro con sintaxis corta. No integrar Twig, Blade, Smarty ni similares.

---

## Convenciones de seguridad

### Datos de usuario en vistas

```php
// Siempre escapar output de usuario
<?= htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?>

// Para campos que se sabe son numéricos (el ORM castea)
<?= $row->id; ?>
<?= $row->price; ?>
```

### Formularios POST

Los datos de formulario se pasan a `Niu()` directamente. El Active Record maneja el binding:

```php
// En el controlador
public function createAction(): void {
    $this->noTemplate = ['create'];
    if (isset($_POST['product'])):
        $obj = $this->Product->Niu($_POST['product']);
        $obj->Save() or die($obj->_error);
    endif;
    $this->redirect(INST_URI.'product/index/');
}
```

El nombre del array POST debe coincidir con el nombre singular del modelo en snake_case: `$_POST['product']`, `$_POST['user_profile']`.

### SALT y secretos

- `SALT` se define en `config/host.php` y debe cambiarse en cada proyecto.
- Credenciales de BD solo en `.env` (nunca en código fuente ni en git).
- `.env` debe estar en `.gitignore`.

---

## Convenciones de archivos de configuración

### config/host.php — constantes de la aplicación

```php
define('APP_ENV', $env_vars['APP_ENV']);        // entorno activo
define('INST_URI', $env_vars['SITE_URI']);       // URL base con / al final
define('SITE_STATUS', 'LIVE');                   // LIVE o MAINTENANCE
define('LANDING_PAGE', 'index/landing');         // página de aterrizaje
define('DEF_CONTROLLER', 'index');               // controlador por defecto
define('DEF_ACTION', 'index');                   // acción por defecto
define('USE_ALTER_URL', false);                  // routing alternativo
define('SALT', 'cambiar_esto');                  // siempre único por proyecto
```

### config/db_settings.php — configuración de BD

Siempre incluir al menos los entornos `dev` y `test`. El entorno `test` debe usar SQLite en memoria para que los tests sean rápidos y aislados:

```php
$databases = [
    'dev' => [
        'driver'      => $this->_sysConfig('DB_DRIVER'),
        'host'        => $this->_sysConfig('DB_HOST'),
        'charset'     => $this->_sysConfig('DB_CHARSET'),
        'dialect'     => $this->_sysConfig('DB_DIALECT'),
        'port'        => $this->_sysConfig('DB_PORT'),
        'schema'      => $this->_sysConfig('DB_SCHEMA'),
        'username'    => $this->_sysConfig('DB_USERNAME'),
        'password'    => $this->_sysConfig('DB_PASSWORD'),
        'unix_socket' => $this->_sysConfig('DB_UNIX_SOCKET'),
    ],
    'test' => [
        'driver' => 'sqlite',
        'schema' => 'memory',
    ],
];
```

---

## Scaffold como punto de partida

Cuando se necesita un CRUD completo, siempre usar scaffold como base y luego personalizar. No escribir controladores CRUD desde cero:

```bash
dumbo generate scaffold products name:string description:text price:float
```

Esto genera modelo + migración + controlador con las 4 acciones CRUD + vistas index y addedit. Luego se ajusta según las necesidades específicas.
