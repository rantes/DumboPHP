# BUG-001 — `has_many` falla con modelos namespacados

- **Estado:** Cerrado — fix aplicado en `__call()` (2026-06-08)
- **Componente:** `bin/dumbophp.php` → `Core_General_Class::__call()`
- **Severidad:** Alta (rompe una feature anunciada del ORM)
- **Descubierto por:** Suite de auto-tests (`tests/`), probe empírico durante BUG sweep

## Descripción

Las relaciones `belongs_to` funcionan correctamente, pero las relaciones
`has_many` lanzan una excepción o construyen una columna inexistente cuando el
modelo tiene namespace (todos los modelos en DumboPHP viven en `App\Models\`).

Reproducción (entorno `tests/`):

```php
$user = (new App\Models\User())->Niu(['name' => 'Ana', 'email' => 'ana@x.com']);
$user->Save();
$user->Posts();   // has_many declarado como ['Posts']
// Fatal error: Class "App\Models\Posts" not found
```

```php
$post->User();    // belongs_to -> OK, devuelve el usuario correcto
```

## Causa raíz

En `Core_General_Class::__call()` (aprox. línea 876 de `bin/dumbophp.php`):

1. **Resolución de clase.** Se construye el nombre de la clase relacionada con
   `Camelize($ClassName)` sobre el nombre **plural** de la llamada. Para
   `$user->Posts()` produce `App\Models\Posts` en lugar de `App\Models\Post`.

   ```php
   $classFromCall = Camelize($ClassName);          // 'Posts'
   $className     = "App\\Models\\{$classFromCall}"; // App\Models\Posts  ← no existe
   $obj1          = new $className();                // Fatal
   ```

2. **Prefijo de la foreign key.** Aun llamando en singular, el prefijo de la FK
   se deriva de `unCamelize(get_class($this))`, que **no quita el namespace**:

   ```php
   $prefix = unCamelize(get_class($this));   // 'app\_models\_user'
   $conditions = "`{$prefix}_id`='...'";     // `app\_models\_user_id`  ← columna inexistente
   ```

   `belongs_to` no sufre esto porque su rama usa
   `foreign = strtolower($field)."_id"` (p. ej. `user_id`) y no toca `$prefix`.

## Fix propuesto

Dos cambios mínimos en `Core_General_Class::__call()`:

```php
// 1) Resolver la clase del modelo desde el nombre SINGULAR
-$classFromCall = Camelize($ClassName);
+$classFromCall = Camelize(Singulars(strtolower($ClassName)));

// 2) Derivar el prefijo de la FK desde el nombre corto de la clase (sin namespace)
-$prefix = unCamelize(get_class($this));
+$classParts = explode('\\', get_class($this));
+$prefix     = unCamelize(end($classParts));
```

Con esto, `$user->Posts()` carga `App\Models\Post` y filtra por `user_id`.

## Impacto

- Afecta a **cualquier proyecto** que use `has_many` con modelos namespacados
  (es decir, prácticamente todos, dado el autoloader `App\Models\`).
- El fix es retrocompatible para `belongs_to` (no se toca esa rama) y para
  `has_many_and_belongs_to` (usa `$foreign`, no `$prefix`), pero **debe
  validarse** contra `has_many_and_belongs_to` y contra llamadas que ya
  pasaran el nombre en singular, antes de aplicarse.

## Cobertura en la suite

`tests/suites/TestRelations.php` cubre `belongs_to` y `has_many`. Los
*characterization tests* que documentaban el comportamiento roto se convirtieron
en aserciones positivas (`hasManyTest`, `hasManyNestedTest`) que validan
`$user->Posts()->counter() === 1` y `$post->Comments()->counter() === 1`.

## Resolución (2026-06-08)

Fix aplicado en `Core_General_Class::__call()` (`bin/dumbophp.php`), exactamente
como se propuso:

1. La clase relacionada se resuelve desde el nombre **singular**:
   `$classFromCall = Camelize(Singulars(strtolower($ClassName)));`
2. El prefijo de la FK se deriva del nombre **corto** de la clase (sin
   namespace): `explode('\\', get_class($this))` + `end()` + `unCamelize()`.

`belongs_to` y `has_many_and_belongs_to` no se vieron afectados (no tocan
`$prefix`). `php tests/run.php` → 0 fallos; `php tests/verify_timothy.php` →
exit 0.
