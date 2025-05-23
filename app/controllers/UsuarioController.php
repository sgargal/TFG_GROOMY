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

        echo $mensaje;
        exit();
    }

    public function login() {
        $email = $this->validarEmail($_POST['email']);
        $password = $this->validarPassword($_POST['password']);

        $usuarioModel = new Usuario(null, null, null, null, null, null);
        $usuario = $usuarioModel->login($email, $password);

        if(!$usuario) {
            echo "Usuario o contraseña incorrectos";
            exit();
        } 
        
        $_SESSION['usuario'] = $usuario;
        echo "Inicio de sesión exitoso";
        exit();
    }

    private function validarNombre($nombre){
        $nombre = trim($nombre);
        if(preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/', $nombre)){
            return $nombre;
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'contenido' => "El nombre no es válido"];
            header('Location: ../views/usuario/formularioRegistro.php');
            exit();
        }
    }

    public function validarEmail($email){
        $email = trim($email);
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return $email;
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'contenido' => "El email no es válido"];
            header('Location: ../views/usuario/formularioRegistro.php');
            exit();
        }
    }

    public function validarPassword($password){
        $password = trim($password);
        if(strlen($password) >= 8){
            return $password;
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'contenido' => 'La contraseña debe tener al menos 8 caracteres'];
            header('Location: ../views/usuario/formularioRegistro.php');
            exit();
        }
    }

}

?>