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
            // 1. Obtener datos actuales
            $stmt = $this->db->prepare("SELECT u.nombre, u.email, u.imagen, b.direccion, b.informacion
                                    FROM usuarios u
                                    LEFT JOIN barberia b ON u.id = b.usuario_id
                                    WHERE u.id = :id");
            $stmt->execute([':id' => $usuarioid]);
            $actual = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Usar actuales si los nuevos están vacíos
            $nombre = !empty($nombre) ? $nombre : $actual['nombre'];
            $email = !empty($email) ? $email : $actual['email'];
            $imagen = !empty($imagen) ? $imagen : $actual['imagen'];
            $direccion = !empty($direccion) ? $direccion : $actual['direccion'];
            $informacion = !empty($informacion) ? $informacion : $actual['informacion'];

            // 3. Actualizar datos del usuario
            $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, imagen = :imagen";
            if (!empty($password)) {
                $sql .= ", password = :password";
            }
            $sql .= " WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':imagen', $imagen);
            $stmt->bindParam(':id', $usuarioid);
            if (!empty($password)) {
                $stmt->bindParam(':password', $password);
            }
            $stmt->execute();

            // 4. Obtener o crear barbería
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

            // 5. Borrar datos antiguos
            $this->db->prepare("DELETE FROM servicio WHERE id_barberia = :id")->execute([':id' => $barberiaId]);
            $this->db->prepare("DELETE FROM barbero WHERE id_barberia = :id")->execute([':id' => $barberiaId]);
            $this->db->prepare("DELETE FROM horario WHERE id_barberia = :id")->execute([':id' => $barberiaId]);
            $this->db->prepare("DELETE FROM redes_sociales WHERE id_barberia = :id")->execute([':id' => $barberiaId]);

            // 6. Insertar servicios
            foreach ($servicios as $servicio) {
                if (empty(trim($servicio['nombre'])) || $servicio['precio'] === '' || $servicio['precio'] === null) {
                    continue;
                }

                $stmt = $this->db->prepare("INSERT INTO servicio (nombre, precio, id_barberia) VALUES (:nombre, :precio, :id)");
                $stmt->execute([
                    ':nombre' => $servicio['nombre'],
                    ':precio' => $servicio['precio'],
                    ':id' => $barberiaId
                ]);
            }


            // 7. Insertar empleados
            foreach ($empleados as $empleado) {
                if (empty(trim($empleado['nombre']))) {
                    continue;
                }

                $stmt = $this->db->prepare("INSERT INTO barbero (nombre, imagen, id_barberia) VALUES (:nombre, :imagen, :id)");
                $stmt->execute([
                    ':nombre' => $empleado['nombre'],
                    ':imagen' => $empleado['imagen'] ?? null,
                    ':id' => $barberiaId
                ]);
            }


            // 8. Insertar horarios
            foreach ($horarios as $horario) {
                if (empty($horario['dia']) || empty($horario['inicio']) || empty($horario['fin'])) {
                    continue;
                }

                $stmt = $this->db->prepare("INSERT INTO horario (id_barberia, dia, hora_inicio, hora_fin) VALUES (:id, :dia, :inicio, :fin)");
                $stmt->execute([
                    ':id' => $barberiaId,
                    ':dia' => $horario['dia'],
                    ':inicio' => $horario['inicio'],
                    ':fin' => $horario['fin']
                ]);
            }


            // 9. Insertar redes sociales
            foreach ($redes as $red) {
                if (empty(trim($red['tipo'])) || empty(trim($red['url']))) {
                    continue;
                }

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

    public function obtenerPorId($usuario_id)
    {
        $sql = "SELECT b.*, u.nombre, u.imagen 
            FROM barberia b
            JOIN usuarios u ON b.usuario_id = u.id
            WHERE b.usuario_id = :usuario_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerServicios($barberiaId)
    {
        $stmt = $this->db->prepare("SELECT nombre, precio FROM servicio WHERE id_barberia = :id");
        $stmt->execute([':id' => $barberiaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEmpleados($barberiaId)
    {
        $stmt = $this->db->prepare("SELECT nombre, imagen FROM barbero WHERE id_barberia = :id");
        $stmt->execute([':id' => $barberiaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerHorarios($barberiaId)
    {
        $stmt = $this->db->prepare("SELECT dia, hora_inicio AS inicio, hora_fin AS fin FROM horario WHERE id_barberia = :id");
        $stmt->execute([':id' => $barberiaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerRedes($barberiaId)
    {
        $stmt = $this->db->prepare("SELECT nombre_red_social AS tipo, url FROM redes_sociales WHERE id_barberia = :id");
        $stmt->execute([':id' => $barberiaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


} 