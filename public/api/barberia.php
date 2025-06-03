<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once '../../app/controllers/BarberiaController.php';

use App\Controllers\BarberiaController;

$controller = new BarberiaController();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'registrarBarberia' ) {
        $controller->registrar();
    }else{
        echo json_encode([
            'success' => false,
            'mensaje' => 'Accion no válida'
        ]);
    }
}