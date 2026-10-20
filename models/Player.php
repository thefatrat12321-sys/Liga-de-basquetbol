<?php
require_once __DIR__ . '/../config/Database.php';

class Player {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll($equipo_id = null) {
        if ($equipo_id) {
            $stmt = $this->conn->prepare("
                SELECT j.*, e.nombre as equipo_nombre 
                FROM jugadores j 
                JOIN equipos e ON j.equipo_id = e.id 
                WHERE j.equipo_id = :equipo_id 
                ORDER BY j.numero ASC
            ");
            $stmt->execute(['equipo_id' => $equipo_id]);
        } else {
            $stmt = $this->conn->query("
                SELECT j.*, e.nombre as equipo_nombre 
                FROM jugadores j 
                JOIN equipos e ON j.equipo_id = e.id 
                ORDER BY e.nombre ASC, j.numero ASC
            ");
        }
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM jugadores WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($equipo_id, $nombre, $numero, $posicion, $edad, $altura) {
        $stmt = $this->conn->prepare("
            INSERT INTO jugadores (equipo_id, nombre, numero, posicion, edad, altura) 
            VALUES (:equipo_id, :nombre, :numero, :posicion, :edad, :altura)
        ");
        return $stmt->execute([
            'equipo_id' => $equipo_id,
            'nombre' => trim($nombre),
            'numero' => (int)$numero,
            'posicion' => trim($posicion),
            'edad' => !empty($edad) ? (int)$edad : null,
            'altura' => !empty($altura) ? (float)$altura : null
        ]);
    }

    public function update($id, $equipo_id, $nombre, $numero, $posicion, $edad, $altura) {
        $stmt = $this->conn->prepare("
            UPDATE jugadores 
            SET equipo_id = :equipo_id, nombre = :nombre, numero = :numero, posicion = :posicion, edad = :edad, altura = :altura 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'equipo_id' => $equipo_id,
            'nombre' => trim($nombre),
            'numero' => (int)$numero,
            'posicion' => trim($posicion),
            'edad' => !empty($edad) ? (int)$edad : null,
            'altura' => !empty($altura) ? (float)$altura : null
        ]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM jugadores WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
