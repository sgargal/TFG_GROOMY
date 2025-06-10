<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Cita.php';
use App\Models\Cita;

class CitaController
{
    public function crearCita()
    {
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

        if ($idBarbero == 0) {
            $idBarbero = null;
        }
        echo "FECHA: $fecha\n";
        echo "HORA: $hora\n";
        echo "FECHA-HORA: $fechaHora\n";
        exit;

        // $citaModel = new Cita();
        // $ok = $citaModel->crearCita($idUsuario, $idBarberia, $idBarbero, $idServicio, $metodoPago, $fechaHora);


        // if ($ok) {
        //     header('Location: ../views/barberia/citas.php?reserva=ok');
        //     exit;
        // } else {
        //     echo "Error al guardar la cita.";
        // }
    }

    public function verCitasUsuario() {
        $usuario = $_SESSION['usuario'] ?? null;

        if(!$usuario) {
            echo "Debes iniciar sesión para ver tus citas";
            return;
        }

        $modelo = new Cita();
        $citas = $modelo->obtenerCitasPorUsuario($usuario['id']);

        require_once __DIR__ . '/../views/usuario/citas.php';
    }

    public function cancelarCita() {
    $data = json_decode(file_get_contents("php://input"), true);
    $idCita = $data['id_cita'] ?? null;

    if (!$idCita) {
        echo json_encode(['message' => 'ID de cita no proporcionado']);
        return;
    }

    $modelo = new Cita();
    $ok = $modelo->actualizarEstado($idCita, 'cancelada');

    echo json_encode([
        'message' => $ok ? 'Cita cancelada con éxito.' : 'Error al cancelar la cita.'
    ]);
}

public function marcarComoRealizada() {
    $data = json_decode(file_get_contents("php://input"), true);
    $idCita = $data['id_cita'] ?? null;

    if (!$idCita) {
        echo json_encode(['message' => 'ID de cita no proporcionado']);
        return;
    }

    $modelo = new Cita();
    $ok = $modelo->actualizarEstado($idCita, 'realizada');

    echo json_encode([
        'message' => $ok ? 'Cita marcada como realizada.' : 'Error al actualizar la cita.'
    ]);
}

}

?>