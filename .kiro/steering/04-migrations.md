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
$this->Create_Table();                                   // Crea la tabla con $_fields (IF NOT EXISTS)
$this->Drop_Table();                                     // Elimina la tabla (IF EXISTS)
$this->Add_Column(['field'=>'x', 'type'=>'INTEGER']);    // Agrega columna
$this->Alter_Column(['field'=>'x', 'type'=>'INTEGER']);  // Redefine una columna existente
$this->Remove_Column('x');                               // Elimina columna
$this->Add_Index(['name'=>'idx_a_b', 'fields'=>['a','b']]); // Índice nombrado (name y fields obligatorios; fields es array)
$this->Add_Single_Index('campo');                        // Índice simple sobre un campo
$this->Remove_Index('idx_a_b');                          // Elimina índice
```

## Cambiar el esquema de una tabla con datos

No hay migraciones con versiones ni registro de migraciones aplicadas: el versionador es git. La clase
`Create<Tabla>` describe siempre el esquema **final** (instalaciones nuevas). Para una base que ya tiene datos, el
cambio se escribe con las operaciones de alteración dentro de `alter()` (hoy vacío en la clase base), se ejecuta
una vez y luego se retira.

```php
public function alter() {
    $this->Add_Column(['field' => 'qty', 'type' => 'INTEGER', 'default' => '0']);
}
```

`alter()` no se ejecuta con `dumbo migration up|down|reset|all`. Se ejecuta con `dumbo migration alter <tabla>`
(llama a `alter()` solo) o con `dumbo migration run <tabla>` (`up()` y luego `alter()`).

Parámetros de columna: `field` y `type` obligatorios; opcionales `limit`, `default` y `null`. `null => false` o
`'false'` produce `NOT NULL`; cualquier otro valor, o su ausencia, deja la columna nullable.

| Operación | Si ya está hecha | Si no se puede |
|---|---|---|
| `Add_Column` | no hace nada (la columna ya existe) | — |
| `Remove_Column` | no hace nada (la columna no existe) | SQLite no quita columnas indexadas, PRIMARY KEY o UNIQUE |
| `Alter_Column` | redefine siempre | columna inexistente: `Exception` «Can not alter column `x`: it does not exist in table `t`.» |
| `Add_Index`, `Add_Single_Index`, `Remove_Index` | no hacen nada si el índice ya existe / no existe | — |

Todo lo anterior está cubierto por `tests/suites/TestMigrationsAlter.php` y se comprobó **solo en SQLite**.
El driver MySQL se corrigió por lectura y **no se ha ejecutado**; en MySQL `Alter_Column` usa `MODIFY`, que redefine
la columna completa (hay que repetir tipo, límite, nulabilidad y valor por defecto).

### Limitaciones de SQLite

- `Add_Column` con `null => false` exige un `default` no nulo (lo impone el motor).
- SQLite no tiene `MODIFY COLUMN`: `Alter_Column` reconstruye la tabla en una transacción (renombra, crea de
  nuevo, copia las filas, borra la copia y recrea los índices). Solo conserva `PRIMARY KEY` de una columna y
  `AUTOINCREMENT`; `UNIQUE`, `CHECK`, claves foráneas y triggers del `CREATE TABLE` original se pierden, y no
  admite claves primarias compuestas. Si alguna fila no cabe en la nueva definición (p. ej. `NULL` en una columna
  que pasa a `NOT NULL`) la copia falla y se revierte.
- `Remove_All_indexes()` no funciona en SQLite (el driver devuelve `PRAGMA index_list`, que no trae `INDEX_NAME`).
- No probado: PostgreSQL (su driver conserva los mismos patrones rotos) ni `Add_Index` compuesto en MySQL.

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
