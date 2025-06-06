<?php
require_once __DIR__ . '/../../models/Barberia.php';

use App\Models\Barberia;

$barberiaModel = new Barberia();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "No se ha proporcionado un ID de barbería.";
    exit;
}

$barberia = $barberiaModel->obtenerPorId($id);
if (!$barberia) {
    echo "Barbería no encontrada.";
    exit;
}
$servicios = $barberiaModel->obtenerServicios($barberia['id']);
$empleados = $barberiaModel->obtenerEmpleados($barberia['id']);
$redes = $barberiaModel->obtenerRedes($barberia['id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($barberia['nombre']) ?> | INFO</title>
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
                    <a href="../../../public/index.php" class="boton-estandar"><i class="fa fa-arrow-left"></i>Volver atrás</a>
                </li>
            </ul>
        </nav>
    </header>
    <main id="appDetalleBarberia">
        <section class="detalle-barberia">
            <section class="header-barberia">
                <img src="../../../assets/src/users/<?= htmlspecialchars($barberia['imagen']) ?>" alt="Logo de la barbería" class="logo-barberia">
                <h1><?= htmlspecialchars($barberia['nombre']) ?></h1>
            </section>
            <section class="tabs-barberia">
                <button class="tab"
                    :class="{ active: vistaActiva === 'servicios' }"
                    @click="vistaActiva = 'servicios'">SERVICIOS</button>
                <button class="tab"
                    :class="{ active: vistaActiva === 'informacion' }"
                    @click="vistaActiva = 'informacion'">INFORMACIÓN</button>
            </section>

            <!-- contenido de ambas secciones -->
            <!-- seccion servicios -->
            <section id="panel-servicios" class="panel-tab" v-if="vistaActiva === 'servicios'">
                <ul class="lista-servicios">
                    <?php foreach ($servicios as $servicio): ?>
                        <li class="servicio-item">
                            <span class="nombre-servicio"><?= htmlspecialchars($servicio['nombre']) ?></span>
                            <span class="precio-servicio"><?= htmlspecialchars($servicio['precio']) ?> €</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

                <!-- seccion informacion -->
                <section id="panel-info" class="panel-tab" v-if="vistaActiva === 'informacion'">
                    <article class="mapa-barberia">
                        <iframe
                            src="https://www.google.com/maps?q=<?= urlencode($barberia['direccion']) ?>&output=embed"
                            width="100%" height="300" style="border:0;" allowfullscreen loading="lazy">
                        </iframe>
                    </article>

                    <h3>CONOCE A NUESTROS EMPLEADOS</h3>
                    <ul class="empleados">
                        <?php foreach ($empleados as $empleado): ?>
                            <li class="empleado">
                                <img src="../../../<?= htmlspecialchars($empleado['imagen']) ?>" alt="Foto de barbero">
                                <p><?= htmlspecialchars($empleado['nombre']) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <h3>SOBRE NOSOTROS</h3>
                    <p class="descripcion-barberia">
                        <?= htmlspecialchars($barberia['informacion'] ?? '') ?>
                    </p>

                    <h3>REDES SOCIALES</h3>
                    <section class="redes-sociales">
                        <?php foreach ($redes as $red): ?>
                            <?php
                            $nombre = strtolower($red['tipo']);
                            $icono = '';

                            if ($nombre === 'instagram') {
                                $icono = 'logoInsta.png';
                            } elseif ($nombre === 'facebook') {
                                $icono = 'logoFacebook.png';
                            } elseif ($nombre === 'x') {
                                $icono = 'logoX.png';
                            }
                            ?>
                            <?php if ($icono): ?>
                                <a href="<?= htmlspecialchars($red['url']) ?>" target="_blank" class="icono-red">
                                    <img src="../../../assets/src/<?= $icono ?>" alt="<?= htmlspecialchars($red['tipo']) ?>">
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </section>
                </section>

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
    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    vistaActiva: 'servicios' // por defecto
                }
            }
        }).mount('#appDetalleBarberia');
    </script>

</body>

</html>