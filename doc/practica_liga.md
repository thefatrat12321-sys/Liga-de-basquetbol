# Documentación Técnica: Sistema de Gestión de Liga de Básquetbol

## 1. Descripción General de la Arquitectura del Sistema

El sistema está implementado sobre **Laravel 12** siguiendo el patrón **MVC** que provee el framework, con una capa adicional de **servicios** para la lógica de negocio compleja. La persistencia se realiza con **Eloquent ORM** sobre **SQLite** (desarrollo) o **MySQL** (producción).

```
Petición HTTP
     │
     ▼
routes/web.php  ──►  Middleware (auth / guest)
     │
     ▼
Controlador (app/Http/Controllers)
     │            ├── FormRequest (validación)
     │            └── Service (reglas de negocio)
     ▼
Modelo Eloquent (app/Models)  ──►  Base de datos
     │
     ▼
Vista Blade (resources/views)
```

Diferencias clave frente a la versión anterior en PHP nativo:

| Aspecto | Versión nativa (legacy) | Versión Laravel |
|---|---|---|
| Enrutamiento | `index.php` + `switch ($_GET['route'])` | `routes/web.php` con nombres de ruta |
| Controladores | Clases sueltas en `controllers/` | `app/Http/Controllers` con inyección de dependencias |
| Acceso a datos | PDO y SQL manual | Eloquent + Query Builder |
| Validación | `if` manuales y `$_SESSION['error']` | Form Requests + mensajes en español |
| Autenticación | `$_SESSION['user']` | Guard de Laravel, hash bcrypt y rate limiting |
| Vistas | `require` de PHP con HTML | Blade con layouts, partials y escape automático |
| Esquema de BD | `database/schema.sql` | Migraciones versionadas |
| Datos iniciales | `INSERT` en SQL | Seeders idempotentes |

---

## 2. Listado de Clases, Controladores y Modelos

### Modelos (`app/Models`)

- **`User`**: usuario autenticable. Campos `name`, `email`, `password` y `rol` (`admin`, `arbitro`, `usuario`). Usa el trait `Notifiable` y castea la contraseña con `hashed`.
- **`Equipo`**: franquicia deportiva (`nombre` único, `ciudad`, `entrenador`). Relaciones `jugadores`, `partidosLocal` y `partidosVisitante`.
- **`Jugador`**: deportista vinculado a un equipo (`equipo_id`, `nombre`, `numero`, `posicion`, `edad`, `altura`). Define la constante `POSICIONES` y la relación `equipo`.
- **`Partido`**: encuentro (`equipo_local_id`, `equipo_visitante_id`, `fecha`, `hora`, `lugar`, `puntos_local`, `puntos_visitante`, `estado`). Incluye la constante `ESTADOS`, scopes `deEstado()` y `finalizados()`, y accesores `hora_corta` y `estado_etiqueta`.

### Servicios (`app/Services`)

- **`StandingsService`**:
  - `tabla()`: calcula PJ, PG, PP, PF, PC, DIF y Pts de cada equipo y ordena por puntos, diferencia y puntos a favor.
  - `estadisticas()`: totales de partidos, finalizados, programados, equipos, jugadores y promedio de puntos.

### Controladores (`app/Http/Controllers`)

- **`AuthController`**: login, registro y logout.
- **`DashboardController`**: métricas, tabla de posiciones y últimos partidos.
- **`EquipoController`**: CRUD de equipos.
- **`JugadorController`**: CRUD de jugadores y filtro por equipo.
- **`PartidoController`**: listado, programación, carga de marcador y baja.

### Validación (`app/Http/Requests`)

- `LoginRequest`, `RegisterRequest`
- `StoreEquipoRequest`, `UpdateEquipoRequest`
- `StoreJugadorRequest`, `UpdateJugadorRequest`
- `StorePartidoRequest`, `UpdateMarcadorRequest`

Cada request concentra las reglas y los mensajes, manteniendo los controladores delgados.

---

## 3. Descripción de las Principales Funcionalidades

