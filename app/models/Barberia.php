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

    public function editarBarberiaCompleta($usuarioid, $nombre, $email, $password, $imagen, $direccion, $informacion, $servicios, $empleados, $horarios, $redes)
    {
        try {
            // 1. Actualizar datos básicos del usuario
            $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, imagen = :imagen";
            if ($password) {
                $sql .= ", password = :password";
            }
            $sql .= " WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':imagen', $imagen);
            $stmt->bindParam(':id', $usuarioid);
            if ($password) {
                $stmt->bindParam(':password', $password);
            }
            $stmt->execute();

            // 2. Obtener o crear barbería
            $stmt = $this->db->prepare("SELECT id FROM barberia WHERE usuario_id = :usuario_id");
            $stmt->execute([':usuario_id' => $usuarioid]);
            $barberia = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($barberia) {
                $barberiaId = $barberia['id'];
                $stmt = $this->db->prepare("UPDATE barberia SET direccion = :direccion, informacion = :informacion WHERE id = :id");
                $stmt->execute([
                    ':direccion' => $direccion,
                    ':informacion' => $informacion,
                    ':id' => $barberiaId
                ]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO barberia (direccion, informacion, usuario_id) VALUES (:direccion, :info, :usuario_id)");
                $stmt->execute([
                    ':direccion' => $direccion,
                    ':info' => $informacion,
                    ':usuario_id' => $usuarioid
                ]);
                $barberiaId = $this->db->lastInsertId();
            }

            // 3. Borrar datos antiguos
            $this->db->prepare("DELETE FROM servicio WHERE id_barberia = :id")->execute([':id' => $barberiaId]);
            $this->db->prepare("DELETE FROM barbero WHERE id_barberia = :id")->execute([':id' => $barberiaId]);
            $this->db->prepare("DELETE FROM horario WHERE id_barberia = :id")->execute([':id' => $barberiaId]);
            $this->db->prepare("DELETE FROM redes_sociales WHERE id_barberia = :id")->execute([':id' => $barberiaId]);

            // 4. Insertar servicios
            foreach ($servicios as $servicio) {
                $stmt = $this->db->prepare("INSERT INTO servicio (nombre, precio, id_barberia) VALUES (:nombre, :precio, :id)");
                $stmt->execute([
                    ':nombre' => $servicio['nombre'],
                    ':precio' => $servicio['precio'],
                    ':id' => $barberiaId
                ]);
            }

            // 5. Insertar empleados (barberos)
            foreach ($empleados as $empleado) {
                $stmt = $this->db->prepare("INSERT INTO barbero (nombre, imagen, id_barberia) VALUES (:nombre, :imagen, :id)");
                $stmt->execute([
                    ':nombre' => $empleado['nombre'],
                    ':imagen' => $empleado['imagen'] ?? null,
                    ':id' => $barberiaId
                ]);
            }

            // 6. Insertar horarios
            foreach ($horarios as $horario) {
                $stmt = $this->db->prepare("INSERT INTO horario (id_barberia, dia, hora_inicio, hora_fin) VALUES (:id, :dia, :inicio, :fin)");
                $stmt->execute([
                    ':id' => $barberiaId,
                    ':dia' => $horario['dia'],
                    ':inicio' => $horario['inicio'],
                    ':fin' => $horario['fin']
                ]);
            }

            // 7. Insertar redes sociales
            foreach ($redes as $red) {
                $stmt = $this->db->prepare("INSERT INTO redes_sociales (id_barberia, nombre_red_social, url) VALUES (:id, :nombre, :url)");
                $stmt->execute([
                    ':id' => $barberiaId,
                    ':nombre' => $red['tipo'],
                    ':url' => $red['url']
                ]);
            }

            return true;
        } catch (\PDOException $e) {
            error_log("Error en editarBarberiaCompleta: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerBarberias() {
        $sql ="SELECT u.id, u.nombre, u.imagen, b.direccion
                FROM usuarios u
                LEFT JOIN barberia b ON u.id = b.usuario_id
                WHERE u.rol = 'barberia'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}