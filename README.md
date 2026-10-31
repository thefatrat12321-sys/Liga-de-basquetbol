# 🏀 Liga de Básquetbol — Laravel 12

Sistema de gestión de una liga de básquetbol: autenticación, equipos, jugadores, partidos, carga de resultados y tabla de posiciones con criterio FIBA.

Este proyecto fue migrado desde una implementación PHP nativa (MVC + PDO) a **Laravel 12**, conservando el diseño visual y todas las funcionalidades originales.

**Vista previa pública (solo lectura):** <https://thefatrat12321-sys.github.io/Liga-de-basquetbol/>

> La vista previa es una copia estática generada desde la base de datos. Para iniciar sesión y modificar datos hay que ejecutar la aplicación en local con **XAMPP + MySQL** (ver [`deploy/xampp/LEEME.md`](deploy/xampp/LEEME.md)).

---

## Stack

| Capa | Tecnología |
|---|---|
| Framework | Laravel 12 |
| Lenguaje | PHP 8.2+ (probado en 8.2 y 8.4) |
| Base de datos | MySQL 8 / MariaDB (XAMPP en local) · SQLite como alternativa |
| ORM | Eloquent |
| Vistas | Blade |
| Estilos | CSS propio en `public/css/style.css` (sin build) |
| Pruebas | PHPUnit 11 |

---

## Requisitos

- **XAMPP 8.2+** con Apache y MySQL (o PHP 8.2+ y MySQL instalados por separado).
- Extensiones PHP: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `zip`.
- Composer.

Verifica las extensiones con:

```bash
php -m
```

---

## Inicio rápido con XAMPP

1. Abre el panel de XAMPP y arranca **Apache** y **MySQL**.
2. Crea la base de datos en <http://localhost/phpmyadmin>:

   ```sql
   CREATE DATABASE liga_basquet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Copia el proyecto y configura el entorno:

   ```bash
   composer install
   copy .env.example .env        # Windows
   # cp .env.example .env        # Linux / macOS

   php artisan key:generate
   php artisan migrate:fresh --seed
   php artisan serve
   ```

4. Abre <http://127.0.0.1:8000>.

> **Puerto de MySQL:** `.env.example` usa `DB_PORT=3307` porque en este equipo el
> 3306 está ocupado por otro MySQL. Comprueba el tuyo en **XAMPP → MySQL → Config →
> my.ini** (`port=`) y ajusta `DB_PORT` si es diferente.

La guía completa (extensiones, host virtual `liga.local`, solución de errores) está en
[`deploy/xampp/LEEME.md`](deploy/xampp/LEEME.md).

### Copiar la carpeta a `htdocs` (sin host virtual)

También puedes ejecutar la app con el Apache de XAMPP sin tocar `httpd.conf` ni `hosts`:

1. Copia **toda** la carpeta del proyecto dentro de `C:\xampp\htdocs` (por ejemplo
   `C:\xampp\htdocs\liga`). Incluye `.env` y `vendor/` para que funcione directamente.
2. Arranca **Apache** y **MySQL** en el panel de XAMPP.
3. Abre `http://localhost/liga/` (cambia `liga` por el nombre real de la carpeta).

El `index.php` y el `.htaccess` de la raíz se encargan de todo: sirven los archivos
estáticos de `public/`, envían las rutas a Laravel y bloquean `.env`, `vendor/` y las
carpetas internas. No hace falta configurar ningún host virtual.

> El `vendor/` incluido está generado para **PHP 8.2** (`config.platform.php` en
> `composer.json`), que es la versión que trae XAMPP. Si actualizas dependencias con otra
> versión de PHP, ejecuta `composer install` con el PHP de XAMPP.

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

## Documentación técnica

El documento completo con arquitectura, modelo de datos, rutas, permisos, seguridad,
instalación y despliegue está en
[`doc/practica_liga.md`](doc/practica_liga.md).

Incluye, entre otros:

- Diagrama de capas y flujo de una petición.
- Esquema de las tablas `users`, `equipos`, `jugadores` y `partidos`.
- Las 25 rutas con su middleware y controlador.
- Roles, Gates y reglas de validación.
- Despliegue en `htdocs`, vista previa en Pages y solución de problemas.

