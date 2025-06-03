<?php

namespace App\Models;
require_once __DIR__ . '/../config/Conexion.php';

use Config\Conexion;
use PDO;

class Barberia
{
    private $db;

    public function __construct()
    {
        $this->db = (new Conexion())->Conectar();
    }

    public function registrar($nombre, $email, $password)
    {
        try {
            if (!$this->db) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'No hay conexión con la base de datos.'
                ]);
                exit;
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'barberia')";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $passwordHash);

            return $stmt->execute();
        } catch (\PDOException $e) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Error al registrar la barbería: ' . $e->getMessage()
            ]);
            exit;
        }
    }

}