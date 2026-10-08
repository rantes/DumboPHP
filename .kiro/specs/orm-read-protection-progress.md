# ORM — protección de lecturas por valor, fase 1 (solo seguridad) — Checkpoint 2026-10-08

Rama: `fix/orm-read-protection` (desde `master` `79ca0c2`). Sin push ni merge. `/etc/dumbophp` NO tocado. Sin API nueva.
Contexto y evidencia: `~/web/komodo/.kiro/specs/orm-mecanismo-existente.md` y `orm-conditions-escape-design.md` v2.

## Estado: ✅ Fase 1 implementada con parámetros ENLAZADOS (decisión B, 2026-10-08) — constante APAGADA por defecto
Commits: `f700ff2` (campo/operador, pk, unique), `8ddbd08` (checkpoint, harness MySQL), `dbfc905` (enlace de valores, ORDER BY, LIMIT, Paginate, tests, guía). Sin push.

## Línea base (master `79ca0c2`)
`php tests/verify_timothy.php`: 25 ok, 0 fallos. `php tests/run.php`: 12 suites, 86 tests, 121 aserciones, 0 fallos (el runner no imprime totales; conteo sumando la salida `--verbose`).

## Hecho (commit `f700ff2` y siguientes)
| Tarea | Qué | Dónde |
|---|---|---|
| 3 Campo y operador | Con `DUMBO_QUOTE_CONDITIONS` activa, `_buildConditions` exige: campo = columna real del modelo (o su pk/`rowid`) o `tabla.campo` (JOIN; si la tabla es la del modelo la columna debe existir; la otra tabla solo se valida como identificador); operador ∈ {`= != <> < > <= >= LIKE NOT LIKE IN NOT IN BETWEEN`} (insensible a mayúsculas y espacios). Si no, `QueryConditionException`; nunca se interpola. Cubre `Find_by_*`, condiciones en array y `Paginate` (mismo punto). | `bin/dumbophp.php` `_readsProtected`, `_validConditionField`, `_validConditionOperator`, `_buildConditions` |
| 4 `Save()` y el pk | El UPDATE enlaza el pk (`:__pk`) igual que los valores de `SET`; los 3 drivers aceptan `bindings` interno; el `Update()` público lo descarta (no es API). Sin depender de la bandera. | `bin/dumbophp.php` Save/Update, `lib/db_drivers/{mysql,sqlite,postgresql}.php` |
| (hallazgo) `unique` | `validate['unique']` interpolaba el VALOR del usuario en un WHERE dentro de `Save()` (`campo='valor' AND pk<>'…'`): inyección en la ESCRITURA. Ahora usa la forma array, así hereda el arreglo de valores cuando se implemente. | `bin/dumbophp.php` `_ValidateOnSave` |
| 7 Harness | `DUMBO_QUOTE_CONDITIONS=1 php tests/run.php` corre TODA la suite con la protección; `DUMBO_TEST_MYSQL=1 …` la corre contra MySQL (`tests/config/db_settings.php`). | `tests/bootstrap.php`, `tests/config/db_settings.php` |
| Tests | `tests/suites/TestReadProtection.php` (pk enlazado — falla con el código anterior, comprobado —, campo/operador inválidos, listas blancas, JOIN calificado, ida y vuelta con comilla). | `tests/suites/` |
Resultado: `php tests/run.php` y `DUMBO_QUOTE_CONDITIONS=1 php tests/run.php` → PASS.

Nota sobre el pk: `ActiveRecord::$id` está tipado `?int`, así que con `id` estándar el pk NO era inyectable; solo lo era con un pk personalizado de tipo string (`public string $pk`). Es endurecimiento, no una vulnerabilidad explotable en proyectos estándar.

## Decisión tomada — cómo proteger el VALOR: B (enlace). Comparación original:
El hallazgo que cambia el análisis: **`Connection` fija `ATTR_EMULATE_PREPARES=false`** (`dumbophp.php:714`), es decir, prepared statements NATIVOS. Las escrituras ya enlazan, y el
`getData($prepared, $data)` (l.1165) ya ejecuta `execute($data)`. Por tanto el enlace es parametrización real del servidor.

