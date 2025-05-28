<?php 
namespace App\Controllers;

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
        echo json_encode(['success' => true, 'mensaje' => 'Inicio de sesión exitoso']);
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