1. **Autenticación de usuarios**: registro, inicio y cierre de sesión. Contraseñas con bcrypt, regeneración de sesión y bloqueo tras 5 intentos fallidos.
2. **Gestión de equipos (CRUD)**: alta, edición, listado y eliminación con validación de nombre único. La eliminación en cascada borra jugadores y partidos asociados.
3. **Gestión de jugadores**: registro por equipo con dorsal único dentro del club, posición (Base, Escolta, Alero, Ala-Pívot, Pívot), edad y altura; filtro por equipo.
4. **Programación y control de partidos**: alta de encuentros con validación de equipos distintos; carga de marcador y cambio de estado.
5. **Estadísticas y tabla de clasificación**: cálculo dinámico con criterio FIBA (2 puntos por victoria, 1 por derrota), desempate por diferencia y puntos a favor, más totales generales.

### Roles y permisos

| Rol | Dashboard / Tabla | Partidos (ver) | Cargar resultado | CRUD equipos y jugadores |
|---|---|---|---|---|
| `admin` | ✅ | ✅ | ✅ | ✅ |
| `arbitro` | ✅ | ✅ | ✅ | ❌ |
| `usuario` | ✅ | ✅ | ❌ | ❌ |

- El registro público asigna el rol `usuario` (solo lectura).
- Los permisos se declaran con `Gate` (`gestionar` y `cargar-resultados`) y se aplican con el middleware `can:`.
- La interfaz oculta los botones de acción según el permiso (`@can` / `@cannot`), y las rutas responden **403** si se intenta acceder por URL directa.

---

## 4. Explicación de Fragmentos Relevantes de Código

### A. Tabla de posiciones (`app/Services/StandingsService.php`)

```php
$partidos = Partido::query()
    ->finalizados()
    ->get(['equipo_local_id', 'equipo_visitante_id', 'puntos_local', 'puntos_visitante']);

foreach ($partidos as $partido) {
    $local = $partido->equipo_local_id;
    $visitante = $partido->equipo_visitante_id;

    $filas[$local]['pj']++;
    $filas[$visitante]['pj']++;
    // ...

    if ($partido->puntos_local > $partido->puntos_visitante) {
        $filas[$local]['puntos'] += 2;
        $filas[$visitante]['puntos'] += 1;
    } else {
        $filas[$visitante]['puntos'] += 2;
        $filas[$local]['puntos'] += 1;
    }
}
```

El orden final se resuelve con `sortBy` sobre varios criterios:

```php
->sortBy([
    ['puntos', 'desc'],
    ['dif', 'desc'],
    ['pf', 'desc'],
    ['nombre', 'asc'],
])
```

### B. Validación con Form Requests (`StoreJugadorRequest`)

```php
'numero' => [
    'required',
    'integer',
    'between:0,99',
    Rule::unique('jugadores', 'numero')
        ->where(fn ($query) => $query->where('equipo_id', $this->input('equipo_id'))),
],
```

El dorsal es único **por equipo**, no global. En la edición se añade `->ignore($this->route('jugador'))` para no chocar con el propio registro.

### C. Regla de negocio del marcador (`UpdateMarcadorRequest`)

```php
function (Validator $validator) {
    $empate = (int) $this->input('puntos_local') === (int) $this->input('puntos_visitante');

    if ($this->input('estado') === 'finalizado' && $empate) {
        $validator->errors()->add('puntos_local', 'Un partido finalizado no puede terminar empatado.');
    }
}
```

### D. Rutas con recursos y nombres en español (`routes/web.php`)

```php
Route::resource('equipos', EquipoController::class)
    ->except('show')
    ->parameters(['equipos' => 'equipo']);
```

Los parámetros se declaran explícitamente para que el *route model binding* use `{equipo}`, `{jugador}` y `{partido}`.

### E. Autorización por rol (`app/Providers/AppServiceProvider.php`)

```php
Gate::define('gestionar', fn (User $user) => $user->isAdmin());

Gate::define(
    'cargar-resultados',
    fn (User $user) => in_array($user->rol, ['admin', 'arbitro'], true)
);
```

Y en las rutas:

```php
Route::middleware('can:cargar-resultados')->group(function () {
    Route::get('/partidos/{partido}/marcador', ...);
    Route::patch('/partidos/{partido}/marcador', ...);
});
```

---

## 5. Problemas Encontrados Durante el Desarrollo y Soluciones

