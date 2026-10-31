# Documentación Técnica — Sistema de Gestión de Liga de Básquetbol

| Campo | Valor |
|---|---|
| Proyecto | Liga de Básquetbol |
| Repositorio | <https://github.com/thefatrat12321-sys/Liga-de-basquetbol> |
| Framework | Laravel 12.70.2 |
| Lenguaje | PHP 8.2+ (plataforma fijada en 8.2.12) |
| Base de datos local | MariaDB 10.4.32 de XAMPP, puerto 3307 |
| Servidor local | Apache 2.4.58 + PHP 8.2.12 (XAMPP) |
| Pruebas | PHPUnit 11.5.57 — 29 pruebas, 104 aserciones |
| Última revisión | 30/10/2026 |
| Commit de referencia | `4553892` |

> Este documento describe la aplicación completa que se ejecuta en local. La copia
> publicada en GitHub Pages es únicamente una **vista estática de solo lectura**
> generada desde la base de datos local.

---

## 1. Descripción general

La aplicación permite administrar una liga de básquetbol: equipos, jugadores,
partidos, resultados y tabla de posiciones. Está construida con Laravel 12 y el
patrón MVC, con una capa de servicios para la lógica deportiva.

Hay dos formas de ejecutarla:

1. **Aplicación completa en local** con XAMPP + MySQL. Permite iniciar sesión,
   crear y editar registros, cargar resultados y consultar estadísticas.
2. **Vista previa estática** en `docs/`, publicada en GitHub Pages. Solo muestra
   la tabla de posiciones y los partidos; no tiene formularios ni rutas internas.

Los datos se modifican únicamente en local. Para actualizar la copia pública hay
que ejecutar `php artisan liga:preview` y subir los cambios de `docs/`.

---

## 2. Alcance funcional

| Funcionalidad | Descripción |
|---|---|
| Autenticación | Registro, inicio y cierre de sesión con contraseñas cifradas. |
| Roles | `admin`, `arbitro` y `usuario`, con permisos distintos. |
| Equipos | Alta, edición, listado y eliminación. |
| Jugadores | Alta, edición, listado, eliminación y filtro por equipo. |
| Partidos | Programación, listado, filtro por estado y eliminación. |
| Resultados | Carga de marcador y cambio de estado del partido. |
| Estadísticas | Tabla de posiciones con criterio FIBA y totales generales. |
| Vista pública | Exportación HTML de solo lectura para GitHub Pages. |

### Roles

| Rol | Dashboard / tabla | Ver partidos | Cargar resultados | CRUD equipos y jugadores | Crear / eliminar partidos |
|---|---|---|---|---|---|
| `admin` | ✅ | ✅ | ✅ | ✅ | ✅ |
| `arbitro` | ✅ | ✅ | ✅ | ❌ | ❌ |
| `usuario` | ✅ | ✅ | ❌ | ❌ | ❌ |

El registro público siempre crea usuarios con rol `usuario` (solo lectura).

---

## 3. Stack tecnológico y requisitos

| Capa | Tecnología |
|---|---|
| Framework | Laravel 12.70.2 |
| Lenguaje | PHP 8.2+; `config.platform.php` fijado a `8.2.12` |
| Base de datos | MariaDB 10.4.32 (XAMPP) · SQLite como alternativa |
| ORM | Eloquent |
| Vistas | Blade |
| Estilos | CSS propio en `public/css/style.css` (407 líneas, sin build) |
| Pruebas | PHPUnit 11.5.57 |
| Calidad de código | Laravel Pint |
| Servidor local | Apache 2.4.58 de XAMPP o `php artisan serve` |

### Requisitos

- XAMPP 8.2 o superior con Apache y MySQL/MariaDB.
- Extensiones PHP: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo` y `zip`.
- Composer.
- No se necesita Node.js ni `npm` para ejecutar la aplicación: el CSS se sirve
  directamente desde `public/css/style.css`.

Verificación rápida:

```bat
C:\xampp\php\php.exe -v
C:\xampp\php\php.exe -m
```

---

## 4. Arquitectura

### 4.1 Capas

```
Navegador
   │ HTTP
   ▼
Apache (XAMPP)  o  php artisan serve
   │
   ├── index.php de la raíz (solo despliegue en htdocs)
   │      └── requiere public/index.php
   ▼
routes/web.php
   │  middleware: web, guest, auth, can:gestionar, can:cargar-resultados
   ▼
Controlador (app/Http/Controllers)
   │      ├── FormRequest → validación y mensajes en español
   │      └── Servicio → reglas de negocio (StandingsService)
   ▼
Modelo Eloquent (app/Models)
   ▼
MySQL / MariaDB
   │
   ▼
