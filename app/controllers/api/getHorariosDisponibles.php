<?php
require_once __DIR__ . '/../../models/Barberia.php';
require_once __DIR__ . '/../../models/Cita.php';

use App\Models\Barberia;
use App\Models\Cita;

header('Content-Type: application/json');

$idBarberia = $_GET['idBarberia'] ?? null;
$fecha = $_GET['fecha'] ?? null;

if (!$idBarberia || !$fecha) {
    echo json_encode([]);
    exit;
}

$barberiaModel = new Barberia();
$citaModel = new Cita();

// Obtener día de la semana en minúsculas
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


// Obtener horario de ese día
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

// Generar horas disponibles
$inicio = strtotime($horarioDia['inicio']);
$fin = strtotime($horarioDia['fin']);
$franjas = [];

while ($inicio < $fin) {
    $franjas[] = date('H:i', $inicio);
    $inicio += 30 * 60; // 30 minutos
}

// Obtener horas ya reservadas
$citas = $citaModel->obtenerHorasReservadas($idBarberia, $fecha);
$ocupadas = array_column($citas, 'hora');

// Filtrar
$disponibles = array_values(array_diff($franjas, $ocupadas));

echo json_encode($disponibles);
