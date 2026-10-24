<?php
require_once __DIR__ . '/../config/Database.php';

class BasketballMatch {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll($filtro_estado = null) {
        $sql = "
            SELECT m.*, 
                   el.nombre as local_nombre, 
                   ev.nombre as visitante_nombre 
            FROM partidos m
            JOIN equipos el ON m.equipo_local_id = el.id
            JOIN equipos ev ON m.equipo_visitante_id = ev.id
        ";
        if ($filtro_estado) {
            $sql .= " WHERE m.estado = :estado";
        }
        $sql .= " ORDER BY m.fecha DESC, m.hora DESC";

        $stmt = $this->conn->prepare($sql);
        if ($filtro_estado) {
            $stmt->execute(['estado' => $filtro_estado]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT m.*, 
                   el.nombre as local_nombre, 
                   ev.nombre as visitante_nombre 
            FROM partidos m
            JOIN equipos el ON m.equipo_local_id = el.id
            JOIN equipos ev ON m.equipo_visitante_id = ev.id
            WHERE m.id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($equipo_local_id, $equipo_visitante_id, $fecha, $hora, $lugar, $puntos_local = 0, $puntos_visitante = 0, $estado = 'programado') {
        $stmt = $this->conn->prepare("
            INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha, hora, lugar, puntos_local, puntos_visitante, estado)
            VALUES (:equipo_local_id, :equipo_visitante_id, :fecha, :hora, :lugar, :puntos_local, :puntos_visitante, :estado)
        ");
        return $stmt->execute([
            'equipo_local_id' => $equipo_local_id,
            'equipo_visitante_id' => $equipo_visitante_id,
            'fecha' => $fecha,
            'hora' => $hora,
            'lugar' => trim($lugar),
            'puntos_local' => (int)$puntos_local,
            'puntos_visitante' => (int)$puntos_visitante,
            'estado' => $estado
        ]);
    }

    public function updateScoreAndStatus($id, $puntos_local, $puntos_visitante, $estado) {
        $stmt = $this->conn->prepare("
            UPDATE partidos 
            SET puntos_local = :puntos_local, puntos_visitante = :puntos_visitante, estado = :estado 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'puntos_local' => (int)$puntos_local,
            'puntos_visitante' => (int)$puntos_visitante,
            'estado' => $estado
        ]);
    }

    public function update($id, $equipo_local_id, $equipo_visitante_id, $fecha, $hora, $lugar, $puntos_local, $puntos_visitante, $estado) {
        $stmt = $this->conn->prepare("
            UPDATE partidos 
            SET equipo_local_id = :equipo_local_id,
                equipo_visitante_id = :equipo_visitante_id,
                fecha = :fecha,
                hora = :hora,
                lugar = :lugar,
                puntos_local = :puntos_local,
                puntos_visitante = :puntos_visitante,
                estado = :estado 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'equipo_local_id' => $equipo_local_id,
            'equipo_visitante_id' => $equipo_visitante_id,
            'fecha' => $fecha,
            'hora' => $hora,
            'lugar' => trim($lugar),
            'puntos_local' => (int)$puntos_local,
            'puntos_visitante' => (int)$puntos_visitante,
            'estado' => $estado
        ]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM partidos WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getStandings() {
        // Calcula la tabla de posiciones con estadísticas básicas: PJ, PG, PP, PF, PC, DIF, Pts
        $stmt = $this->conn->query("SELECT id, nombre FROM equipos");
        $teams = $stmt->fetchAll();

        $table = [];
        foreach ($teams as $t) {
            $table[$t['id']] = [
                'id' => $t['id'],
                'nombre' => $t['nombre'],
                'pj' => 0, // Partidos jugados
                'pg' => 0, // Partidos ganados
                'pp' => 0, // Partidos perdidos
                'pf' => 0, // Puntos a favor
                'pc' => 0, // Puntos en contra
                'dif' => 0, // Diferencia de puntos
                'puntos' => 0 // 2 pts por ganar, 1 por jugar/perder
            ];
        }

        // Obtener todos los partidos finalizados
        $stmt = $this->conn->query("SELECT * FROM partidos WHERE estado = 'finalizado'");
        $matches = $stmt->fetchAll();

        foreach ($matches as $m) {
            $loc = $m['equipo_local_id'];
            $vis = $m['equipo_visitante_id'];
            $pts_loc = (int)$m['puntos_local'];
            $pts_vis = (int)$m['puntos_visitante'];

            if (isset($table[$loc]) && isset($table[$vis])) {
                $table[$loc]['pj']++;
                $table[$vis]['pj']++;
                $table[$loc]['pf'] += $pts_loc;
                $table[$loc]['pc'] += $pts_vis;
                $table[$vis]['pf'] += $pts_vis;
                $table[$vis]['pc'] += $pts_loc;

                if ($pts_loc > $pts_vis) {
                    $table[$loc]['pg']++;
                    $table[$loc]['puntos'] += 2;
                    $table[$vis]['pp']++;
                    $table[$vis]['puntos'] += 1;
                } else {
                    $table[$vis]['pg']++;
                    $table[$vis]['puntos'] += 2;
                    $table[$loc]['pp']++;
                    $table[$loc]['puntos'] += 1;
                }
            }
        }

        foreach ($table as &$row) {
            $row['dif'] = $row['pf'] - $row['pc'];
        }

        // Ordenar por: Puntos (DESC), Diferencia (DESC), Puntos a favor (DESC)
        usort($table, function ($a, $b) {
            if ($b['puntos'] !== $a['puntos']) {
                return $b['puntos'] <=> $a['puntos'];
            }
            if ($b['dif'] !== $a['dif']) {
                return $b['dif'] <=> $a['dif'];
            }
            return $b['pf'] <=> $a['pf'];
        });

        return $table;
    }

    public function getGeneralStats() {
        $totalPartidos = $this->conn->query("SELECT COUNT(*) FROM partidos")->fetchColumn();
        $finalizados = $this->conn->query("SELECT COUNT(*) FROM partidos WHERE estado = 'finalizado'")->fetchColumn();
        $programados = $this->conn->query("SELECT COUNT(*) FROM partidos WHERE estado = 'programado'")->fetchColumn();
        $totalEquipos = $this->conn->query("SELECT COUNT(*) FROM equipos")->fetchColumn();
        $totalJugadores = $this->conn->query("SELECT COUNT(*) FROM jugadores")->fetchColumn();

        $promedioPuntos = 0;
        if ($finalizados > 0) {
            $sumPuntos = $this->conn->query("SELECT SUM(puntos_local + puntos_visitante) FROM partidos WHERE estado = 'finalizado'")->fetchColumn();
            $promedioPuntos = round($sumPuntos / $finalizados, 1);
        }

        return [
            'total_partidos' => $totalPartidos,
            'finalizados' => $finalizados,
            'programados' => $programados,
            'total_equipos' => $totalEquipos,
            'total_jugadores' => $totalJugadores,
            'promedio_puntos' => $promedioPuntos
        ];
    }
}