Vista Blade (resources/views) → HTML + CSS
```

### 4.2 Responsabilidades

| Capa | Responsabilidad |
|---|---|
| `routes/web.php` | Define las 25 rutas y aplica middleware de autenticación y permisos. |
| Controladores | Orquestan la petición, consultan modelos y devuelven vistas o redirecciones. |
| Form Requests | Validan datos y centralizan los mensajes de error. |
| `StandingsService` | Calcula la tabla de posiciones y las estadísticas generales. |
| Modelos | Representan las tablas, relaciones, casts y scopes de consulta. |
| Vistas Blade | Presentan la interfaz reutilizando layouts y partials. |
| `GenerarPreview` | Exporta la vista pública estática a `docs/`. |

### 4.3 Flujo de una petición

1. El navegador solicita una URL.
2. Apache o `artisan serve` entrega la petición a `public/index.php`
   (o al `index.php` de la raíz cuando se usa `htdocs`).
3. Laravel carga el entorno, la configuración y el autoload de Composer.
4. El router busca la ruta y ejecuta los middleware (`auth`, `guest`, `can:`).
5. El controlador valida la entrada con un Form Request.
6. El modelo consulta o modifica la base de datos.
7. Se renderiza una vista Blade o se devuelve una redirección con mensaje flash.

---

## 5. Estructura del proyecto

```
LIGA BASQUET/
├── app/
│   ├── Console/Commands/GenerarPreview.php   Comando liga:preview
│   ├── Http/
│   │   ├── Controllers/                      Auth, Dashboard, Equipo, Jugador, Partido
│   │   └── Requests/                         Validación de formularios
│   ├── Models/                               User, Equipo, Jugador, Partido
│   ├── Providers/AppServiceProvider.php      Gates de autorización
│   └── Services/StandingsService.php         Tabla y estadísticas FIBA
├── bootstrap/app.php                         Configuración de Laravel 12
├── config/                                   Configuración del framework
├── database/
│   ├── migrations/                           Esquema versionado
│   ├── seeders/DatabaseSeeder.php            Usuarios y datos demo
│   └── factories/UserFactory.php
├── deploy/xampp/LEEME.md                     Guía paso a paso de XAMPP
├── doc/practica_liga.md                      Este documento
├── docs/                                     Vista previa estática (GitHub Pages)
├── lang/es/validation.php                    Mensajes de validación en español
├── public/
│   ├── css/style.css                         Estilos de la aplicación
│   ├── index.php                             Front controller de Laravel
│   └── .htaccess                             Reglas de Apache de public/
├── resources/views/                          Layouts, partials y páginas Blade
├── routes/web.php                            Rutas de la aplicación
├── storage/logs/laravel.log                  Registro de errores
├── tests/                                    Pruebas Unit y Feature
├── .env                                      Configuración local (no se sube a Git)
├── .env.example                              Plantilla de configuración
├── .htaccess                                 Entrada para htdocs
├── index.php                                 Entrada para htdocs
├── artisan                                   CLI de Laravel
├── composer.json / composer.lock             Dependencias PHP
└── README.md                                 Presentación y guía rápida
```

---

## 6. Modelo de datos

### 6.1 Tablas principales

#### `users`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | bigint unsigned | PK, autoincremental |
| `name` | varchar(100) | obligatorio |
| `email` | varchar(150) | único, obligatorio |
| `email_verified_at` | timestamp | nullable |
| `password` | varchar | hash bcrypt |
| `rol` | enum | `admin`, `arbitro`, `usuario`; por defecto `usuario` |
| `remember_token` | varchar(100) | nullable |
| `created_at`, `updated_at` | timestamp | — |

#### `equipos`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | bigint unsigned | PK |
| `nombre` | varchar(100) | único, obligatorio |
| `ciudad` | varchar(100) | obligatorio |
| `entrenador` | varchar(100) | nullable |
| `created_at`, `updated_at` | timestamp | — |

#### `jugadores`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | bigint unsigned | PK |
| `equipo_id` | bigint unsigned | FK → `equipos.id`, `cascadeOnDelete` |
| `nombre` | varchar(100) | obligatorio |
| `numero` | int unsigned | dorsal 0–99 |
| `posicion` | varchar(50) | Base, Escolta, Alero, Ala-Pívot o Pívot |
| `edad` | int unsigned | nullable, 15–60 |
| `altura` | decimal(3,2) | nullable, metros (ej. 2.08) |
| `created_at`, `updated_at` | timestamp | — |

Restricciones adicionales:

- Índice único compuesto `(equipo_id, numero)`: el dorsal es único **por equipo**.
- Índice simple sobre `equipo_id`.

#### `partidos`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | bigint unsigned | PK |
| `equipo_local_id` | bigint unsigned | FK → `equipos.id`, `cascadeOnDelete` |
| `equipo_visitante_id` | bigint unsigned | FK → `equipos.id`, `cascadeOnDelete` |
| `fecha` | date | obligatorio |
| `hora` | time | obligatorio |
| `lugar` | varchar(150) | obligatorio |
| `puntos_local` | int unsigned | por defecto 0 |
| `puntos_visitante` | int unsigned | por defecto 0 |
| `estado` | enum | `programado`, `en_juego`, `finalizado`, `cancelado`; por defecto `programado` |
| `created_at`, `updated_at` | timestamp | — |

Índices: `estado` y `fecha`.

### 6.2 Tablas internas de Laravel

| Tabla | Uso |
|---|---|
| `password_reset_tokens` | Recuperación de contraseña (no se usa actualmente). |
| `sessions` | Sesiones web (`SESSION_DRIVER=database`). |
| `cache`, `cache_locks` | Caché de Laravel (`CACHE_STORE=database`). |
| `jobs`, `job_batches`, `failed_jobs` | Cola de trabajos (`QUEUE_CONNECTION=database`). |

### 6.3 Relaciones Eloquent

```
Equipo 1 ─── N Jugador
Equipo 1 ─── N Partido (como local)
Equipo 1 ─── N Partido (como visitante)
```

- `Equipo::jugadores()`
- `Equipo::partidosLocal()`
- `Equipo::partidosVisitante()`
- `Jugador::equipo()`
- `Partido::equipoLocal()`
- `Partido::equipoVisitante()`

### 6.4 Borrado en cascada

Al eliminar un equipo se eliminan automáticamente:

- Todos sus jugadores.
- Todos los partidos en los que participa como local o visitante.

Esta regla está definida en la migración con `cascadeOnDelete()`.

---

## 7. Rutas y endpoints

Total: **25 rutas**. Todas usan el grupo de middleware `web`.

### 7.1 Acceso público e invitados

| Método | URI | Nombre | Middleware | Acción |
|---|---|---|---|---|
| GET | `/` | — | `web` | Redirige al dashboard con `redirect()->route('dashboard')`. |
| GET | `/login` | `login` | `web`, `guest` | `AuthController@showLogin` |
| POST | `/login` | `login.store` | `web`, `guest` | `AuthController@login` |
| GET | `/register` | `register` | `web`, `guest` | `AuthController@showRegister` |
| POST | `/register` | `register.store` | `web`, `guest` | `AuthController@register` |

### 7.2 Usuarios autenticados

| Método | URI | Nombre | Middleware | Acción |
|---|---|---|---|---|
| POST | `/logout` | `logout` | `web`, `auth` | `AuthController@logout` |
| GET | `/dashboard` | `dashboard` | `web`, `auth` | `DashboardController@index` |
| GET | `/partidos` | `partidos.index` | `web`, `auth` | `PartidoController@index` |

### 7.3 Carga de resultados (`admin` y `arbitro`)

| Método | URI | Nombre | Middleware | Acción |
|---|---|---|---|---|
| GET | `/partidos/{partido}/marcador` | `partidos.score` | `web`, `auth`, `can:cargar-resultados` | `PartidoController@score` |
| PATCH | `/partidos/{partido}/marcador` | `partidos.updateScore` | `web`, `auth`, `can:cargar-resultados` | `PartidoController@updateScore` |

### 7.4 Gestión de equipos (solo `admin`)

| Método | URI | Nombre | Middleware | Acción |
|---|---|---|---|---|
| GET | `/equipos` | `equipos.index` | `web`, `auth`, `can:gestionar` | `EquipoController@index` |
| GET | `/equipos/create` | `equipos.create` | `web`, `auth`, `can:gestionar` | `EquipoController@create` |
| POST | `/equipos` | `equipos.store` | `web`, `auth`, `can:gestionar` | `EquipoController@store` |
| GET | `/equipos/{equipo}/edit` | `equipos.edit` | `web`, `auth`, `can:gestionar` | `EquipoController@edit` |
| PUT/PATCH | `/equipos/{equipo}` | `equipos.update` | `web`, `auth`, `can:gestionar` | `EquipoController@update` |
| DELETE | `/equipos/{equipo}` | `equipos.destroy` | `web`, `auth`, `can:gestionar` | `EquipoController@destroy` |

### 7.5 Gestión de jugadores (solo `admin`)

| Método | URI | Nombre | Middleware | Acción |
|---|---|---|---|---|
| GET | `/jugadores` | `jugadores.index` | `web`, `auth`, `can:gestionar` | `JugadorController@index` |
| GET | `/jugadores/create` | `jugadores.create` | `web`, `auth`, `can:gestionar` | `JugadorController@create` |
| POST | `/jugadores` | `jugadores.store` | `web`, `auth`, `can:gestionar` | `JugadorController@store` |
| GET | `/jugadores/{jugador}/edit` | `jugadores.edit` | `web`, `auth`, `can:gestionar` | `JugadorController@edit` |
| PUT/PATCH | `/jugadores/{jugador}` | `jugadores.update` | `web`, `auth`, `can:gestionar` | `JugadorController@update` |
| DELETE | `/jugadores/{jugador}` | `jugadores.destroy` | `web`, `auth`, `can:gestionar` | `JugadorController@destroy` |

### 7.6 Programación y baja de partidos (solo `admin`)

| Método | URI | Nombre | Middleware | Acción |
|---|---|---|---|---|
| GET | `/partidos/create` | `partidos.create` | `web`, `auth`, `can:gestionar` | `PartidoController@create` |
| POST | `/partidos` | `partidos.store` | `web`, `auth`, `can:gestionar` | `PartidoController@store` |
| DELETE | `/partidos/{partido}` | `partidos.destroy` | `web`, `auth`, `can:gestionar` | `PartidoController@destroy` |

> Los parámetros `{equipo}`, `{jugador}` y `{partido}` se declaran explícitamente
> con `->parameters()` para que el *route model binding* use los nombres en español.

---

## 8. Roles, permisos y autorización

Los permisos se definen en `app/Providers/AppServiceProvider.php` mediante `Gate`:

```php
Gate::define('gestionar', fn (User $user) => $user->isAdmin());

