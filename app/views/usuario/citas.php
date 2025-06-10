<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../models/Cita.php';

use App\Models\Cita;

$usuario = $_SESSION['usuario'] ?? null;

if (!$usuario) {
    echo "Debes iniciar sesión para ver tus citas.";
    exit;
}

$citaModel = new Cita();
$estado = $_GET['estado'] ?? 'pendientes';
$citas = $citaModel->obtenerCitasPorUsuario($usuario['id'], $estado);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas</title>
    <link rel="icon" href="../../../assets/src/logoGROOMY-fondosin.png">
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
      <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
</head>

<body>
    <header class="header-inicio">
        <nav>
            <img src="../../../assets/src/logoGROOMY-fondoNegro.png" alt="Logo GROOMY" height="150" width="150">
            <ul>
                <li>
                    <a href="../../../public/index.php" class="boton-estandar"><i class="fa fa-home"></i>Volver a inicio</a>
                </li>
            </ul>
        </nav>
    </header>
    <main>
        <div id="appCita">
            <section class="citas-usuario">
                <h2>Tus citas</h2>
                <div class="botones-citas" style="margin-bottom: 20px;">
                    <a href="?estado=pendiente" class="boton-estado <?= $estado === 'pendiente' ? 'activo' : '' ?>">Pendientes</a>
                    <a href="?estado=realizada" class="boton-estado <?= $estado === 'realizada' ? 'activo' : '' ?>">Realizadas</a>
                </div>
                <?php if (empty($citas)): ?>
                    <p>No tienes ninguna cita pendiente.</p>
                <?php else: ?>
                    <div class="lista-citas">
                        <?php foreach ($citas as $cita): ?>
                            <div class="cita-item <?= $cita['estado'] === 'pendiente' ? 'cita-pendiente' : 'cita-realizada' ?>">
                                <p><strong>Barbería:</strong> <?= htmlspecialchars($cita['barberia']) ?></p>
                                <p><strong>Servicio:</strong> <?= htmlspecialchars($cita['servicio']) ?></p>
                                <p><strong>Barbero:</strong> <?= $cita['barbero'] ?? 'Cualquiera' ?></p>
                                <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($cita['fecha_hora'])) ?></p>
                                <p><strong>Hora:</strong> <?= date('H:i', strtotime($cita['fecha_hora'])) ?></p>
                                <p><strong>Método de pago:</strong> <?= htmlspecialchars($cita['metodo_pago']) ?></p>
                                <?php if ($cita['estado'] === 'pendiente'): ?>
                                    <div class="botones-citas">
                                        <button @click="abrirModal('cancelada', <?= $cita['id'] ?>)" class="boton-cerrar">Cancelar</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            <section v-if="mostrarModal" class="modal-overlay">
                <section class="modal-content">
                    <p>¿Estás seguro de que quieres {{ accion === 'realizada' ? 'marcar como realizada' : 'cancelar'}} esta cita?</p>
                    <button @click="confirmarAccion" class="boton-confirmar">Sí</button>
                    <button @click="mostrarModal = false" class="boton-cerrar">No</button>
                </section>
            </section>
        </div>
    </main>
    <footer class="footer">
        <nav>
            <ul class="footer-links">
                <li><a href="../etc/preguntas.php">PREGUNTAS FRECUENTES</a></li>
                <li><a href="../etc/contactanos.php">CONTÁCTANOS</a></li>
            </ul>
            <ul class="footer-redes">
                <li><a href="https://www.instagram.com/" target="_blank"><img src="../../../assets/src/logoInsta.png"></a></li>
                <li><a href="https://www.facebook.com/" target="_blank"><img src="../../../assets/src/logoFacebook.png"></a></li>
                <li><a href="https://x.com/" target="_blank"><img src="../../../assets/src/logoX.png"></a></li>
            </ul>
        </nav>
    </footer>
    <script src="../../../public/js/abrirModal.js" defer></script>
</body>

</html>