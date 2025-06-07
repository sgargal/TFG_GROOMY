<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo '<pre>';
print_r($_POST);
echo '</pre>';


require_once __DIR__ . '/../models/Cita.php';
use App\Models\Cita;

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header ('Location: ../public/index.php');
    exit;
}

$idUsuario = $_POST['id_usuario'] ?? null;
$idBarberia = $_POST['id_barberia'] ?? null;
$idBarbero = $_POST['id_barbero'] ?? null;
$idServicio = $_POST['id_servicio'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$hora = $_POST['hora'] ?? null;
$metodoPago = $_POST['metodo_pago'] ?? null;

if (!$idUsuario || !$idBarberia || !$idServicio || !$fecha || !$hora || !$metodoPago) {
    echo "Faltan datos obligatorios.";
    exit;
}

$fechaHora = "$fecha $hora:00";
$estado = 'no pagada';

if ($idBarbero == 0) {
    $idBarbero = null;
}

$citaModel = new Cita();
$ok = $citaModel->crearCita($idUsuario, $idBarberia, $idBarbero, $idServicio, $metodoPago, $estado, $fechaHora);

if ($ok) {
    header('Location: ../views/barberia/citas.php?reserva=ok');
    exit;
} else {
    echo "Error al guardar la cita.";
}

?>