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
    public ?float  $price       = null;
    public ?int    $stock       = null;

    // _init_() reemplaza al constructor — NUNCA sobreescribir __construct()
    public function _init_(): void {
        // Configuración de relaciones, validaciones y hooks aquí
        $this->validate['presence_of'] = ['name'];
    }
}
```

### Reglas de modelos

- Las propiedades públicas representan columnas. El tipo de la propiedad refleja el de la columna: `?string` para texto, `?int` para enteros, IDs, timestamps y flags 0/1, `?float` para decimales (`FLOAT`, `DECIMAL`). Nunca `?bool`: usar `?int` con 0/1. Una propiedad `?string` sigue funcionando igual que siempre, pero no valida nada.
- `_init_()` es el inicializador del modelo. Nunca usar `__construct()`.
- `id`, `created_at` y `updated_at` son manejados automáticamente — no declararlos.
- El nombre de la tabla se deriva automáticamente: `Product` → `products`, `UserProfile` → `user_profiles`.
- Para PK personalizada: `public string $pk = 'mi_pk';`

### Datos inválidos en propiedades tipadas

Los valores que llegan por el constructor o por `Niu([...])` (la asignación en bloque desde `$_POST`) se validan contra el tipo de la propiedad. Un valor que no cabe **no se asigna**, queda registrado en `$obj->_error` bajo el nombre del campo (sin lanzar excepción) y `Save()` devuelve `false`. Los demás campos se asignan normalmente. Comportamiento comprobado en SQLite:

| Valor | `?int` | `?float` | `?string` |
|-------|--------|----------|-----------|
| `"5"`, `5` | `5` | `5.0` | `"5"` |
| `"5.7"` | error | `5.7` | `"5.7"` |
| `""` | `0` | `0.0` | `""` |
| `null` | `0` | `0.0` | `null` |
| `"abc"` | error | error | `"abc"` |
| `"3,5"` | error | error | `"3,5"` |
| `"1e3"` | `1000` | `1000.0` | `"1e3"` |
| arreglo | error | error | error |

(`""` y `null` valen `0` solo en campos numéricos de la tabla.) Un `?string` solo rechaza lo que no es texto (un arreglo).

**Casos de formulario en un `?int`** (comprobados): se aceptan `"5.0"`, `"5.00"`, `"100.00"` (valen `5`, `5` y `100`), `" 5"`, `"5 "`, `"+5"`, `"-5"`, `"007"`, `"1e3"` y `"1.5e2"` (`1000` y `150`); se rechazan `"0x1A"`, `"5.7"`, `".5"` y un entero que desborda 64 bits (`"9223372036854775808"`). Un número cuyo valor es entero se acepta como entero; solo se rechaza el que tiene parte decimal. En un `?float` se acepta `".5"` (`0.5`); un entero grande como `"9223372036854775807"` pierde precisión por ser float.

**Errores y mensajes:** `$obj->_error->errFields()` lista los campos con error (el mensaje no nombra el campo) y `(string) $obj->_error` devuelve los mensajes. Por defecto: `int` → «Este campo debe ser un número entero.», `float` → «Este campo debe ser un número.», `string` → «Este campo debe ser texto.» (y «Este campo no es válido.» para cualquier otro tipo). Se registran en el constructor, antes de que corra `_init_()`, por eso el modelo los cambia **redeclarando la propiedad `_typeMessages` en el cuerpo de la clase** (no desde `_init_()`), con las tres claves:

```php
class Product extends ActiveRecord {
    public $_typeMessages = [
        'int'    => 'Solo enteros.',
        'float'  => 'Solo números.',
        'string' => 'Solo texto.',
    ];
    public ?int $stock = null;
}
```

`_typeMessages` no es una columna: no se guarda ni aparece en `getArray()`, en `json_encode()` ni en `getRawFields()`/`getFields()`. El resto de los mensajes del framework (`validate[...]`, etc.) sigue en inglés y se sobrescribe por campo con la clave `message`.

**Límite:** la asignación directa a una propiedad tipada declarada (`$obj->stock = 'abc'`) la resuelve PHP y lanza `TypeError`; el framework no la intercepta. Valide antes de asignar, o pase los datos por `Niu([...])`.

**Lectura de la base:** un `NULL` de la base se lee como `0` en columnas numéricas y como `""` en las demás (`PDO::NULL_TO_STRING`). Las columnas `INT`, `BIGINT`, `FLOAT`, `DECIMAL` y `NUMERIC` se leen como número.

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

> **Estado real de las relaciones** (verificado en `Core_General_Class::__call`):
> - `belongs_to` funciona correctamente.
> - `has_one` está **declarado pero no cableado** en el resolutor `__call`
>   (solo `belongs_to`, `has_many` y `has_many_and_belongs_to` filtran por la
>   foreign key); un accessor `has_one` devuelve registros sin filtrar.
> - El accessor `has_many` **funciona correctamente con modelos namespacados**.
>   El bug histórico está **corregido** (ver
>   `.kiro/bugs/FWK-001-has-many-namespaced-models.md`, Cerrado). El accessor
>   debe llamarse con el mismo casing con que se declara la relación; por
>   convención, en minúscula (`$this->has_many = ['comments']` →
>   `$product->comments()`).

## Validaciones

Las validaciones se declaran como arrays en la propiedad `$this->validate`,
indexada por el tipo de validación. Se ejecutan automáticamente en `Save()`.
**No existe un método `validates_presence_of()`** — la configuración es siempre
por array.

```php
public function _init_(): void {
    // Forma simple: lista de campos (usa el mensaje por defecto)
    $this->validate['presence_of'] = ['name', 'email'];

    // Con mensaje personalizado: cada entrada es un array ASOCIATIVO
    // con las claves 'field' y 'message'
    $this->validate['presence_of'] = [
        ['field' => 'name',  'message' => 'El nombre es obligatorio'],
        ['field' => 'email', 'message' => 'El email es obligatorio'],
    ];
}
```

Tipos de validación disponibles (todos bajo `$this->validate[...]`):

| Clave | Qué valida | Forma de cada entrada |
| ----- | ---------- | --------------------- |
| `presence_of` | Campo presente, no vacío ni `null` | `'campo'` **o** `['field' => 'campo', 'message' => '...']` |
| `email`       | Formato de email válido | `'campo'` **o** `['field' => 'campo', 'message' => '...']` |
| `numeric`     | Valor numérico | `'campo'` **o** `['field' => 'campo', 'message' => '...']` |
| `unique`      | Sin duplicados en la tabla | **siempre** `['field' => 'campo', 'message' => '...']` |

> **Importante:** cuando una entrada se da como array debe ser **asociativa**
> (`['field' => ..., 'message' => ...]`). Una entrada **posicional** como
> `['campo', 'mensaje']` lanza `Field key must be defined in array` —
> el código lee `$entry['field']`, no `$entry[0]`
> (ver `_ValidateOnSave()` en `bin/dumbophp.php`).

Si la validación falla, `Save()` retorna `false`. Para inspeccionar el error:
`(string) $obj->_error` (mensajes) o `$obj->_error->errFields()` (campos que
fallaron).

## Hooks del ciclo de vida

Los hooks se declaran como **arrays de nombres de métodos** del propio modelo.
Cada método se invoca en el momento correspondiente del ciclo de
`Save()` / `Find()` / `Delete()`. Si un hook activa `$this->_error`, la
operación se aborta y `Save()` retorna `false`.

```php
public function _init_(): void {
    $this->before_save   = ['sanitizeName'];
    $this->before_insert = ['setDefaults'];
    $this->after_insert  = ['notifyCreated'];
    $this->after_find    = ['decorate'];
}

public function sanitizeName(): void {
    $this->name = htmlentities((string) $this->name);
}
```

Hooks disponibles (todos arrays de nombres de método):

| Hook | Cuándo se ejecuta |
| ---- | ----------------- |
| `before_save`   | Antes de cualquier `Save()` (insert o update) |
| `before_insert` | Antes de un INSERT |
| `before_update` | Antes de un UPDATE |
| `after_insert`  | Después de un INSERT |
| `after_update`  | Después de un UPDATE |
| `after_save`    | Después de cualquier `Save()` |
| `before_delete` / `after_delete` | Alrededor de `Delete()` |
| `before_find` / `after_find`     | Alrededor de `Find()` |

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
