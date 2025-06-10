<?php
require_once __DIR__ . '/../../../app/models/Barberia.php';
require_once __DIR__ . '/../../../app/models/Cita.php';


use App\Models\Barberia;
use App\Models\Cita;

header('Content-Type: application/json');

$idBarberia = isset($_GET['id_barberia']) ? (int)$_GET['id_barberia'] : null;
$fecha = $_GET['fecha'] ?? null;
$idBarbero = isset($_GET['id_barbero']) ? (int)$_GET['id_barbero'] : 0;

if (!$idBarberia || !$fecha) {
    echo json_encode([]);
    exit;
}

$barberiaModel = new Barberia();
$citaModel = new Cita();

// Obtener día de la semana (traducido)
$diaIngles = strtolower(date('l', strtotime($fecha)));
$diasTraducidos = [
    'monday' => 'lunes',
    'tuesday' => 'martes',
    'wednesday' => 'miércoles',
    'thursday' => 'jueves',
    'friday' => 'viernes',
    'saturday' => 'sábado',
    'sunday' => 'domingo',
];
$diaSemana = $diasTraducidos[$diaIngles] ?? null;

$horarios = $barberiaModel->obtenerHorarios($idBarberia);
$horarioDia = null;

foreach ($horarios as $h) {
    if (strtolower($h['dia']) === $diaSemana) {
        $horarioDia = $h;
        break;
    }
}

if (!$horarioDia) {
    echo json_encode([]);
    exit;
}

// Generar franjas de 30 minutos
$inicio = strtotime($horarioDia['inicio']);
$fin = strtotime($horarioDia['fin']);
$franjas = [];

while ($inicio < $fin) {
    $franjas[] = date('H:i', $inicio);
    $inicio += 30 * 60;
}

// Obtener citas
if ($idBarbero !== 0) {
    $citas = $citaModel->obtenerHorasReservadasBarbero($idBarbero, $fecha);
} else {
    $citas = $citaModel->obtenerHorasReservadas($idBarberia, $fecha);
}

$ocupadas = array_column($citas, 'hora');

// Filtrar
$disponibles = array_values(array_diff($franjas, $ocupadas));


echo json_encode($disponibles);
