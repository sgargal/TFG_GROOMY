<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once '../../app/controllers/BarberiaController.php';
require_once '../../app/models/Barberia.php';

use App\Controllers\BarberiaController;
use App\Models\Barberia;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new BarberiaController();
    
    switch ($_POST['action']) {
        case 'registrarBarberia':
            $controller->registrar();
            break;

        case 'editarPerfil':
            $controller->editarPerfil();
            break;

        default:
            echo json_encode([
                'success' => false,
                'mensaje' => 'Acción no válida'
            ]);
            break;
    }
}elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    session_start();
    $usuario = $_SESSION['usuario'] ?? null;
    if(!$usuario) {
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }

    $action = $_GET['action'] ?? null;
    if ($action === 'obtenerPerfil') {

        $barberiaModel = new Barberia();
        $barberia = $barberiaModel->obtenerPorId($usuario['id']);

        if(!$barberia || !isset($barberia['id'])) {
            echo json_encode(['error' => 'Barbería no encontrada']);
            exit;
        }

        $barberiaId = $barberia['id'];

        $servicios = $barberiaModel->obtenerServicios($barberiaId);
        $empleados = $barberiaModel->obtenerEmpleados($barberiaId);
        $horarios = $barberiaModel->obtenerHorarios($barberiaId);
        $redes = $barberiaModel->obtenerRedes($barberiaId);

        echo json_encode([
            'servicios' => $servicios,
            'empleados' => $empleados,
            'horarios' => $horarios,
            'redesSociales' => $redes
        ]);
        exit;
    }

}
