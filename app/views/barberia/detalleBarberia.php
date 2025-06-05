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
    <main class="detalle-barberia">
        <section class="header-barberia">
            <img src="../../../assets/src/users/<?= htmlspecialchars($barberia['imagen']) ?>" alt="Logo de la barbería" class="logo-barberia">
            <h1><?= htmlspecialchars($barberia['nombre']) ?></h1>
        </section>
        <section class="tabs-barberia">
            <button id="tab-servicios" class="tab active">SERVICIOS</button>
            <span class="separador"> | </span>
            <button id="tab-info" class="tab">INFORMACIÓN</button>
        </section>

        <!-- contenido de ambas secciones -->
        <!-- seccion servicios -->
        <section id="panel-servicios" class="panel-tab">
            <ul class="lista-servicios">
                <?php foreach ($servicios as $servicio): ?>
                    <li class="servicio-item">
                        <span class="nombre-servicio"><?= htmlspecialchars($servicio['nombre']) ?></span>
                        <span class="precio-servicio"><?= htmlspecialchars($servicio['precio']) ?> €</span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <h3>REDES SOCIALES</h3>
            <section class="redes-sociales">
                <?php foreach ($redes as $red): ?>
                    <a href="<?= htmlspecialchars($red['url']) ?>" target="_blank">
                        <?= htmlspecialchars($red['nombre_red_social']) ?>
                    </a>
                <?php endforeach; ?>
            </section>
        </section>

        <!-- seccion informacion -->
        <section id="panel-info" class="panel-tab oculto">
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
                        <img src="../../../assets/src/barberos/<?= htmlspecialchars($empleado['imagen']) ?>" alt="Foto de barbero">
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
                    <a href="<?= htmlspecialchars($red['url']) ?>" target="_blank">
                        <?= htmlspecialchars($red['nombre_red_social']) ?>
                    </a>
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
</body>

</html>