---

## Vista previa estática (GitHub Pages)

GitHub Pages solo sirve archivos estáticos: **no ejecuta PHP ni Laravel**. Por eso el
sitio público es una copia HTML generada desde la base de datos local:

```bash
php artisan liga:preview
```

El comando escribe en `docs/`:

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

Para publicarla:

1. En GitHub: **Settings → Pages → Source: Deploy from a branch → `main` / `/docs`**.
2. Cada vez que cambien los datos en local:

   ```bash
   php artisan liga:preview
   git add docs
   git commit -m "actualizar vista previa"
   git push
   ```

La vista previa **no tiene login, formularios ni botones de edición**: solo muestra la
tabla y los partidos. Los datos solo se modifican en la instalación local con XAMPP.

---

## Usar SQLite en lugar de MySQL

`.env.example` usa MySQL de XAMPP por defecto. Si no quieres arrancar MySQL:

```dotenv
DB_CONNECTION=sqlite
```

Y ejecuta de nuevo:

```bash
php artisan config:clear
php artisan migrate:fresh --seed
```

SQLite no requiere instalar nada más; el archivo se crea en `database/database.sqlite`.

---

## Funcionalidades

- **Autenticación**: login, registro y logout con sesiones de Laravel, contraseñas hasheadas (bcrypt) y límite de intentos.
- **Equipos**: CRUD completo con nombre único, ciudad y entrenador.
- **Jugadores**: CRUD por equipo, dorsal único dentro del equipo, posición, edad y altura; filtro por equipo.
- **Partidos**: programación, filtros por estado y carga de marcador/estado (`programado`, `en_juego`, `finalizado`, `cancelado`).
- **Estadísticas**: tabla de posiciones (PJ, PG, PP, PF, PC, DIF, Pts) con 2 puntos por victoria y 1 por derrota; promedio de puntos y totales.
- **Vista previa pública**: exportación estática de solo lectura para GitHub Pages.

---

## Estructura

```
app/
├── Console/Commands/
│   └── GenerarPreview.php   Exporta la vista previa estática a docs/
├── Http/
│   ├── Controllers/        Auth, Dashboard, Equipo, Jugador, Partido
│   └── Requests/           Validación (login, registro, CRUD, marcador)
├── Models/                 User, Equipo, Jugador, Partido
└── Services/
    └── StandingsService.php  Tabla de posiciones y estadísticas

database/
├── migrations/             Esquema (users, equipos, jugadores, partidos)
├── seeders/                Datos demo + usuarios por rol
└── factories/

deploy/
└── xampp/LEEME.md          Guía de instalación con XAMPP + MySQL

docs/                       Vista previa estática (GitHub Pages)

lang/es/validation.php      Mensajes de validación en español
resources/views/
├── layouts/app.blade.php   Layout de la aplicación
├── preview/                Layout y vistas de la versión estática
├── partials/               Tablas y filtros compartidos
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

Resultado actual: **29 pruebas, 104 aserciones**. Cubren autenticación, autorización por
roles, CRUD de equipos y jugadores, reglas del marcador, cálculo de posiciones y la
generación de la vista previa estática.

---

## Comandos útiles

```bash
php artisan migrate:fresh --seed   # Reinicia la BD y carga datos demo
php artisan liga:preview           # Regenera docs/ para GitHub Pages
php artisan route:list             # Lista las rutas
php artisan config:clear           # Limpia la caché de configuración
```

---

## Notas de mantenimiento

- El archivo `.env` **no se sube** al repositorio (está en `.gitignore`): cada equipo configura sus credenciales.
- `config/database.php` debe permanecer en minúsculas (importante al desplegar en Linux).
- El CSS se sirve directamente desde `public/css/style.css`; no requiere `npm install` ni `npm run build`.
- La lógica de posiciones vive en `app/Services/StandingsService.php`, separada de los controladores.
- La vista previa de `docs/` se genera con `php artisan liga:preview`; no editar esos HTML a mano.
