<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../models/Barberia.php';
require_once __DIR__ . '/../../models/Cita.php';

use App\Models\Barberia;
use App\Models\Cita;

$usuario = $_SESSION['usuario'] ?? null;
if (!$usuario) {
    header('Location: ../../../public/index.php');
    exit;
}

$idBarberia = $_GET['id'] ?? null;
if (!$idBarberia) {
    echo "No se ha proporcionado un ID de barbería.";
    exit;
}

$barberiaModel = new Barberia();
$empleados = $barberiaModel->obtenerEmpleados($idBarberia);
$servicios = $barberiaModel->obtenerServicios($idBarberia);
$horarios = $barberiaModel->obtenerHorarios($idBarberia);

$servicioSeleccionado = isset($_GET['servicio']) ? (int)$_GET['servicio'] : null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva</title>
    <link rel="icon" href="../../../assets/src/logoGROOMY-fondosin.png">
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
</head>

<body>
    <header class="header-inicio">
        <nav>
            <img src="../../../assets/src/logoGROOMY-fondoNegro.png" alt="Logo GROOMY" height="150" width="150">
            <ul>
                <li><a href="../usuario/citas.php?estado=pendiente" class="boton-estandar"><i class="fas fa-calendar"></i>Citas</a></li>
                <li>
                    <a href="../../../public/index.php" class="boton-estandar"><i class="fa fa-arrow-left"></i>Abandonar proceso</a>
                </li>
            </ul>
        </nav>
    </header>
    <main class="contenedor-reserva">
        <div id="app-reserva">
            <form ref="formulario" action="../../../public/api/crearCita.php" method="POST" class="formulario-reserva">
                <div class="reserva-layout">
                    <!-- IZQUIERDA -->
                    <div class="columna-izquierda">
                        <section class="reserva-bloque">
                            <h3>Selecciona una fecha:</h3>
                            <div id="calendario" class="calendario-grid"></div>
                            <input type="hidden" name="fecha" id="fecha-seleccionada">
                        </section>

                        <section class="reserva-bloque" id="bloque-horas" style="display: none;">
                            <h3>Horas disponibles:</h3>
                            <div id="horas-disponibles" class="botones-horario"></div>
                            <input type="hidden" name="hora" :value="hora">
                        </section>
                    </div>

                    <!-- DERECHA -->
                    <div class="columna-derecha">
                        <input type="hidden" name="id_usuario" value="<?= $usuario['id'] ?>">
                        <input type="hidden" name="id_barberia" value="<?= $idBarberia ?>">

                        <section class="reserva-bloque">
                            <h3>Empleado:</h3>
                            <div class="grupo-empleados">
                                <!-- Empleados reales -->
                                <?php foreach ($empleados as $empleado): ?>
                                    <input
                                        type="radio"
                                        name="id_barbero"
                                        value="<?= $empleado['id'] ?>"
                                        id="barbero_<?= $empleado['id'] ?>"
                                        v-model="barberoSeleccionado"
                                        hidden>
                                    <label for="barbero_<?= $empleado['id'] ?>" class="empleado-item">
                                        <img class="avatar-empleado"
                                            src="../../../<?= htmlspecialchars($empleado['imagen']) ?>"
                                            onerror="this.src='../../../assets/src/sinImagen.png';"
                                            alt="<?= htmlspecialchars($empleado['nombre']) ?>">
                                        <span><?= htmlspecialchars($empleado['nombre']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </section>

                        <section class="reserva-bloque">
                            <h3>Servicio:</h3>
                            <select name="id_servicio" v-model="servicioSeleccionado" required>
                                <?php foreach ($servicios as $servicio): ?>
                                    <option value="<?= $servicio['id'] ?>"><?= htmlspecialchars($servicio['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </section>

                        <section class="reserva-bloque">
                            <h3>Seleccione el método de pago:</h3>
                            <div class="grupo-metodos">
                                <input type="radio" name="metodo_pago" value="paypal" id="pago_paypal" v-model="metodoPago" hidden required>
                                <label for="pago_paypal" class="metodo-item">
                                    <i class="fab fa-paypal"></i>
                                    <span>PAYPAL</span>
                                </label>

                                <input type="radio" name="metodo_pago" value="efectivo" id="pago_efectivo" v-model="metodoPago" hidden>
                                <label for="pago_efectivo" class="metodo-item">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>EFECTIVO</span>
                                </label>
                            </div>
                        </section>

                        <button type="button" class="boton-Reservar" @click="abrirResumen">Confirmar reserva</button>
                    </div>

                </div>
            </form>
            <section v-if="mostrarResumen" class="modal-overlay">
                <section class="modal-content">
                    <template v-if="estadoReserva === 'resumen'">
                        <h2>Resumen de tu cita</h2>
                        <ul>
                            <li><strong>Fecha:</strong> {{ fecha }}</li>
                            <li><strong>Hora:</strong> {{ hora }}</li>
                            <li><strong>Servicio:</strong> {{ servicioNombre }}</li>
                            <li><strong>Barbero:</strong> {{ barberoNombre }}</li>
                            <li><strong>Método de pago:</strong> {{ metodoPago }}</li>
                        </ul>
                        <button @click="enviarFormulario">Sí, confirmar</button>
                        <button @click="mostrarResumen = false">Cancelar</button>
                    </template>

                    <template v-else-if="estadoReserva === 'exito'">
                        <h2 class="success">¡Reserva realizada con éxito!</h2>
                    </template>

                    <template v-else-if="estadoReserva === 'error'">
                        <h2 class="error"> Ha ocurrido un error al guardar la reserva.</h2>
                        <button @click="mostrarResumen = false">Cerrar</button>
                    </template>
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
    <script src="../../../assets/js/reservarCalendario.js"></script>
    <script src="../../../public/js/reservaResumen.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.vm) {
                vm.inicializar({
                    usuarioId: <?= json_encode($usuario['id']) ?>,
                    barberiaId: <?= json_encode($idBarberia) ?>,
                    servicios: <?= json_encode($servicios) ?>,
                    barberos: <?= json_encode($empleados) ?>,
                    servicioSeleccionado: <?= json_encode($servicioSeleccionado) ?>
                });
            } else {
                console.error("vm no está definido");
            }
        });
    </script>
</body>

</html>