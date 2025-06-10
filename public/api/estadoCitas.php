<?php
require_once __DIR__ . '/../../app/models/Cita.php';
require_once __DIR__ . '/../../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
use App\Models\Cita;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents("php://input"), true);
$idCita = $data['id_cita'] ?? null;
$nuevoEstado = $data['estado'] ?? null;

if (!$idCita || !$nuevoEstado) {
    echo json_encode(['message' => 'Faltan datos (id_cita o estado).']);
    exit;
}

$modelo = new Cita();
$ok = $modelo->actualizarEstado($idCita, $nuevoEstado);

if ($ok && $nuevoEstado === 'cancelada') {
    $infoCita = $modelo->obtenerInfoCita($idCita);

    if ($infoCita && !empty($infoCita['email'])) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER'];
            $mail->Password   = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
            $mail->Port       = $_ENV['SMTP_PORT'];

            $mail->setFrom($_ENV['SMTP_USER'], 'Groomy');
            $mail->addAddress($infoCita['email'], $infoCita['nombre_usuario']);

            $mail->isHTML(true);
            $mail->Subject = 'Tu cita ha sido cancelada';
            $mail->Body = "
                <p>Hola <strong>{$infoCita['nombre_usuario']}</strong>,</p>
                <p>Tu cita programa para el <strong>" . date('d/m/Y H:i', strtotime($infoCita['fecha_hora'])) . "</strong> ha sido <span style='color:red;'>cancelada</span> por la barbería.</p>
                <p>Si lo deseas, puedes contactar con ellos directamente a través de GROOMY.</p>
                <p>Y cuando quieras, puedes reservar una nueva cita desde la app cuando quieras 😊</p>
                <p>Un saludo, <br><strong>El equipo de Groomy</strong></p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Error al enviar correo: {$mail->ErrorInfo}");
        }
    } else {
        error_log("No se encontró el correo del usuario para la cita ID $idCita");
    }
}

echo json_encode([
    'message' => $ok ? "Cita actualizada a '$nuevoEstado'." : 'Error al actualizar la cita.'
]);
