<?php
// Arranca sesión si hace falta
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/plain'); // Para que Vue reciba texto simple

require_once __DIR__ . '/../../app/models/Cita.php';

use App\Models\Cita;

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Método no permitido';
    exit;
}

// Recoger datos
$idUsuario     = $_POST['id_usuario']     ?? null;
$idBarberia    = $_POST['id_barberia']    ?? null;
$idBarbero     = $_POST['id_barbero']     ?? null;
$idServicio    = $_POST['id_servicio']    ?? null;
$fecha         = $_POST['fecha']          ?? null;
$hora          = $_POST['hora']           ?? null;
$metodoPago    = $_POST['metodo_pago']    ?? null;

// Validar que no falte nada
if (!$idUsuario || !$idBarberia || !$idServicio || !$fecha || !$hora || !$metodoPago) {
    http_response_code(400);
    echo 'Faltan datos obligatorios';
    exit;
}

// Combinar fecha y hora
$fechaHora = "$fecha $hora:00";
$estado = 'no pagada';

// Si no hay barbero concreto
if ($idBarbero == 0) {
    $idBarbero = null;
}

// Guardar en BD
$citaModel = new Cita();
$ok = $citaModel->crearCita(
    $idUsuario,
    $idBarberia,
    $idBarbero,
    $idServicio,
    $metodoPago,
    $estado,
    $fechaHora
);

// Responder
if ($ok) {
    echo 'ok';
} else {
    http_response_code(500);
    echo 'error';
}
