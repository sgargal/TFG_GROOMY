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






}

?>