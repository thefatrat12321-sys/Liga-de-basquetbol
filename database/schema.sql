-- Base de datos para Gestión de Liga de Básquetbol
CREATE DATABASE IF NOT EXISTS `liga_basquet` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `liga_basquet`;

-- 1. Tabla de Usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `rol` ENUM('admin', 'arbitro', 'usuario') DEFAULT 'admin',
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabla de Equipos
CREATE TABLE IF NOT EXISTS `equipos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL UNIQUE,
    `ciudad` VARCHAR(100) NOT NULL,
    `entrenador` VARCHAR(100) DEFAULT NULL,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla de Jugadores
CREATE TABLE IF NOT EXISTS `jugadores` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `equipo_id` INT NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `numero` INT NOT NULL,
    `posicion` VARCHAR(50) NOT NULL,
    `edad` INT DEFAULT NULL,
    `altura` DECIMAL(3,2) DEFAULT NULL,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`equipo_id`) REFERENCES `equipos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabla de Partidos
CREATE TABLE IF NOT EXISTS `partidos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `equipo_local_id` INT NOT NULL,
    `equipo_visitante_id` INT NOT NULL,
    `fecha` DATE NOT NULL,
    `hora` TIME NOT NULL,
    `lugar` VARCHAR(150) NOT NULL,
    `puntos_local` INT DEFAULT 0,
    `puntos_visitante` INT DEFAULT 0,
    `estado` ENUM('programado', 'en_juego', 'finalizado', 'cancelado') DEFAULT 'programado',
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`equipo_local_id`) REFERENCES `equipos`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`equipo_visitante_id`) REFERENCES `equipos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserción de Usuario administrador inicial (Contraseña: admin123)
INSERT INTO `usuarios` (`nombre`, `email`, `password`, `rol`) VALUES
('Administrador Liga', 'admin@liga.com', '$2y$12$7X4Bvb8Ei/fvDeLJ8Dy3fOtsjNBJI9z/prMCG5dTjmsxEhOpCfLrq', 'admin')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Datos de prueba para Equipos
INSERT INTO `equipos` (`id`, `nombre`, `ciudad`, `entrenador`) VALUES
(1, 'Halcones Dorados', 'Ciudad Central', 'Carlos Martínez'),
(2, 'Toros Salvajes', 'Valle Norte', 'Roberto Gómez'),
(3, 'Celtas del Sur', 'Metrópolis', 'Alejandro Fernández'),
(4, 'Tiburones Azules', 'Costa Brava', 'Miguel Ángel Soto')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Datos de prueba para Jugadores
INSERT INTO `jugadores` (`equipo_id`, `nombre`, `numero`, `posicion`, `edad`, `altura`) VALUES
(1, 'Javier Pérez', 23, 'Escolta', 24, 1.95),
(1, 'Andrés Luna', 7, 'Base', 22, 1.85),
(1, 'Mario Silva', 15, 'Ala-Pívot', 27, 2.02),
(2, 'Luis Morales', 10, 'Alero', 25, 1.98),
(2, 'Diego Castro', 32, 'Pívot', 29, 2.08),
(3, 'Esteban Ruiz', 11, 'Base', 21, 1.82),
(3, 'Gabriel Vargas', 8, 'Escolta', 26, 1.93),
(4, 'Fernando Ríos', 5, 'Alero', 23, 1.96)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Datos de prueba para Partidos
INSERT INTO `partidos` (`equipo_local_id`, `equipo_visitante_id`, `fecha`, `hora`, `lugar`, `puntos_local`, `puntos_visitante`, `estado`) VALUES
(1, 2, '2026-10-15', '18:00:00', 'Coliseo Municipal Central', 85, 78, 'finalizado'),
(3, 4, '2026-10-16', '20:00:00', 'Estadio Bicentenario', 92, 88, 'finalizado'),
(2, 3, '2026-10-22', '19:00:00', 'Polideportivo Norte', 68, 74, 'finalizado'),
(1, 4, '2026-11-05', '18:30:00', 'Coliseo Municipal Central', 0, 0, 'programado')
ON DUPLICATE KEY UPDATE `id`=`id`;
