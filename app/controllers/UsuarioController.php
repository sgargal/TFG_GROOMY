<?php 
namespace App\Controllers;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../models/usuario.php';
use App\Models\Usuario;

class UsuarioController {
    public function __construct() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

        $_SESSION['usuario'] = $usuario;

        // Si es barbería y es su primer login, mostrar mensaje y marcar como visto
        if ($usuario['rol'] === 'barberia' && $usuario['primer_login'] == 1) {
            $_SESSION['mostrar_mensaje_inicio'] = true;

            // Llamar al modelo para marcar primer_login = 0
            $usuarioModel->marcarPrimerLoginComoVisto($usuario['id']);
        }
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
        // Si no se sube nueva imagen, mantener la anterior
        if ($img && $img['error'] === UPLOAD_ERR_OK) {
            $nombreImg = uniqid() . "_" . basename($img['name']);
            $rutaDestino = __DIR__ . '/../../assets/src/users/' . $nombreImg;

            if (!move_uploaded_file($img['tmp_name'], $rutaDestino)) {
                echo json_encode(['error' => 'Error al subir la imagen']);
                exit();
            }
        } else {
            // Mantener imagen actual
            $nombreImg = $_SESSION['usuario']['imagen'] ?? null;
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
       

        $_SESSION['usuario']['nombre'] = $nombre;
        $_SESSION['usuario']['email'] = $email;
        if ($nombreImg) {
            $_SESSION['usuario']['imagen'] = $nombreImg;
        }

        $_SESSION['mensaje'] = $resultado
            ? "Perfil actualizado correctamente."
            : "Hubo un error al actualizar tu perfil.";

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