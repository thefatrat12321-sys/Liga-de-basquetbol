<?php
require_once __DIR__ . '/../config/Database.php';

class Team {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("
            SELECT e.*, COUNT(j.id) as total_jugadores 
            FROM equipos e 
            LEFT JOIN jugadores j ON e.id = j.equipo_id 
            GROUP BY e.id 
            ORDER BY e.nombre ASC
        ");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM equipos WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($nombre, $ciudad, $entrenador) {
        $stmt = $this->conn->prepare("INSERT INTO equipos (nombre, ciudad, entrenador) VALUES (:nombre, :ciudad, :entrenador)");
        return $stmt->execute([
            'nombre' => trim($nombre),
            'ciudad' => trim($ciudad),
            'entrenador' => trim($entrenador)
        ]);
    }

    public function update($id, $nombre, $ciudad, $entrenador) {
        $stmt = $this->conn->prepare("UPDATE equipos SET nombre = :nombre, ciudad = :ciudad, entrenador = :entrenador WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'nombre' => trim($nombre),
            'ciudad' => trim($ciudad),
            'entrenador' => trim($entrenador)
        ]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM equipos WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
