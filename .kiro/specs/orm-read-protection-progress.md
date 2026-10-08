# ORM — protección de lecturas por valor, fase 1 (solo seguridad) — Checkpoint 2026-10-08

Rama: `fix/orm-read-protection` (desde `master` `79ca0c2`). Sin push ni merge. `/etc/dumbophp` NO tocado. Sin API nueva.
Contexto y evidencia: `~/web/komodo/.kiro/specs/orm-mecanismo-existente.md` y `orm-conditions-escape-design.md` v2.

## Estado: ⏸ DETENIDO a la espera de una decisión (tarea 2, el escape de VALORES)
La regla de trabajo pide detenerse si enlazar parámetros es más seguro y de esfuerzo comparable. Lo es (ver "Decisión pendiente"). Todo lo que NO depende de esa decisión ya está hecho.

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

## Decisión pendiente — cómo proteger el VALOR (tarea 2)
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

## Pendiente (depende de la decisión)
Escape/enlace de valores en `_buildConditions` (`Find_by_*`, array, `IN`, `BETWEEN`), `Paginate`, tests de comilla simple Y barra invertida, casos legítimos (`O'Brien`, `%`, `_`, NULL, vacío, Unicode, números como cadena, espacios dobles), guía en README, flag en `src/host.php`.
