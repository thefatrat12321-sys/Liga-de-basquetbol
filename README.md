# 🏀 Liga de Básquetbol — Laravel 12

Sistema de gestión de una liga de básquetbol: autenticación, equipos, jugadores, partidos, carga de resultados y tabla de posiciones con criterio FIBA.

Este proyecto fue migrado desde una implementación PHP nativa (MVC + PDO) a **Laravel 12**, conservando el diseño visual y todas las funcionalidades originales.

---

## Stack

| Capa | Tecnología |
|---|---|
| Framework | Laravel 12 |
| Lenguaje | PHP 8.2+ (probado en 8.4) |
| Base de datos | SQLite (local) / MySQL (producción) |
| ORM | Eloquent |
| Vistas | Blade |
| Estilos | CSS propio en `public/css/style.css` (sin build) |
| Pruebas | PHPUnit 11 |

---

## Requisitos

- PHP 8.2 o superior con las extensiones: `mbstring`, `openssl`, `pdo_sqlite`, `sqlite3` (y `pdo_mysql` si usas MySQL).
- Composer.

Verifica las extensiones con:

```bash
php -m
```

---

## Instalación

```bash
composer install

copy .env.example .env        # Windows
# cp .env.example .env        # Linux / macOS

php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

La aplicación queda disponible en <http://127.0.0.1:8000>.

### Credenciales de demostración

| Correo | Contraseña | Rol | Permisos |
|---|---|---|---|
| `admin@liga.com` | `admin123` | admin | Todo: equipos, jugadores y partidos |
| `arbitro@liga.com` | `arbitro123` | arbitro | Ver todo y cargar resultados |
| `espectador@liga.com` | `usuario123` | usuario | Solo lectura (dashboard y partidos) |

El seeder también carga 4 equipos, 8 jugadores y 4 partidos de ejemplo.

### Roles y permisos

- **admin**: CRUD completo de equipos, jugadores y partidos; carga de resultados.
- **arbitro**: consulta el dashboard y los partidos, y puede cargar marcadores/estados.
- **usuario**: solo lectura. Ve la tabla de posiciones y el listado de partidos, sin botones de acción.
- El registro público crea usuarios con rol `usuario` (solo lectura).
- Los permisos se definen con `Gate` en `app/Providers/AppServiceProvider.php` y se aplican con middleware `can:` en `routes/web.php`.

---

## Usar MySQL en lugar de SQLite

Por defecto `.env` usa SQLite (`DB_CONNECTION=sqlite`), que funciona sin instalar nada. Para usar MySQL:

1. Crea la base de datos:

   ```sql
   CREATE DATABASE liga_basquet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. En `.env`, comenta `DB_CONNECTION=sqlite` y descomenta el bloque de MySQL con tus credenciales:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=liga_basquet
   DB_USERNAME=root
   DB_PASSWORD=tu_password
   ```

3. Ejecuta:

   ```bash
   php artisan config:clear
   php artisan migrate:fresh --seed
   ```

---

## Funcionalidades

- **Autenticación**: login, registro y logout con sesiones de Laravel, contraseñas hasheadas (bcrypt) y límite de intentos.
- **Equipos**: CRUD completo con nombre único, ciudad y entrenador.
- **Jugadores**: CRUD por equipo, dorsal único dentro del equipo, posición, edad y altura; filtro por equipo.
- **Partidos**: programación, filtros por estado y carga de marcador/estado (`programado`, `en_juego`, `finalizado`, `cancelado`).
- **Estadísticas**: tabla de posiciones (PJ, PG, PP, PF, PC, DIF, Pts) con 2 puntos por victoria y 1 por derrota; promedio de puntos y totales.

---

## Estructura

```
app/
├── Http/
│   ├── Controllers/        Auth, Dashboard, Equipo, Jugador, Partido
│   └── Requests/           Validación (login, registro, CRUD, marcador)
├── Models/                 User, Equipo, Jugador, Partido
└── Services/
    └── StandingsService.php  Tabla de posiciones y estadísticas

database/
├── migrations/             Esquema (users, equipos, jugadores, partidos)
├── seeders/                Datos demo + administrador
└── factories/

lang/es/validation.php      Mensajes de validación en español
resources/views/
├── layouts/app.blade.php   Layout principal
├── partials/alerts.blade.php
├── auth/                   login, register
├── equipos/                index, create, edit, _form
├── jugadores/              index, create, edit, _form
├── partidos/               index, create, score
└── dashboard.blade.php

routes/web.php              Rutas de la aplicación
public/css/style.css        Diseño (naranja / oscuro)
```

---

## Rutas principales

| Método | URI | Acción |
|---|---|---|
| GET | `/` | Redirige a `/dashboard` |
| GET/POST | `/login` | Iniciar sesión |
| GET/POST | `/register` | Registro |
| POST | `/logout` | Cerrar sesión |
| GET | `/dashboard` | Estadísticas y tabla |
| resource | `/equipos` | CRUD de equipos |
| resource | `/jugadores` | CRUD de jugadores (filtro `?equipo_id=`) |
| resource | `/partidos` | Listado, alta y baja (filtro `?estado=`) |
| GET/PATCH | `/partidos/{partido}/marcador` | Ver y cargar resultado |

---

## Pruebas

```bash
php artisan test
```

Cobertura actual: autenticación, CRUD de equipos, jugadores (incluido dorsal único por equipo), partidos (incluido empate inválido al finalizar) y cálculo de la tabla de posiciones.

---

## Comandos útiles

```bash
php artisan migrate:fresh --seed   # Reinicia la BD y carga datos demo
php artisan route:list             # Lista las rutas
php artisan config:clear           # Limpia la caché de configuración
composer dev                       # Servidor + queue + logs + vite
```

---

## Notas de mantenimiento

- `config/database.php` debe permanecer en minúsculas (importante al desplegar en Linux).
- El CSS se sirve directamente desde `public/css/style.css`; no requiere `npm install` ni `npm run build`.
- La lógica de posiciones vive en `app/Services/StandingsService.php`, separada de los controladores.
