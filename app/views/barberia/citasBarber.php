<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$usuario = $_SESSION['usuario'] ?? null;

if (!$usuario || $usuario['rol'] !== 'barberia') {
    echo "Acceso denegado.";
    exit;
}

require_once '../../../app/models/Cita.php';

use App\Models\Cita;


$fecha = $_GET['fecha'] ?? date('Y-m-d');
$barberoId = $_GET['barbero'] ?? null;

$citaModel = new Cita();

$idBarberia = $citaModel->obtenerIdBarberiaPorUsuario($usuario['id']);
$citas = $citaModel->obtenerCitasPorBarberia($idBarberia, $fecha, $barberoId);
$barberos = $citaModel->obtenerBarberosPorBarberia($idBarberia);

?>
<!DOCTYPE html>
<html lang="es">

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
    <main class="contenedor-citas-barberia">
        <div id="appCita">
            <h2 style="text-align: center;">Citas del día <?= date('d/m/Y', strtotime($fecha)) ?></h2>

            <form method="GET" class="filtros-citas-barberia">
                <label for="fecha">Fecha:</label>
                <input type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($fecha) ?>">

                <label for="barbero">Barbero:</label>
                <select name="barbero" id="barbero">
                    <option value="">Todos</option>
                    <?php foreach ($barberos as $barbero): ?>
                        <option value="<?= $barbero['id'] ?>" <?= $barberoId == $barbero['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($barbero['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="boton-estandar">Filtrar</button>
            </form>

            <div class="lista-citas">
                <?php if (empty($citas)): ?>
                    <p style="text-align: center;">No hay citas para esta fecha.</p>
                <?php else: ?>
                    <?php foreach ($citas as $cita): ?>
                        <div class="cita-item <?= $cita['estado'] === 'pendiente' ? 'cita-pendiente' : 'cita-realizada' ?>">
                            <p><strong>Cliente:</strong> <?= htmlspecialchars($cita['cliente']) ?></p>
                            <p><strong>Servicio:</strong> <?= htmlspecialchars($cita['servicio']) ?></p>
                            <p><strong>Barbero:</strong> <?= $cita['barbero'] ?? 'Cualquiera' ?></p>
                            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($cita['fecha_hora'])) ?></p>
                            <p><strong>Hora:</strong> <?= date('H:i', strtotime($cita['fecha_hora'])) ?></p>
                            <p><strong>Método de pago:</strong> <?= htmlspecialchars($cita['metodo_pago']) ?></p>
                            <div class="botones-citas">
                                <button @click="abrirModal('realizada', <?= $cita['id'] ?>)" class="boton-estandar">Realizada</button>
                                <button @click="abrirModal('cancelar', <?= $cita['id'] ?>)" class="boton-cerrar">Cancelar</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

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