# DumboPHP — Visión General del Proyecto

## ¿Qué es DumboPHP?

DumboPHP es un framework MVC en PHP 8.1+ de bajo overhead, inspirado en Ruby on Rails. Su filosofía central es que **el framework debe desaparecer**: el desarrollador se enfoca en el qué, no en el cómo.

## Principios de diseño (no negociables)

1. **DRY** — No repetir lógica. Si algo se repite dos veces, se abstrae.
2. **KISS** — La solución más simple que funcione. Sin over-engineering.
3. **Duck Typing** — Si camina como pato y grazna como pato, es un pato. No se fuerzan tipos donde no es necesario.
4. **Lazy Model Load** — Los modelos se cargan bajo demanda vía `__get()` en el Controller. Nunca se instancian manualmente en el controlador.
5. **Active Record optimizado** — El modelo ES la tabla. Hereda de `ActiveRecord`, que extiende `ArrayObject`.
6. **ArrayObject para datasets** — Los resultados de BD son `ArrayObject`, iterables directamente con `foreach`.
7. **Muy bajo overhead** — Sin capas innecesarias, sin magia costosa, sin dependencias externas salvo PDO y JSON.
8. **Diseño inspirado en Rails** — Convención sobre configuración. Nombres predecibles. Scaffolding funcional.

## Stack técnico

- **PHP 8.1+** con PDO
- **Sin Composer en runtime** — el framework puede funcionar standalone (`dumbophp.php`)
- **Drivers de BD**: MySQL, PostgreSQL, SQLite (incluyendo sqlite2, sqlite3, `:memory:`)
- **CLI**: `dumbo` (gestión del proyecto) y `dumboTest` (ejecución de tests con XDebug)
- **Testing**: Framework propio `Timothy` (`dumboTests` + `testDispatcher`), genera XML JUnit y coverage Clover

## Estructura de un proyecto DumboPHP

```
mi-proyecto/
├── .env                    # Variables de entorno (nunca en git)
├── .env.example            # Plantilla del .env
├── .htaccess               # Redirige todo a app/webroot/
├── app/
│   ├── controllers/        # *_controller.php — clase *Controller extends Controller
│   ├── helpers/            # Helpers opcionales
│   ├── models/             # *.php — clase * extends ActiveRecord
│   └── views/
│       ├── layout.phtml    # Layout principal con <?=$this->yield;?>
│       └── <controller>/   # Una carpeta por controlador
│           └── *.phtml     # Vistas (PHP corto: <? ?> y <?= ?>)
│   └── webroot/
│       ├── .htaccess       # Rewrite rules internas
│       ├── index.php       # Punto de entrada HTTP
│       ├── css/ js/ images/ fonts/ plugins/
├── config/
│   ├── host.php            # Constantes de la app (APP_ENV, INST_URI, etc.)
│   └── db_settings.php     # Configuración de BD por entorno
├── migrations/             # Clases de migración
│   └── create_<tabla>.php
├── tests/                  # Tests unitarios (extienden dumboTests)
├── tmp/                    # Logs y archivos temporales (auto-creado)
└── vendor/                 # Composer (opcional)
```

## Namespaces

| Namespace | Ubicación |
|-----------|-----------|
| `DumboPHP\` | Framework core (`dumbophp.php`, `lib/`) |
| `App\Controllers\` | `app/controllers/` |
| `App\Models\` | `app/models/` |
| `Migrations\` | `migrations/` |
| `tests\` | `tests/` |

## Autoloading

El autoloader convierte `App\Models\UserProfile` → `app/models/user_profile.php` usando `unCamelize()`. Los modelos usan nombre singular en snake_case como nombre de archivo.
