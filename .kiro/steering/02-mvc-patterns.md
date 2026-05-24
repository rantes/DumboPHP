# DumboPHP — Patrones MVC

## Routing

El routing es por convención, sin archivos de rutas. La URL se mapea directamente:

```
/controller/action/param1/param2
```

- `DEF_CONTROLLER` y `DEF_ACTION` en `config/host.php` definen los defaults (normalmente `index`/`index`).
- El `.htaccess` pasa todo como `?url=controller/action/...` al `index.php`.
- Existe routing alternativo configurable con `USE_ALTER_URL` y `ALTER_URL_CONTROLLER_ACTION`.

## Controladores

**Archivo**: `app/controllers/<nombre_singular>_controller.php`
**Clase**: `<NombreCamelizado>Controller extends Controller`
**Namespace**: `App\Controllers`

```php
<?php
namespace App\Controllers;
use DumboPHP\Controller;

class ProductController extends Controller {

    public function indexAction(): void {
        // Cualquier propiedad de $this es accesible en la vista
        $this->data     = $this->Product->Find();
        $this->title    = 'Lista de productos';
        $this->featured = $this->Product->Find([':first']);
    }

    public function showAction(): void {
        $this->product = $this->Product->Find($this->params['id']);
    }
}
```

### Reglas de controladores

- Cada acción es un método con sufijo `Action`. El prefijo puede ser cualquier texto (ej: `indexAction`, `addEditAction`, `doSomethingAction`).
- `$this->params` contiene los parámetros de la URL y GET.
- Cualquier propiedad asignada a `$this` en la acción queda disponible en la vista con el mismo nombre. No hay una variable especial obligatoria — `$this->data`, `$this->product`, `$this->items`, etc., todas son accesibles en la vista como `$this->data`, `$this->product`, `$this->items`.
- `$this->layout` define qué layout usar. La clase padre ya lo define; para cambiarlo se asigna en `_init_()` o en la propia acción: `$this->layout = 'admin_layout';`. Asignar `null` desactiva el layout.
- `$this->noTemplate` es un array de nombres de acción (sin el sufijo `Action`) que no renderizan vista (ej: acciones que solo redirigen).
- Los modelos se acceden como propiedades: `$this->Product`, `$this->UserProfile` — **nunca** `new Product()`.
- Para redirigir usar el método `redirect()` del framework: `$this->redirect(INST_URI.'controller/action/');`

### Lazy Model Load

El `__get()` del Controller instancia el modelo automáticamente al primer acceso:

```php
// Correcto — el framework instancia Product en el primer acceso
$this->data = $this->Product->Find();

// Incorrecto — viola el principio de Lazy Load
$product = new \App\Models\Product();
$this->data = $product->Find();
```

### Redirecciones

Usar siempre el método `redirect()` del framework, nunca `header()` directamente:

```php
// Correcto
public function deleteAction(): void {
    $this->noTemplate = ['delete'];
    isset($this->params['id']) and $this->Product->Delete($this->params['id']);
    $this->redirect(INST_URI.'product/index/');
}

// Incorrecto
header('Location: '.INST_URI.'product/index/');
exit;
```

## Vistas

**Archivo**: `app/views/<controlador_singular>/<accion>.phtml`
**Sintaxis**: PHP corto habilitado (`<? ?>` y `<?= ?>`)

```php
<!-- app/views/product/index.phtml -->
<section>
    <? foreach($this->data as $row): ?>
        <p><?= $row->name; ?></p>
    <? endforeach; ?>
</section>
```

### Layout

El layout envuelve todas las vistas. La vista se inyecta con `<?=$this->yield;?>`:

```php
<!-- app/views/layout.phtml -->
<!doctype html>
<html>
<head>
    <base href="<?=INST_URI;?>">
    <title>Mi App</title>
</head>
<body>
    <?=$this->yield;?>
</body>
</html>
```

### Acceso a datos en vistas

- Cualquier propiedad asignada a `$this` en el controlador es accesible en la vista con el mismo nombre: `$this->data`, `$this->product`, `$this->title`, etc.
- `$this->params` — parámetros de la URL
- `INST_URI` — URL base de la aplicación (siempre con `/` al final)
- Las vistas son PHP puro, sin motor de plantillas adicional.

## Modelos

Ver steering `03-active-record.md` para documentación completa.

## Convenciones de nombres

| Elemento | Convención | Ejemplo |
|----------|-----------|---------|
| Tabla BD | plural, snake_case | `user_profiles` |
| Archivo modelo | singular, snake_case | `user_profile.php` |
| Clase modelo | singular, CamelCase | `UserProfile` |
| Archivo controlador | singular, snake_case + `_controller` | `user_profile_controller.php` |
| Clase controlador | singular, CamelCase + `Controller` | `UserProfileController` |
| Carpeta de vistas | singular, snake_case | `user_profile/` |
| Archivos de vista | snake_case + `.phtml` | `index.phtml`, `add_edit.phtml` |
| Acciones | cualquierTexto + `Action` | `indexAction`, `addEditAction`, `doSomethingAction` |
