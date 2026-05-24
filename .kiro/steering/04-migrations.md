# DumboPHP — Migraciones

## Estructura de una migración

**Archivo**: `migrations/create_<tabla_plural>.php`  
**Clase**: `Create<TablaPlural> extends Migrations`  
**Namespace**: `Migrations`

```php
<?php
namespace Migrations;
use DumboPHP\Migrations;

class CreateProducts extends Migrations {
    public function _init_(): void {
        $this->_fields = [
            ['field'=>'id',          'type'=>'INTEGER', 'null'=>'false', 'limit'=>'11', 'primary'=>true, 'autoincrement'=>true],
            ['field'=>'name',        'type'=>'VARCHAR',  'null'=>'false', 'limit'=>'255', 'default'=>''],
            ['field'=>'description', 'type'=>'TEXT',     'null'=>'true'],
            ['field'=>'price',       'type'=>'FLOAT',    'null'=>'false', 'default'=>'0'],
            ['field'=>'stock',       'type'=>'INTEGER',  'null'=>'false', 'limit'=>'11', 'default'=>'0'],
            ['field'=>'category_id', 'type'=>'INTEGER',  'null'=>'false', 'limit'=>'11', 'default'=>'0'],
        ];
    }

    public function up(): void {
        $this->Create_Table();
    }

    public function down(): void {
        $this->Drop_Table();
    }
}
```

## Tipos de campo disponibles

| Tipo en migración | Tipo SQL generado | Notas |
|-------------------|-------------------|-------|
| `INTEGER` | `INTEGER(11)` | Entero estándar |
| `BIGINT` | `BIGINT` | Entero grande |
| `VARCHAR` | `VARCHAR(255)` | String, limit recomendado |
| `TEXT` | `TEXT` | Texto largo |
| `FLOAT` | `FLOAT` | Decimal |

## Definición de campos

Cada campo es un array asociativo con las siguientes claves:

```php
[
    'field'         => 'nombre_columna',   // requerido
    'type'          => 'VARCHAR',          // requerido
    'null'          => 'false',            // 'true' o 'false' (string)
    'limit'         => '255',              // opcional, tamaño
    'default'       => '',                 // opcional, valor por defecto
    'primary'       => true,               // solo para PK
    'autoincrement' => true,               // solo para PK
    'comment'       => 'descripción',      // opcional
]
```

## Métodos disponibles en Migrations

```php
$this->Create_Table();                          // Crea la tabla con $_fields
$this->Drop_Table();                            // Elimina la tabla
$this->Add_Column(['field'=>'x', 'type'=>'Y']); // Agrega columna
$this->Alter_Column(['field'=>'x', 'type'=>'Y']);// Modifica columna
$this->Remove_Column('nombre_columna');          // Elimina columna
$this->Add_Index('nombre_idx', 'campo');         // Agrega índice nombrado
$this->Add_Single_Index('campo');                // Agrega índice simple
$this->Remove_Index('nombre_idx');               // Elimina índice
$this->Add_Primary_Key('campo');                 // Agrega PK
```

## Ejecutar migraciones desde CLI

```bash
# Ejecutar up de una migración específica
dumbo migration up products

# Ejecutar down de una migración específica
dumbo migration down products

# Reset (down + up) de una migración
dumbo migration reset products

# Ejecutar up de todas las migraciones
dumbo migration up all

# Reset de todas las migraciones
dumbo migration reset all
```

## Seeds

Los seeds permiten poblar la BD con datos iniciales.

**Archivo**: `migrations/seeds.php`  
**Clase**: `Seeds extends Controller`  
**Namespace**: `Migrations`

```php
<?php
namespace Migrations;
use DumboPHP\Controller;

class Seeds extends Controller {
    public function sow(): void {
        // Usar modelos directamente para insertar datos
        $category = $this->Category->Niu();
        $category->name = 'Electronics';
        $category->Save();

        $product = $this->Product->Niu();
        $product->name        = 'Sample Product';
        $product->price       = '9.99';
        $product->category_id = $category->id;
        $product->Save();
    }
}
```

Ejecutar seeds:
```bash
dumbo migration sow
```

Generar archivo seeds vacío:
```bash
dumbo generate seed
```

## Generación automática de migraciones

El CLI genera la migración y la ejecuta automáticamente:

```bash
# Sintaxis: dumbo generate model <tabla> <campo:tipo> [<campo:tipo> ...]
dumbo generate model products name:string description:text price:float stock:integer category_id:integer

# Tipos disponibles en CLI: primary, integer, biginteger, string, text, float, decimal
# Con tamaño personalizado: name:string{100}
# Con null permitido: description:text:null
# Con valor por defecto: stock:integer:default{0}
```

## Convención de nombres de archivos

- Archivo: `create_<tabla_plural_snake_case>.php` → `create_user_profiles.php`
- Clase: `Create<TablaPlural>` → `CreateUserProfiles`
- La tabla en BD es siempre el plural del nombre del modelo en snake_case
