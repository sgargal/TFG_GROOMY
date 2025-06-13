<?php
// Arranca sesión si hace falta
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../../app/models/Cita.php';
require_once __DIR__ . '/../../app/models/usuario.php';

use App\Models\Cita;
use App\Models\Usuario;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

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

// Si no hay barbero concreto
if ($idBarbero == 0) {
    $idBarbero = null;
}

// Guardar en BD
$citaModel = new Cita();
try {
    $ok = $citaModel->crearCita(
        $idUsuario,
        $idBarberia,
        $idBarbero,
        $idServicio,
        $metodoPago,
        $fechaHora
    );
} catch (\PDOException $e) {
    error_log("ERROR SQL crearCita: " . $e->getMessage());
    $ok = false;
}


if ($ok) {
    // —– ENVÍO DE CORREO —–
    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->obtenerUsuarioPorId($idUsuario);
    $mail = new PHPMailer(true);
    try {
        // Config SMTP
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->Port       = $_ENV['SMTP_PORT'];;
        $mail->CharSet    = 'UTF-8';

        // Remitente y destinatario
        $mail->setFrom('soportegroomy@gmail.com', 'Groomy');
        $mail->addAddress($usuario['email'], $usuario['nombre']);

        // Contenido
        $mail->isHTML(true);
        // Obtener nombre de servicio
        $infoServicio = $citaModel->obtenerInfoServicio($idServicio);
        $nombre_servicio = $infoServicio['nombre'] ?? 'Servicio Desconocido';

        // Obtener nombre del barbero
        $nombre_barbero = 'No especificado';
        if ($idBarbero) {
            $infoBarbero = $citaModel->obtenerInfoBarbero($idBarbero);
            $nombre_barbero = $infoBarbero['nombre'] ?? 'No especificado';
        }
        // Calcular horas para Google Calendar
        $start = date('Ymd\THis', strtotime("$fecha $hora"));
        $end = date('Ymd\THis', strtotime("$fecha $hora +30 minutes")); // ajusta duración si hace falta

        $googleCalendarUrl = "https://www.google.com/calendar/render?action=TEMPLATE" .
            "&text=" . urlencode("Cita - $nombre_servicio") .
            "&dates={$start}/{$end}" .
            "&details=" . urlencode("Tu cita con $nombre_barbero para $nombre_servicio") .
            "&location=" . urlencode("Te esperamos en tu barbería favorita. ¡Gracias por usar Groomy!") .
            "&ctz=Europe/Madrid&sf=true&output=xml";

        $mail->Subject = 'Confirmación de tu reserva';
        $mail->Body    = "
            <h3>Hola, {$usuario['nombre']}</h3>
            <p>Resumen de tu reserva:</p>
            <ul>
              <li>Fecha: {$fecha}</li>
              <li>Hora: {$hora}</li>
              <li>Servicio: {$nombre_servicio}</li>
              <li>Barbero: {$nombre_barbero}</li>
            </ul>
            <p>
                <a href='$googleCalendarUrl' style='
                    display:inline-block;
                    padding:10px 20px;
                    background-color:#000;
                    color:#fff;
                    text-decoration:none;
                    border-radius:5px;
                    font-weight:bold;
                '>Añadir a Google Calendar</a>
            </p>
            <h4>¡Muchas gracias por confiar en GROOMY para reservar tu cita! 😊</h4>
        ";
        $mail->AltBody = "Reserva: Fecha {$fecha}, Hora {$hora}, Servicio {$nombre_servicio}, Barbero {$nombre_barbero}";

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Reserva OK y email enviado.']);
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        echo json_encode(['success' => true, 'message' => 'Reserva OK, fallo al enviar email.']);
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar cita.']);
}