| | A) `quote()` centralizado | B) parámetros enlazados |
|---|---|---|
| Seguridad | Depende de `PDO::quote` (charset, `NO_BACKSLASH_ESCAPES`); el valor viaja dentro del SQL | **El valor nunca se mezcla con el SQL**; independiente de charset/modo SQL; mismo mecanismo que las escrituras |
| Valores con espacios dobles / saltos de línea | **Se corrompen**: `QueryCondition` colapsa `\s{2,}` y `\n` en TODO el string, también dentro de literales (l.107) ⇒ `"a  b"` se buscaría como `"a b"`. Habría que saltarse esa normalización. | No afecta (el valor no está en el string) |
| Cambios | `_buildConditions` (+ saltar normalización en `QueryCondition`); `Paginate` no cambia (el string ya lleva literales) | `_buildConditions` (placeholders con nombre `:__cN`), `QueryCondition` (lleva bindings), `_prepareSelectParams`/`Find`/`Paginate` (fusionar y pasar bindings a la consulta de conteo), `Select` de los 3 drivers (rama array), `_sqlQuery` pasa a mostrar placeholders |
| Riesgo de compatibilidad | Bajo: `_sqlQuery` sigue con valores | Medio: `_sqlQuery` con placeholders; condiciones string del llamador con `:palabra` fuera de comillas colisionarían con placeholders con nombre |
| Esfuerzo estimado | ~0,5 día | ~1 día (incluye drivers y `Paginate`) |
| MySQL / barra invertida | Hay que probarlo en MySQL (depende de `quote`) | Inmune por construcción (igual se prueba en MySQL) |
**Recomendación:** B (enlace), por seguridad estructural, por la corrupción de espacios en A y porque la infraestructura (`getData`, drivers de escritura) ya existe. A sigue siendo viable como atajo.
Ambas protegen `Find_by_*`, las condiciones en array y `Paginate` en el único punto `_buildConditions`; ninguna requiere API pública nueva ni cambios en las apps.

## Tarea 6 — escapes manuales dentro del framework (doble comillado)
`grep` en `bin/`, `lib/`, `src/` y las plantillas del generador: **cero** `addslashes`, `real_escape_string`, `quote()` o `str_replace("'")`. Los scaffolds (`lib/DumboGeneratorClass.php:231-252`) pasan `params['id']` a `Find()`/`Delete()` y `$_POST[...]` a `Niu()`: `Find($id)` castea a entero (mysql.php:37-52) y `Save()` enlaza; no escapan a mano. **Riesgo de doble comillado dentro del framework: ninguno.**
Otros sumideros internos que interpolan datos del objeto (no se tocaron; fuera del alcance acordado, a decidir):
- `__call` relaciones (`dumbophp.php` l.911-916): `'{$this->pk}'`/FK de la fila cargada (ids de BD; riesgo 2.º orden).
- `_delete_or_nullify_dependents` (`" = '{$id}'"`) y `Delete` de los drivers con ids en array (`implode(',', $conditions)`, mysql.php:157).
- Condiciones string del llamador, `join/group/sort/limit/fields` y `Find_by_SQL`: SQL del llamador por diseño.

