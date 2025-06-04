<?php 
namespace App\Controllers;

require_once __DIR__ . '/../models/Barberia.php';
use App\Models\Barberia;

class BarberiaController {
    public function __construct() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(isset($_POST['action'])) {
                switch($_POST['action']) {
                    case 'editarPerfil': 
                        $this->editarPerfil();
                        break;
                }
            }
        }
    }
    public function registrar() {
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if(!$nombre || !$email || !$password) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Todos los campos son obligatorios'
            ]);
            return;
        }

        $barberiaModel = new Barberia();
        $resultado = $barberiaModel->registrar($nombre, $email, $password);

        if($resultado) {
            echo json_encode([
                'success' => true,
                'mensaje' => 'Barbería registrada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Error al registrar la barbería'
            ]);
        }
    }

    public function editarPerfil() {
        session_start();
        $id = $_SESSION['usuario']['id'] ?? null;

        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';

        $servicios = json_decode($_POST['servicios'] ?? '[]', true);
        $empleados = json_decode($_POST['empleados'] ?? '[]', true);
        $horarios = json_decode($_POST['horarios'] ?? '[]', true);
        $redes = json_decode($_POST['redesSociales'] ?? '[]', true);

        $img = $_FILES['img'] ?? null;
        if($img && $img['error'] === UPLOAD_ERR_OK) {
            $nombreImg = uniqid() . '-' . basename($img['name']);
            $rutaDestino = __DIR__ . '/../../assets/src/barbers/' . $nombreImg;
            
            if(!move_uploaded_file($img['tmp_name'], $rutaDestino)) {
                echo json_encode(['error' => 'Error al subir la imagen']);
                exit();
            }
        }else {
            $nombreImg = $_SESSION['usuario']['imagen'] ?? null;
        }

        $passwordHash = null;
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        }

        $modelo = new \App\Models\Barberia();
        $resultado = $modelo->editarBarberiaCompleta(
            $id,
            $nombre,
            $email,
            $passwordHash,
            $nombreImg,
            $direccion,
            $descripcion,
            $servicios,
            $empleados,
            $horarios,
            $redes
        );

        if ($resultado) {
            $_SESSION['usuario']['nombre'] = $nombre;
            $_SESSION['usuario']['email'] = $email;
            $_SESSION['usuario']['imagen'] = $nombreImg;
            $_SESSION['usuario']['direccion'] = $direccion;
            $_SESSION['usuario']['descripcion'] = $descripcion;

            $_SESSION['mensaje'] = "Perfil actualizado correctamente";
        } else {
            $_SESSION['mensaje'] = "Error al actualizar perfil";
        }

        header('Location: /dashboard/groomy/app/views/barberia/editarPerfilBarber.php');
        exit();
    }

    public function listarBarberias() {
        $modelo = new \App\Models\Barberia();
        return $modelo->obtenerBarberias();
    }
}