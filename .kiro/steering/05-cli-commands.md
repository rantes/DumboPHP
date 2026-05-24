# DumboPHP — CLI (dumbo shell)

## Uso general

```bash
dumbo <comando> <opción> <params> [--opcion=valor]
```

El CLI debe ejecutarse **desde la raíz del proyecto** (donde está `config/host.php`).

## Comandos

### create — Crear nuevo proyecto

```bash
dumbo create <nombre-proyecto>
```

Crea la estructura completa de directorios y archivos base:
- `app/controllers/`, `app/models/`, `app/views/`, `app/webroot/`
- `app/webroot/css/`, `js/`, `images/`, `fonts/`, `plugins/`
- `config/host.php`, `config/db_settings.php`
- `.htaccess`, `.env.example`
- `app/views/layout.phtml`
- `migrations/`

---

### generate — Generar archivos

```bash
# Scaffold completo (modelo + migración + controlador + vistas CRUD)
dumbo generate scaffold <tabla> <campo:tipo> [<campo:tipo> ...]

# Solo modelo (con migración automática)
dumbo generate model <tabla> <campo:tipo> [<campo:tipo> ...]

# Modelo sin migración
dumbo generate model <tabla> no-migration <campo:tipo> [...]

# Controlador con acciones específicas
dumbo generate controller <nombre> <accion1> <accion2> ...

# Archivo de seeds vacío
dumbo generate seed
```

**Tipos de campo para CLI**: `primary`, `integer`, `biginteger`, `string`, `text`, `float`, `decimal`

**Modificadores de campo**:
- `nombre:string{100}` — tamaño personalizado
- `descripcion:text:null` — permite NULL
- `stock:integer:default{0}` — valor por defecto

**Ejemplos**:
```bash
dumbo generate scaffold products name:string price:float stock:integer:default{0} category_id:integer
dumbo generate model user_profiles first_name:string last_name:string email:string{100} bio:text:null
dumbo generate controller dashboard stats reports
```

---

### destroy — Eliminar archivos generados

```bash
# Elimina modelo, migración, controlador y vistas
dumbo destroy scaffold <tabla>

# Elimina solo modelo y migración
dumbo destroy model <tabla>

# Elimina solo controlador
dumbo destroy controller <nombre>
```

---

### migration — Gestionar migraciones

```bash
# Ejecutar up (crear tabla)
dumbo migration up <tabla>
dumbo migration up all

# Ejecutar down (eliminar tabla)
dumbo migration down <tabla>
dumbo migration down all

# Reset (down + up)
dumbo migration reset <tabla>
dumbo migration reset all

# Ejecutar seeds
dumbo migration sow
```

---

### db — Operaciones de base de datos

```bash
# Exportar datos de un modelo a archivo dump
dumbo db dump <modelo>
dumbo db dump all

# Importar datos desde archivo dump
dumbo db load <modelo>
dumbo db load all
```

---

### run — Ejecutar una acción desde CLI

Útil para testing manual o scripts:

```bash
# Ejecutar controller/action
dumbo run index/index
dumbo run product/index
dumbo run product/show id=5
dumbo run user/create name=John email=john@example.com
```

---

## Opciones globales

| Opción | Descripción | Ejemplo |
|--------|-------------|---------|
| `--env=<entorno>` | Fuerza un entorno específico | `--env=test` |
| `--halt=true` | Detiene ejecución en el primer error | `--halt=true` |
| `--standalone=true` | Modo standalone (sin vendor) | `--standalone=true` |
| `--watch=true` | Modo watch (para tests) | `--watch=true` |
| `--help` | Muestra ayuda | `--help` |

---

## dumboTest — Ejecutar tests

```bash
# Ejecutar todos los tests
dumboTest all

# Ejecutar tests específicos (separados por espacio)
dumboTest ProductTest
dumboTest ProductTest UserTest OrderTest

# Con opciones
dumboTest all --halt=true
dumboTest all --dir=tests/
dumboTest all --watch=true   # modo watch, re-ejecuta al guardar
```

Requiere **XDebug** instalado. Genera:
- `test-result.xml` — reporte JUnit
- `coverage.xml` — reporte de cobertura Clover
- `tmp/logs/unit_testing.log` — log detallado

---

## Configuración del entorno (.env)

El archivo `.env` en la raíz del proyecto (nunca en git):

```ini
[environment]
APP_ENV=dev

[app_values]
INST_URI=https://localhost/myproject/
SALT=cambia_este_valor_siempre
SITE_NAME="Mi Aplicación"

[db_settings]
DB_DRIVER=mysql
DB_HOST=localhost
DB_CHARSET=utf8
DB_DIALECT=2
DB_PORT=3306
DB_SCHEMA=mi_base_de_datos
DB_USERNAME=usuario
DB_PASSWORD=contraseña
DB_UNIX_SOCKET=/tmp/mysql.sock
```

Para SQLite:
```ini
DB_DRIVER=sqlite
DB_SCHEMA=/ruta/absoluta/mi_base.sqlite
# Para SQLite en memoria (solo tests):
DB_SCHEMA=memory
```

El entorno `test` en `config/db_settings.php` usa SQLite en memoria por defecto.