## Qué NO se verificó
- **MySQL (todo).** No hay credenciales utilizables aquí (la clave del dev vive en `.env.secrets`; no la usé) ni una base desechable. Quedan SIN verificar en MySQL: la barra invertida (`x\' OR 1=1 -- -`, `\`, `\\`), `NO_BACKSLASH_ESCAPES`, charsets multibyte, el UPDATE con `:__pk` nativo, y toda la suite. Comando para correrlo tú (base desechable; la suite hace DROP/CREATE de sus tablas):
  ```
  mysql -u root -p -e "CREATE DATABASE dumbo_test CHARACTER SET utf8mb4"
  cd ~/web/DumboPHP
  DUMBO_TEST_MYSQL=1 DUMBO_TEST_DB_SCHEMA=dumbo_test DUMBO_TEST_DB_USER=<usuario> DUMBO_TEST_DB_PASS=<clave> php tests/run.php
  DUMBO_TEST_MYSQL=1 DUMBO_QUOTE_CONDITIONS=1 DUMBO_TEST_DB_SCHEMA=dumbo_test DUMBO_TEST_DB_USER=<usuario> DUMBO_TEST_DB_PASS=<clave> php tests/run.php
  ```
  (Las suites no se han corrido nunca contra MySQL: pueden fallar por diferencias de dialecto ajenas a este cambio; compáralas con `master`.)
- PostgreSQL: el cambio de driver es idéntico pero no se ejecutó.

## Valor por defecto de la constante y verificaciones previas (tarea 5 y "Verificación en las apps", solo informe)
`DUMBO_QUOTE_CONDITIONS` queda **sin definir = apagada** en el framework (comportamiento idéntico al actual). Recomendado:
| Proyecto | Valor | Antes de activar verificar |
|---|---|---|
| Plantilla `src/host.php` (proyectos nuevos) | `true` — se añade cuando se implemente la fase de valores | — |
| **Komodo** | `true` | (1) Sus condiciones en array/`Find_by_*` ya reciben el valor crudo y no escapan a mano; los 19 usos de `DB->quote` que quedan están en condiciones STRING (`seat_slots_trait` ×8, `admin_base_trait` ×2, `admin_controller` ×6, `seat_request` ×3), que ni A ni B tocan ⇒ ninguno se rompe. (2) **`tests/e2e/OrmProtectionProbe.php` hay que ajustarlo**: usa el campo `probe_field`, que con la bandera activa NO es columna de `params` ⇒ `QueryConditionException`; debe usar `name` (columna real) y capturar la excepción como "no protege". (3) Correr `dumboTest all` con la bandera: los 7 tests `PENDIENTE-FRAMEWORK` deben activarse solos y pasar; la barra invertida solo en MySQL. (4) Campos usados en sus arrays: todos son columnas reales o `tabla.campo` de JOIN (revisión estática); cualquier otro lanza. |
| **Gecko** | `true` (primero: protege el login público `portal_controller.php:48`) | Sus 4 strings con `{$id}` son `int` tipado (no cambian). Correr su suite con la bandera. |
| **Iguana** | `true` | `api_controller.php:~410-412` escapa a mano con lista blanca dentro de un STRING: no se rompe (A/B no tocan strings), pero conviene pasarlo a forma array. Los dos casos "por verificar" quedan **resueltos**: `$tenantId = (int)` (api_controller.php:400) y `$id = (int)` (operator_controller.php:275) ⇒ seguros. |
Antes de activar en cualquier app: `grep` de nombres de campo en condiciones array que no sean columnas (lanzarán), operadores fuera de la lista blanca, y suite completa con la bandera.


---

## Implementación B (commit `dbfc905`) — qué hace con `DUMBO_QUOTE_CONDITIONS = true`
Todo está en `bin/dumbophp.php`; los drivers no cambian salvo el `bindings` interno del UPDATE (commit anterior). Con la constante apagada el comportamiento es EXACTAMENTE el anterior (ruta legada intacta).
| Requisito | Implementación |
|---|---|
| Placeholders únicos por consulta | `_bindValue()` genera `:__c0`, `:__c1`… con el tamaño de `_queryBindings`; se limpia tras cada consulta (éxito y error) y ante una condición inválida (`_resetQueryState`). Test: `placeholdersAreUniquePerQueryTest`. |
| `IN` / `NOT IN` | `_inSql()`: un placeholder por elemento; lista vacía: `IN` → `1=0`, `NOT IN` → `1=1`; los `null` del listado se omiten; un escalar es una lista de uno. |
| `null` | `=` → `IS NULL`; `!=`/`<>` → `IS NOT NULL`; con otro operador o dentro de `BETWEEN` → `QueryConditionException`. **Cambio de comportamiento** frente al `= ''` anterior (solo con la constante activa). |
| `LIKE` / `NOT LIKE` | Se enlaza el patrón COMPLETO; `%` y `_` siguen siendo comodines (literales en `=`); el ORM no escapa el patrón (README documenta el `\%` de MySQL/PG; SQLite no tiene escape por defecto). |
| `BETWEEN` | Dos valores enlazados, ninguno `null`. |
| `ORDER BY` | `_safeOrderBy()`: columna del modelo, `tabla.col`, alias `AS` de `fields` o posición numérica + `ASC/DESC`; re-emitido canónico con backticks; expresiones/funciones lanzan. |
| `LIMIT` | `_safeLimit()`: `n` o `offset,n` enteros no negativos; el resto lanza. |
| `Paginate` | Captura los bindings tras `_prepareSelectParams`, ejecuta el conteo con ellos (`getData` directo) y los restaura para la consulta de la página. También valida `sort`. |
| `Save()` / `unique` | pk enlazado siempre; `unique` en forma array (hereda el enlace). |
| Valores no escalares | `QueryConditionException`. bool → `'1'/'0'`. |

## Tests
- `tests/suites/TestBoundConditions.php` (26 tests): comilla simple y barra invertida (`=`, `Find_by_`, `LIKE`, `IN`, `BETWEEN`), ida y vuelta de valores con `\`, `O'Brien`, `%`/`_` (literales en `=`, comodines en `LIKE`), vacío vs `NULL`, números como cadena/enteros/bool, Unicode, espacios dobles y saltos de línea, `IN` vacío, placeholders únicos, estado limpio tras errores, conector `OR`, `and()` mezclado, ORDER BY aceptado/rechazado, LIMIT, `Paginate` (conteo = página, inyección, estado), `unique` con comillas, soft delete y JOIN calificado. **Comprobado**: con el código previo (`git stash` de `bin/`) fallan por inyección real (`x' OR '1'='1` devuelve filas / error de sintaxis).
- `TestReadProtection` (campo/operador/pk) + `verify_timothy`.
- Resultado final (SQLite): `php tests/run.php` → PASS (122 tests, 172 aserciones, 0 fallos; constante apagada: las pruebas dependientes registran "bandera apagada"). `php tests/run.php --protected` → PASS (122 tests, 318 aserciones, 0 fallos). `verify_timothy`: 25/25.

## Validación contra Komodo (sin modificar la app ni `/etc`)
Suite completa de Komodo ejecutada con el framework de este repo (lanzador temporal con otra ruta de inclusión) y `DUMBO_QUOTE_CONDITIONS=1` por `auto_prepend_file`: **125 tests, 6426 aserciones, 0 fallos**, y los **7 tests `PENDIENTE-FRAMEWORK` se activaron solos y pasaron** (0 mensajes PENDIENTE).
Requisito detectado: el sondeo `tests/e2e/OrmProtectionProbe.php` de Komodo debe usar un campo real y capturar la excepción; sin eso la suite aborta con `Invalid condition field: probe_field`. Parche aplicado solo temporalmente para validar y revertido (Komodo quedó limpio):
```php
try {
    $build->invoke($model, [['name', "a'b"]]);        // 'name' es columna real de params
    $built = implode(' ', $store->getValue($model));
    $safe  = !str_contains($built, "'a'b'");
} catch (\Throwable $e) {
    $safe = false;                                    // la constante apagada también acaba aquí con un campo inválido
}
return $safe;
```
## SIN VERIFICAR en MySQL (sigue siendo así — la constante queda apagada hasta que lo corras)
Todo `TestBoundConditions` en MySQL, en particular: la barra invertida (`backslashPayloadsMatchNothingTest`, `backslashValuesRoundTripTest`), prepared statements nativos con `:__cN` y `:__pk`, `LIKE` con `\` (escape por defecto de MySQL), colación/mayúsculas en `=`, comparación de cadenas numéricas con columnas INTEGER, `ORDER BY` con backticks, `LIMIT a,b` literal (enlazar LIMIT no se usa a propósito), conteo de `Paginate` y charsets multibyte.
```
mysql -u root -p -e "CREATE DATABASE dumbo_test CHARACTER SET utf8mb4"
cd ~/web/DumboPHP
DUMBO_TEST_MYSQL=1 DUMBO_TEST_DB_SCHEMA=dumbo_test DUMBO_TEST_DB_USER=<u> DUMBO_TEST_DB_PASS=<p> php tests/run.php --protected
DUMBO_TEST_MYSQL=1 DUMBO_TEST_DB_SCHEMA=dumbo_test DUMBO_TEST_DB_USER=<u> DUMBO_TEST_DB_PASS=<p> php tests/run.php
```
Las suites nunca se han corrido contra MySQL: compara cualquier fallo con `master` antes de atribuirlo a este cambio. PostgreSQL: no ejecutado.

## Riesgos conocidos / decisiones
- `null` pasa de `= ''` a `IS NULL` (solo con la constante). Revisar llamadores que dependan de eso.
- Strings de `conditions` del llamador con `?` o `:nombre` fuera de comillas no pueden mezclarse con condiciones array bajo la constante (PDO no admite mezclar posicional y con nombre).
- `Find_by_<columna inexistente>` y campos desconocidos ahora lanzan (antes: error SQL).
- `_sqlQuery` muestra placeholders (`:__c0`), no valores.
- Sumideros internos no tocados: relaciones (`__call`), `_delete_or_nullify_dependents`, `Delete` con ids en array.