1. **Migración desde PHP nativo a Laravel**
   - *Problema:* la instalación del esqueleto de Laravel sobrescribió `config/Database.php` (que contenía la clase PDO) y eliminó `controllers/`, `models/` y `views/`.
   - *Solución:* se recuperó el código original desde el historial de git y se reescribió cada capa con las convenciones de Laravel (Eloquent, Blade, Form Requests, migraciones).

2. **Nombre de archivo de configuración**
   - *Problema:* `config/Database.php` con mayúscula funciona en Windows, pero no en servidores Linux.
   - *Solución:* se renombró a `config/database.php`.

3. **Extensiones de PHP faltantes**
   - *Problema:* Laravel requiere `mbstring`; las pruebas requieren `pdo_sqlite`/`sqlite3`.
   - *Solución:* se habilitaron en `php.ini` y se documentaron como requisito.

4. **Credenciales de MySQL**
   - *Problema:* el usuario `root` local rechazaba la conexión sin contraseña.
   - *Solución:* se configuró **SQLite** como base local sin dependencias y se dejó el bloque de MySQL documentado en `.env` para producción.

5. **Integridad referencial**
   - *Problema:* riesgo de registros huérfanos al eliminar equipos.
   - *Solución:* llaves foráneas con `cascadeOnDelete()` en las migraciones (`jugadores` y `partidos`).

6. **Empates en básquetbol**
   - *Problema:* el formulario permitía finalizar un partido empatado.
   - *Solución:* validación posterior en `UpdateMarcadorRequest`.

7. **Mensajes de validación en inglés**
   - *Problema:* Laravel no incluye traducciones al español por defecto.
   - *Solución:* se creó `lang/es/validation.php` y se configuró `APP_LOCALE=es`.

---

## 6. Decisiones Técnicas Tomadas y Justificación

- **Laravel 12 en lugar de PHP puro**: aporta enrutamiento, ORM, validación, autenticación y pruebas sin reimplementar infraestructura; reduce código repetitivo y errores de seguridad.
- **Eloquent en lugar de SQL manual**: elimina SQL disperso, previene inyección y facilita relaciones y *eager loading*.
- **Capa de servicio para la tabla**: la lógica FIBA es la parte más delicada del dominio y se prueba de forma aislada.
- **Nombres de dominio en español**: `equipos`, `jugadores` y `partidos` conservan la terminología del negocio y reducen el riesgo de errores en la migración.
- **SQLite para desarrollo**: permite ejecutar el proyecto sin instalar un servidor de base de datos; el mismo esquema funciona en MySQL.
- **CSS propio servido desde `public/`**: mantiene el diseño original y evita un paso de build con Node.
- **Form Requests**: validación declarativa, mensajes centralizados y controladores legibles.

---

## 7. Esquema de Base de Datos

| Tabla | Campos principales |
|---|---|
| `users` | `id`, `name`, `email`, `password`, `rol`, timestamps |
| `equipos` | `id`, `nombre` (único), `ciudad`, `entrenador`, timestamps |
| `jugadores` | `id`, `equipo_id` (FK), `nombre`, `numero`, `posicion`, `edad`, `altura`, timestamps; único `(equipo_id, numero)` |
| `partidos` | `id`, `equipo_local_id` (FK), `equipo_visitante_id` (FK), `fecha`, `hora`, `lugar`, `puntos_local`, `puntos_visitante`, `estado`, timestamps |

Además, Laravel crea `cache`, `jobs`, `sessions` y `password_reset_tokens` para sus servicios internos.

---

## 8. Pruebas

Se implementaron pruebas de funcionalidad con `RefreshDatabase` sobre SQLite en memoria:

- `AuthTest`: acceso de invitados, login válido/inválido, registro y logout.
- `EquipoTest`: alta, nombre único, edición y eliminación.
- `JugadorTest`: alta, dorsal único por equipo, mismo dorsal en otro equipo y filtro.
- `PartidoTest`: programación, equipos distintos, carga de marcador y empate inválido.
- `StandingsTest`: cálculo FIBA y estadísticas generales.
- `AuthorizationTest`: lectura para `usuario`, resultados para `arbitro` y acceso total del `admin`.

Ejecución:

```bash
php artisan test
```

Resultado actual: **27 pruebas, 87 aserciones, todas correctas**.