Gate::define(
    'cargar-resultados',
    fn (User $user) => in_array($user->rol, ['admin', 'arbitro'], true)
);
```

Se aplican en `routes/web.php` con middleware `can:`:

```php
Route::middleware('can:cargar-resultados')->group(function () {
    Route::get('/partidos/{partido}/marcador', [PartidoController::class, 'score'])
        ->name('partidos.score');

    Route::patch('/partidos/{partido}/marcador', [PartidoController::class, 'updateScore'])
        ->name('partidos.updateScore');
});

Route::middleware('can:gestionar')->group(function () {
    Route::resource('equipos', EquipoController::class)->except('show');
    Route::resource('jugadores', JugadorController::class)->except('show');
    Route::resource('partidos', PartidoController::class)->only(['create', 'store', 'destroy']);
});
```

Comportamiento:

- La interfaz oculta botones con `@can` / `@cannot`.
- Si un usuario intenta entrar por URL directa a una ruta no permitida, Laravel
  responde **403 Forbidden**.
- El registro público asigna siempre `rol = usuario`.

---

## 9. Capa HTTP

### 9.1 Controladores

| Controlador | Responsabilidad |
|---|---|
| `AuthController` | Login, registro y logout. |
| `DashboardController` | Tabla de posiciones, estadísticas y últimos partidos. |
| `EquipoController` | CRUD de equipos. |
| `JugadorController` | CRUD de jugadores y filtro por equipo. |
| `PartidoController` | Listado, programación, marcador y eliminación. |

### 9.2 Form Requests y validaciones

| Form Request | Reglas principales |
|---|---|
| `LoginRequest` | `email` obligatorio y válido; `password` obligatorio; límite de 5 intentos por email + IP. |
| `RegisterRequest` | `name` máx. 100; `email` único, máx. 150; `password` confirmado y mínimo 8 caracteres. |
| `StoreEquipoRequest` | `nombre` único y máx. 100; `ciudad` obligatoria; `entrenador` opcional. |
| `UpdateEquipoRequest` | Igual que el anterior, ignorando el equipo actual en la regla `unique`. |
| `StoreJugadorRequest` | `equipo_id` existente; `nombre` máx. 100; `numero` 0–99 y único por equipo; `posicion` dentro de `Jugador::POSICIONES`; `edad` 15–60; `altura` 1–2.5. |
| `UpdateJugadorRequest` | Igual que el anterior, ignorando el jugador actual. |
| `StorePartidoRequest` | Equipos existentes y distintos; `fecha` válida; `hora` en formato `H:i`; `lugar` máx. 150. |
| `UpdateMarcadorRequest` | Puntos 0–999; estado dentro de `Partido::ESTADOS`; un partido `finalizado` no puede quedar empatado. |

### 9.3 Reglas de negocio destacadas

- **Dorsal único por equipo**, no global. El mismo número puede existir en dos
  equipos distintos.
- **Equipos distintos** en un partido: `equipo_local_id` debe ser diferente de
  `equipo_visitante_id`.
- **Sin empates al finalizar**: si `estado = finalizado`, los puntos deben ser
  diferentes.
- **Estados válidos**: `programado`, `en_juego`, `finalizado`, `cancelado`.
- **Borrado en cascada**: al eliminar un equipo se eliminan sus jugadores y
  partidos asociados.

---

## 10. Lógica de negocio: tabla de posiciones

`StandingsService` calcula la clasificación con criterio FIBA:

- **2 puntos por victoria**.
- **1 punto por derrota**.
- Solo se consideran partidos con estado `finalizado`.
- Columnas calculadas: PJ (partidos jugados), PG, PP, PF (puntos a favor),
  PC (puntos en contra), DIF (PF − PC) y Pts.
- Desempate: puntos, diferencia, puntos a favor y, por último, nombre del equipo.

```php
$partidos = Partido::query()
    ->finalizados()
    ->get(['equipo_local_id', 'equipo_visitante_id', 'puntos_local', 'puntos_visitante']);

