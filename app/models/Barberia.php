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

            // 5. Borrar solo los servicios que NO están en el POST y que no tienen citas
            // — obtiene todos los IDs enviados desde el formulario
            $idsEnForm = array_column($servicios, 'id');

            // Si algún servicio nuevo viene sin ID, lo insertaremos más abajo
            $idsEnFormFiltrados = array_filter($idsEnForm, fn($x) => !empty($x));

            // Prepara la lista de IDs seguros para borrar (si existe y no está en form)
            $sqlDel = "DELETE FROM servicio
                        WHERE id_barberia = :barberia
                        AND id NOT IN (" . (empty($idsEnFormFiltrados) ? "0" : implode(',', $idsEnFormFiltrados)) . ")
                        AND id NOT IN (SELECT id_servicio FROM cita WHERE id_barberia = :barberia)
                ";
            $stmt = $this->db->prepare($sqlDel);
            $stmt->execute([':barberia' => $barberiaId]);

            // 6. Insertar servicios
            foreach ($servicios as $servicio) {
                $id = $servicio['id'] ?? null;
                $nombre = trim($servicio['nombre']);
                $precio = $servicio['precio'];

                if ($nombre === '' || $precio === '' || $precio === null) {
                    continue;
                }

                if ($id) {
                    // Actualizar servicio existente
                    $stmt = $this->db->prepare("UPDATE servicio SET nombre = :nombre, precio = :precio WHERE id = :id AND id_barberia = :barberia");
                    $stmt->execute([
                        ':nombre' => $nombre,
                        ':precio' => $precio,
                        ':id' => $id,
                        ':barberia' => $barberiaId
                    ]);
                } else {
                    // Insertar nuevo servicio
                    $stmt = $this->db->prepare("INSERT INTO servicio (nombre, precio, id_barberia) VALUES (:nombre, :precio, :id)");
                    $stmt->execute([
                        ':nombre' => $nombre,
                        ':precio' => $precio,
                        ':id' => $barberiaId
                    ]);
                }
            }

            //   7. Empleados: borrar solo los que no vienen en POST
            $idsEmpsEnForm = array_column($empleados, 'id');
            $idsEmpsFiltr = array_filter($idsEmpsEnForm, fn($x) => !empty($x));

            
            $sqlDelEmp = "DELETE FROM barbero
                            WHERE id_barberia = :barberia
                            AND id NOT IN (" . (empty($idsEmpsFiltr) ? "0" : implode(',', $idsEmpsFiltr)) . ")";
            $this->db->prepare($sqlDelEmp)->execute([':barberia' => $barberiaId]);

            // Ahora recorrer POST para actualizar o insertar
            foreach ($empleados as $emp) {
                $idEmp = $emp['id'] ?? null;
                $nombre = trim($emp['nombre']);
                if ($nombre === '') continue;

                if ($idEmp) {
                    // Actualiza existente
                    $stmt = $this->db->prepare("UPDATE barbero
                                                SET nombre = :nombre, imagen = :imagen
                                                WHERE id = :id AND id_barberia = :barberia");
                    $stmt->execute([
                        ':nombre'  => $nombre,
                        ':imagen'  => $emp['imagen'] ?? null,
                        ':id'      => $idEmp,
                        ':barberia' => $barberiaId
                    ]);
                } else {
                    // Inserta nuevo
                    $stmt = $this->db->prepare("INSERT INTO barbero (id_barberia, nombre, imagen)
                                                VALUES (:barberia, :nombre, :imagen)");
                    $stmt->execute([
                        ':barberia' => $barberiaId,
                        ':nombre'  => $nombre,
                        ':imagen'  => $emp['imagen'] ?? null
                    ]);
                }
            }

            //   9. Redes sociales: lo mismo
            $idsRedesEnForm = array_column($redes, 'id');
            $idsRedesFiltr = array_filter($idsRedesEnForm, fn($x) => !empty($x));

            $sqlDelRed = " DELETE FROM redes_sociales
                            WHERE id_barberia = :barberia
                            AND id NOT IN (" . (empty($idsRedesFiltr) ? "0" : implode(',', $idsRedesFiltr)) . ")";
            $this->db->prepare($sqlDelRed)->execute([':barberia' => $barberiaId]);

            foreach ($redes as $red) {
                $idRed = $red['id'] ?? null;
                $tipo  = trim($red['tipo']);
                $url   = trim($red['url']);
                if ($tipo === '' || $url === '') continue;

                if ($idRed) {
                    // Actualiza existente
                    $stmt = $this->db->prepare("UPDATE redes_sociales
                                                SET nombre_red_social = :tipo, url = :url
                                                WHERE id = :id AND id_barberia = :barberia");
                    $stmt->execute([
                        ':tipo'    => $tipo,
                        ':url'     => $url,
                        ':id'      => $idRed,
                        ':barberia' => $barberiaId
                    ]);
                } else {
                    // Inserta nueva
                    $stmt = $this->db->prepare("INSERT INTO redes_sociales (id_barberia, nombre_red_social, url)
                                                VALUES (:barberia, :tipo, :url)");
                    $stmt->execute([
                        ':barberia' => $barberiaId,
                        ':tipo'    => $tipo,
                        ':url'     => $url
                    ]);
                }
                
            }
            // 10. Horarios: eliminar antiguos y guardar nuevos
                $this->db->prepare("DELETE FROM horario WHERE id_barberia = :barberia")
                    ->execute([':barberia' => $barberiaId]);

                $insertHorario = $this->db->prepare(
                    "INSERT INTO horario (id_barberia, dia, hora_inicio, hora_fin) VALUES (:barberia, :dia, :inicio, :fin)"
                );

                foreach ($horarios as $h) {
                    $dia    = $h['dia'] ?? '';
                    $inicio = $h['inicio'] ?? '';
                    $fin    = $h['fin'] ?? '';

                    if ($dia && $inicio && $fin) {
                        $insertHorario->execute([
                            ':barberia' => $barberiaId,
                            ':dia'      => $dia,
                            ':inicio'   => $inicio,
                            ':fin'      => $fin
                        ]);
                    }
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
        $stmt = $this->db->prepare("SELECT id, nombre, precio FROM servicio WHERE id_barberia = :id");
        $stmt->execute([':id' => $barberiaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEmpleados($barberiaId)
    {
        $stmt = $this->db->prepare("SELECT id, nombre, imagen FROM barbero WHERE id_barberia = :id");
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