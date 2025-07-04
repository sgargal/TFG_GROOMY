<?php
namespace App\Models;

require_once __DIR__ . '/../config/Conexion.php';

use PDO;
use Config\Conexion;

class Cita {
    private $db;

    public function __construct() {
        $this->db = (new Conexion())->Conectar();
    }

    public function crearCita($idUsuario, $idBarberia, $idBarbero, $idServicio, $metodoPago, $fechaHora)
    {
        $sql = "INSERT INTO cita
            (id_usuario, id_barberia, id_barbero, id_servicio, metodo_pago, fecha_hora) 
            VALUES (:id_usuario, :id_barberia, :id_barbero, :id_servicio, :metodo_pago, :fecha_hora)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->bindParam(':id_barberia', $idBarberia);
        $stmt->bindParam(':id_barbero', $idBarbero);
        $stmt->bindParam(':id_servicio', $idServicio);
        $stmt->bindParam(':metodo_pago', $metodoPago);
        $stmt->bindValue(':fecha_hora', $fechaHora, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function obtenerHorasReservadas($idBarberia, $fecha)
    {
        $stmt = $this->db->prepare("
        SELECT DATE_FORMAT(fecha_hora, '%H:%i') AS hora 
        FROM cita
        WHERE id_barberia = :id 
        AND DATE(fecha_hora) = :fecha");
        $stmt->execute([
            ':id' => $idBarberia,
            ':fecha' => $fecha
        ]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'hora');
    }
    public function obtenerHorasReservadasBarbero($idBarbero, $fecha)
    {
        error_log("➡️ BUSCANDO horas reservadas para barbero=$idBarbero en fecha=$fecha");

        $stmt = $this->db->prepare("
        SELECT DATE_FORMAT(fecha_hora, '%H:%i') AS hora
        FROM cita
        WHERE id_barbero = :barbero
        AND DATE(fecha_hora) = :fecha
    ");

        $stmt->execute([
            ':barbero' => $idBarbero,
            ':fecha' => $fecha
        ]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("📦 Resultado crudo:");
        error_log(print_r($result, true));

        return $result;
    }


    public function obtenerCitasPorUsuario($idUsuario, $estado = 'pendiente')
    {
        $sql = "SELECT 
                c.*, 
                u.nombre AS barberia, 
                s.nombre AS servicio,
                br.nombre AS barbero
            FROM cita c
            JOIN servicio s ON c.id_servicio = s.id
            LEFT JOIN barbero br ON c.id_barbero = br.id
            JOIN barberia ba ON c.id_barberia = ba.id
            JOIN usuarios u ON ba.usuario_id = u.id
            WHERE c.id_usuario = :id AND c.estado = :estado
            ORDER BY c.fecha_hora ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $idUsuario,
            ':estado' => $estado
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerCitasPorBarberia($barberiaId, $fecha, $barberoId = null)
    {
        $sql = "SELECT 
                c.*, 
                u.nombre AS cliente, 
                s.nombre AS servicio, 
                b.nombre AS barbero
            FROM cita c
            JOIN usuarios u ON c.id_usuario = u.id
            JOIN servicio s ON c.id_servicio = s.id
            LEFT JOIN barbero b ON c.id_barbero = b.id
            WHERE c.id_barberia = :barberiaId
              AND DATE(c.fecha_hora) = :fecha";

        if ($barberoId) {
            $sql .= " AND c.id_barbero = :barberoId";
        }

        $sql .= " ORDER BY c.fecha_hora ASC";

        $stmt = $this->db->prepare($sql);

        $params = [
            ':barberiaId' => $barberiaId,
            ':fecha' => $fecha
        ];

        if ($barberoId) {
            $params[':barberoId'] = $barberoId;
        }

        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerBarberosPorBarberia($barberiaId)
    {
        $stmt = $this->db->prepare("SELECT id, nombre FROM barbero WHERE id_barberia = :id");
        $stmt->execute([':id' => $barberiaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerIdBarberiaPorUsuario($usuarioId)
    {
        $stmt = $this->db->prepare("SELECT id FROM barberia WHERE usuario_id = :usuario_id");
        $stmt->execute([':usuario_id' => $usuarioId]);
        return $stmt->fetchColumn();
    }
    public function obtenerInfoServicio($id)
    {
        $stmt = $this->db->prepare("SELECT nombre FROM servicio WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerInfoBarbero($id)
    {
        $stmt = $this->db->prepare("SELECT nombre FROM barbero WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function actualizarEstado($id, $nuevoEstado)
    {
        $stmt = $this->db->prepare("UPDATE cita SET estado = :estado WHERE id = :id");
        return $stmt->execute([
            ':estado' => $nuevoEstado,
            ':id' => $id
        ]);
    }

    public function obtenerInfoCita($id)
    {
        $stmt = $this->db->prepare("
        SELECT 
            c.fecha_hora,
            u.email,
            u.nombre AS nombre_usuario
        FROM cita c
        JOIN usuarios u ON c.id_usuario = u.id
        WHERE c.id = :id
    ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}

?>