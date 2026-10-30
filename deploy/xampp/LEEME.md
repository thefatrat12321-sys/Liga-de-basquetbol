# Ejecutar la liga en local con XAMPP + MySQL

Guía para Windows. La aplicación completa (login, CRUD, carga de resultados) se ejecuta
en tu equipo; GitHub solo publica una **vista previa estática** de solo lectura.

---

## 1. Requisitos

- **XAMPP 8.2 o superior** (PHP 8.2+, MySQL/MariaDB y phpMyAdmin).
- **Composer** ([getcomposer.org](https://getcomposer.org/download/)).
- Git (opcional, para clonar el repositorio).

> Laravel 12 exige PHP 8.2 o superior. Comprueba la versión con:
>
> ```bat
> C:\xampp\php\php.exe -v
> ```

---

## 2. Arrancar XAMPP y crear la base de datos

1. Abre el **Panel de Control de XAMPP** y pulsa **Start** en `Apache` y en `MySQL`.
2. Entra en <http://localhost/phpmyadmin>.
3. **Nueva** → nombre de la base: `liga_basquet` → cotejamiento `utf8mb4_unicode_ci` → **Crear**.

También puedes crearla por consola:

```bat
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS liga_basquet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

---

## 3. Configurar el proyecto

Copia la carpeta del proyecto donde quieras (por ejemplo `C:\xampp\htdocs\liga`)
y abre una terminal en esa carpeta.

```bat
copy .env.example .env
```

El `.env.example` ya viene con la configuración de XAMPP:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=liga_basquet
DB_USERNAME=root
DB_PASSWORD=
```

> XAMPP usa `root` sin contraseña por defecto. Si le pusiste una, escríbela en
> `DB_PASSWORD`. Si prefieres no usar MySQL, comenta ese bloque y descomenta
> `# DB_CONNECTION=sqlite`.

---

## 4. Extensiones de PHP

Abre `C:\xampp\php\php.ini` y verifica que estas líneas **no** tengan `;` delante:

```ini
extension=pdo_mysql
extension=mbstring
extension=fileinfo
extension=openssl
extension=zip
```

Guarda el archivo y reinicia **Apache** desde el panel de XAMPP.

---

## 5. Instalar y arrancar

```bat
composer install
C:\xampp\php\php.exe artisan key:generate
C:\xampp\php\php.exe artisan migrate:fresh --seed
C:\xampp\php\php.exe artisan serve
```

Abre <http://127.0.0.1:8000>.

> Si `composer` o `php` no se reconocen en la terminal, usa las rutas completas:
> `C:\xampp\php\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar install`.

### Cuentas demo

| Correo | Contraseña | Rol |
|---|---|---|
| `admin@liga.com` | `admin123` | admin (todo) |
| `arbitro@liga.com` | `arbitro123` | arbitro (cargar resultados) |
| `espectador@liga.com` | `usuario123` | usuario (solo lectura) |

---

## 6. Opcional: usar Apache en lugar de `artisan serve`

Si quieres entrar por `http://liga.local` sin dejar una terminal abierta:

1. Añade a `C:\Windows\System32\drivers\etc\hosts`:

   ```text
   127.0.0.1 liga.local
   ```

2. Crea `C:\xampp\apache\conf\extra\httpd-vhosts.conf` (o añádelo al final):

   ```apache
   <VirtualHost *:80>
       ServerName liga.local
       DocumentRoot "C:/xampp/htdocs/liga/public"

       <Directory "C:/xampp/htdocs/liga/public">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

3. En `C:\xampp\apache\conf\httpd.conf` descomenta:

   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   Include conf/extra/httpd-vhosts.conf
   ```

4. Reinicia Apache y ajusta en `.env`:

   ```dotenv
   APP_URL=http://liga.local
   ```

5. Ejecuta `php artisan config:clear`.

---

## 7. Actualizar la vista previa de GitHub

Cuando modifiques equipos, jugadores o resultados en local:

```bat
C:\xampp\php\php.exe artisan liga:preview
git add docs
git commit -m "actualizar vista previa"
git push
```

GitHub Pages tardará uno o dos minutos en reflejar los cambios.

---

## Problemas comunes

| Error | Solución |
|---|---|
| `could not find driver` | Activa `extension=pdo_mysql` en `php.ini` y reinicia Apache. |
| `Access denied for user 'root'@'localhost'` | Revisa `DB_PASSWORD` en `.env` (por defecto vacío). |
| `php` no se reconoce como comando | Usa `C:\xampp\php\php.exe` en lugar de `php`. |
| `SQLSTATE[HY000] [2002]` | MySQL no está arrancado en el panel de XAMPP. |
| Cambios en `.env` sin efecto | Ejecuta `php artisan config:clear`. |
| Página en blanco o error 500 | Revisa `storage/logs/laravel.log` y los permisos de `storage/` y `bootstrap/cache/`. |
