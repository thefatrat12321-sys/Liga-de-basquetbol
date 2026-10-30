# Documentación Técnica: Sistema de Gestión de Liga de Básquetbol

## 1. Descripción General de la Arquitectura del Sistema
El sistema se desarrolló implementando el patrón Modelo-Vista-Controlador (MVC) en PHP nativo, conectado a una base de datos MySQL mediante PDO (PHP Data Objects). Se seleccionó una arquitectura sin frameworks externos para cumplir con la premisa de máxima sencillez, portabilidad y ejecución sin dependencias.

El flujo inicia en el enrutador (`index.php`), el cual evalúa la ruta solicitada, gestiona la sesión y delega la ejecución al controlador respectivo. El controlador consulta los modelos y retorna vistas HTML con estilos limpios y responsivos.

```
Petición HTTP -> index.php (Enrutador)
                      |
             [Controlador (Controller)]
             /                        \
    [Modelo (PDO)]            [Vista (HTML/CSS)]
          |
    Base de Datos MySQL
```

---

## 2. Listado de Clases, Controladores y Modelos Implementados

### Modelos (`models/`):
- **`Database` (`config/Database.php`)**: Administra la conexión persistente PDO hacia MySQL con control de errores mediante excepciones y codificación UTF-8.
- **`User` (`models/User.php`)**: Gestiona la persistencia de usuarios, registro seguro y validación de contraseñas con `password_verify()`.
- **`Team` (`models/Team.php`)**: Encapsula el CRUD de equipos y realiza el conteo de jugadores registrados por club.
- **`Player` (`models/Player.php`)**: Administra los jugadores por equipo, sus dorsales, biometría y posiciones de juego.
- **`BasketballMatch` (`models/BasketballMatch.php`)**: Gestiona el fixture y resultados de partidos. Calcula dinámicamente la tabla general de posiciones (PJ, PG, PP, PF, PC, DIF, Pts) y promedios anotadores.

### Controladores (`controllers/`):
- **`AuthController`**: Controla el flujo de autenticación (login, registro y cierre de sesión).
- **`DashboardController`**: Compila las métricas generales y la tabla de posiciones.
- **`TeamController`**: Procesa la creación, edición, listado y eliminación de equipos.
- **`PlayerController`**: Gestiona la inscripción y edición de jugadores.
- **`MatchController`**: Administra la programación de encuentros y el registro de marcadores.

---

## 3. Descripción de las Principales Funcionalidades Desarrolladas
1. **Autenticación de Usuarios**: Acceso seguro con sesiones de PHP y cifrado `BCRYPT` para contraseñas. Protección de vistas administrativas.
2. **Gestión de Equipos (CRUD)**: Creación, actualización y eliminación de franquicias deportivas con validación de nombres únicos y localidad.
3. **Gestión de Jugadores**: Registro de deportistas vinculados a su club, especificando dorsal (0-99), edad, altura y posición (Base, Escolta, Alero, Ala-Pívot, Pívot). Incluye filtro interactivo por equipo.
4. **Programación y Control de Partidos**: Creación de encuentros deportivos y módulo de carga de marcadores en vivo o finalizados.
5. **Estadísticas y Tabla de Clasificación**: Cálculo de puntos oficiales bajo criterio FIBA (2 puntos por victoria, 1 por derrota), diferencia de anotación y estadísticas globales de la competencia.

---

## 4. Explicación de Fragmentos Relevantes de Código

### A. Cálculo Dinámico de la Tabla de Posiciones (`models/BasketballMatch.php`)
```php
public function getStandings() {
    $teams = $this->conn->query("SELECT id, nombre FROM equipos")->fetchAll();
    $table = [];
    foreach ($teams as $t) {
        $table[$t['id']] = [
            'id' => $t['id'], 'nombre' => $t['nombre'],
            'pj' => 0, 'pg' => 0, 'pp' => 0, 'pf' => 0, 'pc' => 0, 'dif' => 0, 'puntos' => 0
        ];
    }
    $matches = $this->conn->query("SELECT * FROM partidos WHERE estado = 'finalizado'")->fetchAll();
    foreach ($matches as $m) {
        $l = $m['equipo_local_id']; $v = $m['equipo_visitante_id'];
        $table[$l]['pj']++; $table[$v]['pj']++;
        $table[$l]['pf'] += $m['puntos_local']; $table[$v]['pf'] += $m['puntos_visitante'];
        $table[$l]['pc'] += $m['puntos_visitante']; $table[$v]['pc'] += $m['puntos_local'];
        if ($m['puntos_local'] > $m['puntos_visitante']) {
            $table[$l]['pg']++; $table[$l]['puntos'] += 2;
            $table[$v]['pp']++; $table[$v]['puntos'] += 1;
        } else {
            $table[$v]['pg']++; $table[$v]['puntos'] += 2;
            $table[$l]['pp']++; $table[$l]['puntos'] += 1;
        }
    }
    return $table;
}
```

### B. Consultas Preparadas para Seguridad SQL (`models/Player.php`)
```php
public function create($equipo_id, $nombre, $numero, $posicion, $edad, $altura) {
    $stmt = $this->conn->prepare("
        INSERT INTO jugadores (equipo_id, nombre, numero, posicion, edad, altura) 
        VALUES (:equipo_id, :nombre, :numero, :posicion, :edad, :altura)
    ");
    return $stmt->execute([
        'equipo_id' => $equipo_id, 'nombre' => trim($nombre),
        'numero' => (int)$numero, 'posicion' => trim($posicion),
        'edad' => !empty($edad) ? (int)$edad : null,
        'altura' => !empty($altura) ? (float)$altura : null
    ]);
}
```

---

## 5. Problemas Encontrados Durante el Desarrollo y Soluciones

1. **Evitar sobrecarga innecesaria de dependencias**:
   - *Problema:* El uso de frameworks complejos implicaba múltiples comandos de instalación y archivos de configuración densos.
   - *Solución:* Se adoptó PHP MVC puro con PDO, facilitando una ejecución directa e inmediata en cualquier servidor local como XAMPP.

2. **Manejo de integridad referencial**:
   - *Problema:* Riesgo de registros huérfanos al suprimir equipos con jugadores o encuentros vinculados.
   - *Solución:* Se configuró `ON DELETE CASCADE` en las llaves foráneas de la base de datos MySQL.

3. **Cálculo de puntaje deportivo**:
   - *Problema:* En básquetbol no existen empates reglamentarios y la puntuación difiere del fútbol tradicional.
   - *Solución:* Se programó el sistema oficial FIBA otorgando 2 unidades al ganador y 1 unidad al perdedor.

---

## 6. Decisiones Técnicas Tomadas y Justificación

- **PHP Estructurado en MVC**: Garantiza legibilidad y separación de capas sin sobrecarga.
- **MySQL con Motor InnoDB**: Asegura integridad transaccional y llaves foráneas seguras.
- **CSS Responsivo Moderno**: Interfaz visual atractiva y ligera sin librerías externas.