foreach ($partidos as $partido) {
    $local = $partido->equipo_local_id;
    $visitante = $partido->equipo_visitante_id;

    $filas[$local]['pj']++;
    $filas[$visitante]['pj']++;

    if ($partido->puntos_local > $partido->puntos_visitante) {
        $filas[$local]['puntos'] += 2;
        $filas[$visitante]['puntos'] += 1;
    } else {
        $filas[$visitante]['puntos'] += 2;
        $filas[$local]['puntos'] += 1;
    }
}
```

Orden final:

```php
->sortBy([
    ['puntos', 'desc'],
    ['dif', 'desc'],
    ['pf', 'desc'],
    ['nombre', 'asc'],
])
```

### Estadísticas generales

`StandingsService::estadisticas()` devuelve:

- Total de partidos.
- Partidos finalizados.
- Partidos programados.
- Total de equipos.
- Total de jugadores.
- Promedio de puntos por partido finalizado (suma de ambos equipos / finalizados,
  redondeado a un decimal).

---

## 11. Vistas y interfaz

- **Layout principal**: `resources/views/layouts/app.blade.php`.
- **Alertas flash**: `resources/views/partials/alerts.blade.php`.
- **Tabla de posiciones**: `partials/tabla-posiciones.blade.php`.
- **Tabla de partidos**: `partials/tabla-partidos.blade.php`.
- **Últimos partidos**: `partials/tabla-ultimos-partidos.blade.php`.
- **Filtros de partidos**: `partials/filtros-partidos.blade.php`.
- **Vistas de la preview**: `resources/views/preview/*`.

La vista pública reutiliza los mismos partials de las tablas, pero no incluye
formularios, botones de acción ni enlaces a rutas internas. El único indicador es
la etiqueta **“Solo lectura”**.

### Estilos

- Archivo único: `public/css/style.css`.
- Sin Tailwind, sin Vite y sin `npm run build` para la interfaz.
- `package.json` conserva el esqueleto de Laravel, pero no es necesario para
  ejecutar la aplicación.

---

## 12. Autenticación y seguridad

| Medida | Implementación |
|---|---|
| Hash de contraseñas | Cast `hashed` en `User` + `BCRYPT_ROUNDS=12`. |
| Sesiones | Driver `database`; tabla `sessions`. |
| CSRF | Directiva `@csrf` en todos los formularios. |
| Rate limiting | Máximo 5 intentos fallidos por email + IP. |
| Regeneración de sesión | `session()->regenerate()` al iniciar sesión. |
| Logout seguro | `Auth::logout()`, invalidación de sesión y regeneración del token. |
| Autorización | Gates `gestionar` y `cargar-resultados` + middleware `can:`. |
| Datos ocultos | `password` y `remember_token` en `$hidden`. |
| Archivos sensibles | `.env`, `.git`, `vendor/`, `storage/`, `composer.*` y `artisan` bloqueados por `.htaccess`. |

### Protecciones del `.htaccess` raíz

El `.htaccess` de la raíz, usado solo en el despliegue con Apache/`htdocs`:

1. Bloquea archivos y carpetas ocultas (`.env`, `.git`, `.htaccess`, etc.).
2. Bloquea carpetas internas: `app`, `bootstrap`, `config`, `database`, `lang`,
   `resources`, `routes`, `storage`, `tests`, `vendor`, `deploy` y `doc`.
3. Bloquea `composer.json`, `composer.lock`, `composer.phar`, `artisan` y
   `phpunit.xml`.
4. Sirve `css/`, `js/`, `build/`, `favicon.ico` y `robots.txt` desde `public/`.
5. Envía el resto de peticiones al `index.php` de la raíz.

Por eso, al abrir `http://localhost/liga/.env` o `http://localhost/liga/vendor/...`
la respuesta correcta es **403 Forbidden**.

> En producción real se recomienda `APP_DEBUG=false` y servir solo la carpeta
> `public/` como *DocumentRoot*. En este proyecto se usa `APP_DEBUG=true` porque
> el entorno es local.

---

## 13. Configuración y variables de entorno

El archivo `.env` **no se sube a Git**. Se crea a partir de `.env.example`.

| Variable | Valor local | Uso |
|---|---|---|
| `APP_NAME` | `Liga de Basquetbol` | Nombre visible en la interfaz. |
| `APP_ENV` | `local` | Entorno de desarrollo. |
| `APP_KEY` | generada con `key:generate` | Cifrado de sesiones y cookies. |
| `APP_DEBUG` | `true` | Muestra detalles de errores en local. |
| `APP_URL` | `http://localhost:8000` | URL base para `artisan serve`. |
| `APP_LOCALE` | `es` | Idioma de la aplicación. |
| `APP_FALLBACK_LOCALE` | `en` | Idioma de respaldo. |
| `APP_FAKER_LOCALE` | `es_ES` | Datos falsos en español. |
| `DB_CONNECTION` | `mysql` | Driver de base de datos. |
| `DB_HOST` | `127.0.0.1` | Host de MySQL. |
| `DB_PORT` | `3307` | Puerto de MySQL de XAMPP. |
| `DB_DATABASE` | `liga_basquet` | Nombre de la base. |
| `DB_USERNAME` | `root` | Usuario de MySQL. |
| `DB_PASSWORD` | *(vacío)* | Contraseña de MySQL de XAMPP. |
| `SESSION_DRIVER` | `database` | Sesiones en la tabla `sessions`. |
| `SESSION_LIFETIME` | `120` | Minutos de vida de la sesión. |
| `CACHE_STORE` | `database` | Caché en base de datos. |
| `QUEUE_CONNECTION` | `database` | Cola en base de datos. |
| `BCRYPT_ROUNDS` | `12` | Coste del hash bcrypt. |

### Puerto de MySQL

En este equipo el puerto **3306 está ocupado por otro MySQL** y XAMPP usa el
**3307**. Se puede comprobar en:

```
Panel de XAMPP → MySQL → Config → my.ini
```

Buscar `port=` y ajustar `DB_PORT` si fuera necesario.

### Plataforma PHP

`composer.json` incluye:

```json
"config": {
    "platform": {
        "php": "8.2.12"
    }
}
```

Esto obliga a Composer a instalar dependencias compatibles con PHP 8.2, que es la
versión que trae XAMPP. Sin esta configuración, Composer puede instalar Symfony 8.1
o superior, que exige PHP ≥ 8.4.1 y provoca un error 500 en XAMPP.

---

## 14. Instalación local

### Opción A — `php artisan serve` (recomendada para desarrollo)

1. Abrir el panel de XAMPP y arrancar **Apache** y **MySQL**.
2. Crear la base de datos en <http://localhost/phpmyadmin> o por consola:

   ```bat
   C:\xampp\mysql\bin\mysql.exe -h 127.0.0.1 -P 3307 -u root -e "CREATE DATABASE IF NOT EXISTS liga_basquet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

3. Instalar dependencias:

   ```bat
   composer install
   ```

   Si se usa el PHP de XAMPP y falta la extensión `zip`, ejecutar Composer con
   otro PHP 8.2+ que sí la tenga, o habilitar `extension=zip` en
   `C:\xampp\php\php.ini`.

4. Crear el archivo de entorno:

   ```bat
   copy .env.example .env
   ```

5. Generar la clave y crear las tablas con datos demo:

   ```bat
   php artisan key:generate
   php artisan migrate:fresh --seed
   ```

6. Arrancar el servidor:

   ```bat
   php artisan serve
   ```

7. Abrir <http://127.0.0.1:8000>.

### Opción B — Copiar la carpeta a `htdocs`

1. Copiar **toda** la carpeta del proyecto dentro de `C:\xampp\htdocs`
   (por ejemplo `C:\xampp\htdocs\liga`). Incluir `.env` y `vendor/`.
2. Arrancar **Apache** y **MySQL** en XAMPP.
3. Abrir `http://localhost/liga/`.

No hay que editar `httpd.conf`, `hosts` ni crear un host virtual.

> Si el proyecto se descarga desde GitHub, `vendor/` no viene incluido porque está
> en `.gitignore`. En ese caso ejecutar `composer install` dentro de la carpeta.

---

## 15. Despliegue en `htdocs` (Apache)

### Archivos de entrada

| Archivo | Función |
|---|---|
| `index.php` (raíz) | Requiere `public/index.php` para ejecutar Laravel desde la carpeta del proyecto. |
| `.htaccess` (raíz) | Sirve estáticos, protege archivos internos y envía las rutas al front controller. |
| `public/index.php` | Front controller estándar de Laravel. |
| `public/.htaccess` | Reglas de Apache para la carpeta `public/`. |

### Reglas del `.htaccess` raíz

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Bloquear archivos y carpetas ocultas
    RewriteRule (^|/)\. - [F,L]

    # Bloquear carpetas internas
    RewriteRule ^(app|bootstrap|config|database|lang|resources|routes|storage|tests|vendor|deploy|doc)(/|$) - [F,L]

    # Bloquear archivos de configuración
    RewriteRule ^(composer\.(json|lock|phar)|artisan|phpunit\.xml(\.dist)?)$ - [F,L]

    # Servir estáticos desde public/
    RewriteRule ^(css|js|build)/(.*)$ public/$1/$2 [END]
    RewriteRule ^(favicon\.ico|robots\.txt)$ public/$1 [END]

    # Front controller
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [END]
</IfModule>
```

### Requisitos de Apache

- `mod_rewrite` activo.
- `AllowOverride All` en el directorio `htdocs` (XAMPP lo trae por defecto).
- `DirectoryIndex` debe incluir `index.php` (XAMPP lo trae por defecto).

### URL según el nombre de la carpeta

| Carpeta en `htdocs` | URL |
|---|---|
| `C:\xampp\htdocs\liga` | <http://localhost/liga/> |
| `C:\xampp\htdocs\liga_basquet` | <http://localhost/liga_basquet/> |
| `C:\xampp\htdocs\mi-liga` | <http://localhost/mi-liga/> |

No hay que cambiar `.env` ni `APP_URL`: Laravel detecta el host y la subcarpeta
desde la petición.

### Redirección de la raíz

La ruta raíz debe usar el nombre de la ruta, no una URL absoluta:

```php
Route::get('/', fn () => redirect()->route('dashboard'));
```

Si se usa `Route::redirect('/', '/dashboard')`, Laravel genera `/dashboard` sin
la subcarpeta y el navegador termina en la página de bienvenida de XAMPP. Ese
fallo ya está corregido en `routes/web.php`.

---

## 16. Vista previa estática y GitHub Pages

GitHub Pages no ejecuta PHP. Por eso el sitio público se genera como HTML:

```bat
php artisan liga:preview
```

El comando:

1. Consulta la base de datos con `StandingsService` y Eloquent.
2. Renderiza las vistas `resources/views/preview/*` reutilizando los partials.
3. Escribe en `docs/`:

   ```
   docs/
   ├── index.html                  Estadísticas + tabla de posiciones
   ├── partidos.html               Todos los partidos
   ├── partidos-programado.html    Filtro por estado
   ├── partidos-en_juego.html
   ├── partidos-finalizado.html
   ├── partidos-cancelado.html
   ├── css/style.css
   └── .nojekyll
   ```

4. No incluye formularios, botones de acción ni enlaces a `/login` o `/dashboard`.

Se puede cambiar la carpeta de salida:

```bat
php artisan liga:preview --output=docs
```

### Publicación en GitHub Pages

1. Subir los cambios a `main`.
2. En GitHub: **Settings → Pages → Source: Deploy from a branch**.
3. Seleccionar rama `main` y carpeta `/docs`.
4. Guardar y esperar a que GitHub publique el sitio.

Cada vez que cambien los datos en local:

```bat
php artisan liga:preview
git add docs
git commit -m "actualizar vista previa"
git push
```

---

## 17. Pruebas y calidad

### Ejecución

```bat
php artisan test
```

También se puede usar el PHP de XAMPP:

```bat
C:\xampp\php\php.exe artisan test
```

Resultado actual:

```
Tests:    29 passed (104 assertions)
```

Las pruebas usan **SQLite en memoria** (`phpunit.xml`), por lo que no necesitan
MySQL ni la base `liga_basquet`.

### Cobertura funcional

| Archivo | Pruebas | Qué verifica |
|---|---|---|
| `AuthTest` | 5 | Invitados, login válido/inválido, registro y logout. |
| `EquipoTest` | 5 | Alta, nombre único, edición, eliminación y acceso de invitados. |
| `JugadorTest` | 4 | Alta, dorsal único por equipo, mismo dorsal en otro equipo y filtro. |
| `PartidoTest` | 4 | Programación, equipos distintos, marcador y empate inválido. |
| `StandingsTest` | 2 | Cálculo FIBA y estadísticas generales. |
| `AuthorizationTest` | 5 | Permisos de `usuario`, `arbitro` y `admin`. |
| `PreviewCommandTest` | 2 | Generación de `docs/` sin formularios ni rutas internas. |
| `ExampleTest` | 2 | Redirección de la raíz y prueba unitaria básica. |

### Estilo de código

```bat
vendor\bin\pint
```

Pint debe terminar con `passed`.

---

## 18. Operación y mantenimiento

### Comandos habituales

| Objetivo | Comando |
|---|---|
| Limpiar configuración cacheada | `php artisan config:clear` |
| Limpiar rutas cacheadas | `php artisan route:clear` |
| Limpiar vistas compiladas | `php artisan view:clear` |
| Recrear la base con datos demo | `php artisan migrate:fresh --seed` |
| Regenerar la vista pública | `php artisan liga:preview` |
| Ejecutar pruebas | `php artisan test` |
| Revisar estilo | `vendor\bin\pint` |

> `migrate:fresh --seed` **borra todos los datos** y vuelve a cargar los datos de
> demostración. Usarlo solo en desarrollo.

### Copia de seguridad de la base

```bat
C:\xampp\mysql\bin\mysqldump.exe -h 127.0.0.1 -P 3307 -u root liga_basquet > liga_basquet.sql
```

Restaurar:

```bat
C:\xampp\mysql\bin\mysql.exe -h 127.0.0.1 -P 3307 -u root liga_basquet < liga_basquet.sql
```

También se puede importar el `.sql` desde phpMyAdmin.

### Actualizar dependencias

```bat
composer install
```

Si se cambia `composer.json` o `composer.lock`, ejecutar Composer con el PHP de
XAMPP o con cualquier PHP 8.2+ respetando `config.platform.php`.

### Registros

| Archivo | Contenido |
|---|---|
| `storage/logs/laravel.log` | Errores y excepciones de Laravel. |
| `C:\xampp\apache\logs\error.log` | Errores de Apache. |
| `C:\xampp\apache\logs\access.log` | Peticiones HTTP y códigos de respuesta. |

---

## 19. Solución de problemas

| Síntoma | Causa probable | Solución |
|---|---|---|
| `Composer detected issues... require PHP >= 8.4.1` | `vendor/` instalado con PHP 8.4 y Symfony 8.1. | Mantener `config.platform.php = 8.2.12` y ejecutar `composer install`/`composer update` con PHP 8.2. |
| `SQLSTATE[HY000] [1049] Unknown database 'liga_basquet'` | La base no existe. | Crearla en phpMyAdmin o con el comando `CREATE DATABASE`. |
| `Connection refused` en el puerto 3307 | MySQL no está arrancado o el puerto es otro. | Arrancar MySQL en XAMPP y revisar `port=` en `my.ini`. |
| `http://localhost/liga/` muestra “Welcome to XAMPP” | La ruta raíz redirigía a `/dashboard` sin la subcarpeta. | Usar `redirect()->route('dashboard')` y ejecutar `php artisan route:clear`. |
| `Index of /liga` (listado de archivos) | Faltan `index.php` y `.htaccess` en la raíz de la copia. | Copiar ambos archivos o volver a copiar el proyecto completo. |
| Error 500 al abrir la app | `APP_KEY` vacía, `vendor/` incompatible o error de configuración. | Revisar `storage/logs/laravel.log`, ejecutar `php artisan key:generate` y `composer install`. |
| 403 al abrir `.env` o `vendor/` | Es el comportamiento esperado del `.htaccess`. | No es un error; esos archivos deben quedar bloqueados. |
| El CSS no carga | La regla de estáticos no se aplica o falta `mod_rewrite`. | Verificar `http://localhost/liga/css/style.css` y activar `mod_rewrite` + `AllowOverride All`. |
| 419 Page Expired | Token CSRF o sesión caducada. | Recargar el formulario e iniciar sesión de nuevo. |
| La página se ve vieja después de un cambio | Caché del navegador. | Pulsar `Ctrl + F5` o probar en una ventana privada. |
| `php artisan` usa otra versión de PHP | La terminal apunta al PHP de WinGet, no al de XAMPP. | Usar `C:\xampp\php\php.exe artisan ...` o ajustar el `PATH`. |
| El puerto 3306 está ocupado | Otro MySQL/servicio está usando ese puerto. | Usar XAMPP en 3307 y mantener `DB_PORT=3307`. |

---

## 20. Anexos

### 20.1 Credenciales de demostración

| Correo | Contraseña | Rol |
|---|---|---|
| `admin@liga.com` | `admin123` | admin |
| `arbitro@liga.com` | `arbitro123` | arbitro |
| `espectador@liga.com` | `usuario123` | usuario |

### 20.2 Datos de ejemplo del seeder

- 3 usuarios.
- 4 equipos: Halcones Dorados, Toros Salvajes, Celtas del Sur y Tiburones Azules.
- 8 jugadores.
- 4 partidos: 3 finalizados y 1 programado.

### 20.3 Checklist de despliegue en `htdocs`

- [ ] Apache y MySQL arrancados en XAMPP.
- [ ] Base `liga_basquet` creada.
- [ ] Carpeta completa copiada a `C:\xampp\htdocs\liga`.
- [ ] `.env` presente y con `DB_PORT=3307`.
- [ ] `vendor/` presente y compatible con PHP 8.2.
- [ ] `index.php` y `.htaccess` presentes en la raíz de la copia.
- [ ] `http://localhost/liga/` abre el login o el dashboard.
- [ ] El CSS carga correctamente.
- [ ] `.env` y `vendor/` devuelven 403.
- [ ] Login con `admin@liga.com` / `admin123` funciona.

### 20.4 Comandos útiles

```bat
:: Crear la base
C:\xampp\mysql\bin\mysql.exe -h 127.0.0.1 -P 3307 -u root -e "CREATE DATABASE IF NOT EXISTS liga_basquet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

:: Instalar dependencias
composer install

:: Configurar el entorno
copy .env.example .env
php artisan key:generate

:: Crear tablas y datos demo
php artisan migrate:fresh --seed

:: Arrancar el servidor
php artisan serve

:: Generar la vista pública
php artisan liga:preview

:: Ejecutar pruebas
php artisan test

:: Revisar estilo
vendor\bin\pint
```

### 20.5 Glosario

| Término | Significado |
|---|---|
| **Blade** | Motor de plantillas de Laravel. |
| **Eloquent** | ORM de Laravel para trabajar con la base de datos mediante modelos. |
| **Form Request** | Clase que encapsula la validación de un formulario. |
| **Gate** | Regla de autorización basada en el usuario autenticado. |
| **Middleware** | Capa que intercepta la petición antes del controlador. |
| **Route model binding** | Conversión automática de un parámetro de ruta en un modelo Eloquent. |
| **Seeder** | Clase que carga datos iniciales o de demostración. |
| **Migración** | Archivo versionado que define cambios en el esquema de la base. |
| **Vista previa estática** | HTML generado desde la base y publicado en GitHub Pages. |

---

## 21. Historial del documento

| Fecha | Cambio |
|---|---|
| 30/10/2026 | Actualización completa: MySQL 3307, PHP 8.2, despliegue en `htdocs`, 25 rutas, 29 pruebas / 104 aserciones y solución de problemas. |
