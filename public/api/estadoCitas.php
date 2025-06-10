<?php
require_once __DIR__ . '/../../app/models/Cita.php';
use App\Models\Cita;

$data = json_decode(file_get_contents("php://input"), true);
$idCita = $data['id_cita'] ?? null;
$nuevoEstado = $data['estado'] ?? null;

if (!$idCita || !$nuevoEstado) {
    echo json_encode(['message' => 'Faltan datos (id_cita o estado).']);
    exit;
}

$modelo = new Cita();
$ok = $modelo->actualizarEstado($idCita, $nuevoEstado);

echo json_encode([
    'message' => $ok ? "Cita actualizada a '$nuevoEstado'." : 'Error al actualizar la cita.'
]);
