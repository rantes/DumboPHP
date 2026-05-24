# DumboPHP — Active Record y Modelos

## Definición de un modelo

**Archivo**: `app/models/<nombre_singular>.php`  
**Clase**: `<NombreCamelizado> extends ActiveRecord`  
**Namespace**: `App\Models`

La clase hereda de `ActiveRecord`, que a su vez extiende `ArrayObject`. El nombre de la tabla en BD es el plural en snake_case del nombre de la clase (derivado automáticamente por el framework).

```php
<?php
namespace App\Models;
use DumboPHP\ActiveRecord;

class Product extends ActiveRecord {
    // Propiedades públicas = columnas de la tabla
    public ?string $name        = null;
    public ?string $description = null;
    public ?string $price       = null;
    public ?string $stock       = null;

    // _init_() reemplaza al constructor — NUNCA sobreescribir __construct()
    public function _init_(): void {
        // Configuración de relaciones y validaciones aquí
        $this->validates_presence_of('name');
    }
}
```

### Reglas de modelos

- Las propiedades públicas representan columnas. Siempre `?string $campo = null` (el framework castea según el tipo de BD).
- `_init_()` es el inicializador del modelo. Nunca usar `__construct()`.
- `id`, `created_at` y `updated_at` son manejados automáticamente — no declararlos.
- El nombre de la tabla se deriva automáticamente: `Product` → `products`, `UserProfile` → `user_profiles`.
- Para PK personalizada: `public string $pk = 'mi_pk';`

## Métodos de consulta (Find)

```php
// Todos los registros
$products = $this->Product->Find();

// Por ID (retorna objeto único)
$product = $this->Product->Find(5);

// Por IDs múltiples (string CSV)
$products = $this->Product->Find('1,2,3');

// Primero
$product = $this->Product->Find([':first']);

// Con condiciones (string SQL)
$products = $this->Product->Find([
    'conditions' => "price > 100 AND stock > 0"
]);

// Con condiciones, orden y límite
$products = $this->Product->Find([
    'conditions' => "category_id = {$id}",
    'sort'       => 'name ASC',
    'limit'      => 10
]);

// Con JOIN
$products = $this->Product->Find([
    'fields'     => 'products.*, categories.name AS cat_name',
    'join'       => 'INNER JOIN categories ON categories.id = products.category_id',
    'conditions' => "categories.active = 1"
]);

// Con GROUP BY
$summary = $this->Product->Find([
    'fields' => 'category_id, COUNT(*) as total',
    'group'  => 'category_id'
]);
```

## Crear y guardar registros

```php
// Nuevo registro vacío
$product = $this->Product->Niu();

// Nuevo registro con datos (desde POST, por ejemplo)
$product = $this->Product->Niu($_POST['product']);

// Guardar (INSERT si es nuevo, UPDATE si tiene id)
$product->name  = 'Widget';
$product->price = '9.99';
$saved = $product->Save();

if (!$saved) {
    // $product->_error contiene el mensaje de error
    echo $product->_error;
}
```

## Actualizar registros

```php
// Cargar y modificar
$product = $this->Product->Find(5);
$product->price = '12.99';
$product->Save();

// Actualizar con array de datos
$product = $this->Product->Find(5);
$product->Update(['price' => '12.99', 'stock' => '50']);
```

## Eliminar registros

```php
// Por ID
$this->Product->Delete(5);

// Desde el controlador con redirect
public function deleteAction(): void {
    $this->noTemplate = ['delete'];
    isset($this->params['id']) and $this->Product->Delete($this->params['id']);
    $this->redirect(INST_URI.'product/index/');
}
```

## Relaciones

Las relaciones se declaran en `_init_()` y se acceden como métodos en el objeto resultado:

```php
public function _init_(): void {
    $this->has_many         = ['comments'];       // tiene muchos
    $this->belongs_to       = ['category'];       // pertenece a
    $this->has_one          = ['profile'];        // tiene uno
    $this->has_many_and_belongs_to = ['tags'];    // muchos a muchos
}
```

Acceso desde el controlador (a través del objeto resultado):

```php
$product = $this->Product->Find(5);
$comments = $product->Comments();   // has_many
$category = $product->Category();   // belongs_to
```

## Validaciones

```php
public function _init_(): void {
    $this->validates_presence_of('name');
    $this->validates_presence_of('email');
    // Las validaciones se ejecutan automáticamente en Save()
}
```

Si la validación falla, `Save()` retorna `false` y `$obj->_error` contiene el mensaje.

## Dump y Load (importar/exportar datos)

```php
// Exportar datos de una tabla a archivo
$data = $this->Product->Find();
$data->Dump();  // genera archivo en migrations/

// Importar datos desde archivo dump
$this->Product->LoadDump();
```

También disponible desde CLI:
```bash
dumbo db dump products
dumbo db load products
dumbo db dump all
```

## Formularios — input_for()

El método `input_for($field)` genera el HTML del input apropiado según el tipo de columna:

```php
// En la vista addedit.phtml
<form action="/product/create/" method="POST">
    <?= $this->data->input_for('name'); ?>
    <?= $this->data->input_for('description'); ?>
    <?= $this->data->input_for('price'); ?>
    <input type="submit" value="Guardar" />
</form>
```

## ArrayObject — iteración de resultados

`Find()` sin ID retorna un `ArrayObject` iterable:

```php
// En la vista
<? foreach($this->data as $row): ?>
    <tr>
        <td><?= $row->name; ?></td>
        <td><?= $row->price; ?></td>
    </tr>
<? endforeach; ?>
```

`Find($id)` con ID retorna un objeto único (no iterable), accesible directamente:

```php
<?= $this->data->name; ?>
<?= $this->data->price; ?>
```
