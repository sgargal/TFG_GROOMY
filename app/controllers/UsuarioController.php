<?php 
namespace App\Controllers;

require_once __DIR__ . '/../models/usuario.php';
use App\Models\Usuario;

class UsuarioController {
    public function __construct() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // echo "<pre>";
            // print_r($_POST);
            // print_r($_FILES);
            // echo "</pre>";
            // exit;
            if(isset($_POST['action'])) {
                switch($_POST['action']) {
                    case 'registrar': 
                        $this->registrar();
                        break;
                    case 'login':
                        $this->login();
                        break;
                    case 'editarPerfil':
                        $this->editarPerfil();
                        break;
                    default:
                        echo "Acción no válida";
                        break;
                }
            }
        }
    }

    public function registrar() {
        $nombre = $this->validarNombre($_POST['nombre']);
        $email = $this->validarEmail($_POST['email']);
        $password = $this->validarPassword($_POST['password']);

        $usuario = new Usuario(null, $nombre, $email, $password, 'user', null);
        $mensaje = $usuario->registrar();

        // Comprobar si el mensaje indica éxito
        $success = ($mensaje === 'Usuario registrado correctamente');

        echo json_encode([
            'success' => $success,
            'mensaje' => $mensaje
        ]);
        exit();
    }

    public function login() {
        $email = $this->validarEmail($_POST['email']);
        $password = $this->validarPassword($_POST['password']);

        $usuarioModel = new Usuario(null, null, null, null, null, null);
        $usuario = $usuarioModel->login($email, $password);

        if(!$usuario) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Usuario o contraseña incorrectos'
            ]);
            exit();
        } 
        var_dump($_SESSION['usuario']['id']);
        exit();
        
        $_SESSION['usuario'] = $usuario;
        echo json_encode(['success' => true, 'mensaje' => 'Inicio de sesión exitoso']);
        exit();
    }

    public function editarPerfil() {
        $nombre = $this->validarNombre($_POST['nombre']);
        $email = $this->validarEmail($_POST['email']);
        $img = $_FILES['img'] ?? null;

        if(!$nombre || !$email){
            echo json_encode(['error' => 'Nombre o email no válidos']);
            exit();
        }

        $nombreImg = null;

        if ($img && $img['error'] === UPLOAD_ERR_OK) {
            $nombreImg = uniqid() . "_" . basename($img['name']);
            $rutaDestino = __DIR__ . '/../../assets/src/users/' . $nombreImg;

            if (!move_uploaded_file($img['tmp_name'], $rutaDestino)) {
                echo json_encode(['error' => 'Error al subir la imagen']);
                exit();
            }
        }

        $usuario = new Usuario($_SESSION['usuario']['id'], $nombre, $email, null, $_SESSION['usuario']['rol'], $nombreImg);

        $resultado = $usuario->editarUsuario(
            $_SESSION['usuario']['id'],
            $nombre,
            $email,
            null,
            $nombreImg,
            $_SESSION['usuario']['rol']
        );
        echo json_encode(['mensaje' => $resultado]);
        // Guardar en sesión solo el nombre o ruta web, NO la ruta física
        $_SESSION['usuario']['nombre'] = $nombre;
        $_SESSION['usuario']['email'] = $email;
        if ($nombreImg) {
            $_SESSION['usuario']['imagen'] = $nombreImg;
        }

        $_SESSION['mensaje'] = $resultado;

        header('Location: /dashboard/groomy/public/index.php');
        exit();
    }

    private function validarNombre($nombre){
        $nombre = trim($nombre);
        if(preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/', $nombre)){
            return $nombre;
        } else {
            echo json_encode(['error' => 'El nombre solo puede contener letras y espacios']);
            exit();
        }
    }

    public function validarEmail($email){
        $email = trim($email);
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return $email;
        } else {
            echo json_encode(['error' => 'El email no es válido']);
            exit();
        }
    }

    public function validarPassword($password){
        $password = trim($password);
        if(strlen($password) >= 3){
            return $password;
        } else {
            echo json_encode(['error' => 'La contraseña debe tener al menos 3 caracteres']);
            exit();
        }
    }

}

?>