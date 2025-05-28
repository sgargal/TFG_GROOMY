<?php
session_start();

require_once '../../app/models/usuario.php';
use App\Models\Usuario;

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$model = new Usuario();
$usuario = $model->login($email, $password);

if ($usuario) {
    $_SESSION['usuario'] = [
        'email' => $usuario['email'],
        'nombre' => $usuario['nombre'],
        'imagen' => $usuario['imagen']
    ];

    header("Location: ../index.php");
    exit;
} else {
    echo 'error';
}
?>