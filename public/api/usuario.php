<?php
require_once '../../app/controllers/UsuarioController.php';
use App\Controllers\UsuarioController;

$controller = new UsuarioController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_POST['action']) {
        case 'registrar': 
            $controller->registrar();
            break;
        case 'login':
            $controller->login();
            break;
        default:
            echo "Acción no válida";
    }
}
?>