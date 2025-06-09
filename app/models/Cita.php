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

    public function obtenerCitasPorUsuario($idUsuario) {
        $stmt = $this->db->prepare("
            SELECT c.id, c.metodo_pago, c.estado, c.fecha_hora,
                    b.nombre AS barberia,
                    s.nombre AS servicio,
                    e.nombre AS barbero,
            FROM cita c
            JOIN barberia b ON c.id_barberia = b.id
            JOIN servicio s ON c.id_servicio = s.id
            JOIN barbero e ON  c.id_barbero = e.id
            WHERE c.id_usuario = :id
            ORDER BY c.fecha_hora DESC");
        $stmt->execute([':id' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}

?>