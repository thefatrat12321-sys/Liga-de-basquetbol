# Sistema de Gestión de Liga de Básquetbol

Aplicación web desarrollada en **PHP nativo** y **MySQL** bajo el patrón **Modelo-Vista-Controlador (MVC)**, diseñada de forma limpia, ligera y sin dependencias externas.

---

## 🚀 Requisitos
- Servidor web Apache con PHP 8.0 o superior (p. ej. XAMPP, WampServer, Laragon).
- Servidor de base de datos MySQL / MariaDB.

---

## ⚙️ Instalación y Puesta en Marcha

### 1. Base de Datos
1. Inicia los servicios de **Apache** y **MySQL** en tu entorno local (ej. panel de control de XAMPP).
2. Abre tu gestor de base de datos preferido (phpMyAdmin, MySQL Workbench o consola).
3. Importa el archivo ubicado en:
   ```
   database/schema.sql
   ```
   *Esto creará la base de datos `liga_basquet` con todas las tablas y datos de ejemplo iniciales.*

### 2. Configuración de Conexión
Si tus credenciales de MySQL son distintas a las predeterminadas (`root` sin contraseña en el puerto 3306), edita el archivo:
```
config/Database.php
```

### 3. Ejecutar la Aplicación
Puedes acceder a través de tu servidor Apache (`http://localhost/LIGA%20BASQUET`) o levantar el servidor interno de PHP ejecutando en esta carpeta:
```bash
php -S localhost:8000
```
Luego ingresa en tu navegador a:
```
http://localhost:8000
```

---

## 🔑 Credenciales de Acceso por Defecto
- **Usuario:** `admin@liga.com`
- **Contraseña:** `admin123`

*(También puedes registrar nuevos usuarios directamente desde la interfaz).*

---

## 📁 Estructura del Proyecto
- `config/`: Configuración y clase Singleton de conexión PDO a MySQL.
- `models/`: Modelos de datos (`User`, `Team`, `Player`, `BasketballMatch`).
- `controllers/`: Lógica de negocio y controladores MVC.
- `views/`: Vistas y plantillas de interfaz de usuario.
- `public/`: Hojas de estilo CSS y recursos estáticos.
- `database/`: Script SQL DDL y DML de la base de datos.
- `doc/`: Documentación técnica obligatoria (`practica_liga.md`).
