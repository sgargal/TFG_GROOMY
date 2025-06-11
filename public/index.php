<?php
session_start();
require_once '../app/controllers/UsuarioController.php';
require_once '../app/controllers/BarberiaController.php';


use App\Controllers\UsuarioController;
use App\Controllers\BarberiaController;

$controller = new UsuarioController();
$barberiaController = new BarberiaController();
$barberias = $barberiaController->listarBarberias();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GROOMY</title>
    <link rel="icon" href="../assets/src/logoGROOMY-fondosin.png">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <header>
        <?php
        include '../app/views/layout/header.php';
        ?>
    </header>
    <main>
        <?php if (!empty($_SESSION['mensaje'])): ?>
            <div id="mensajeFlash" class="mensaje-exito-update">
                <?= htmlspecialchars($_SESSION['mensaje']) ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>
        <section class="contenedor-barberias">
            <?php foreach ($barberias as $barberia): ?>
                <div class="tarjeta-barberia">
                    <img src="../assets/src/users/<?= htmlspecialchars($barberia['imagen']) ?>" alt="Logo" class="imagen-barberia">
                    <h3><?= htmlspecialchars($barberia['nombre']) ?></h3>
                    <p><i class="fa fa-map-marker-alt"></i> <?= htmlspecialchars($barberia['direccion'] ?? 'Sin dirección') ?></p>
                    <a href="../app/views/barberia/detalleBarberia.php?id=<?= $barberia['id'] ?>" class="boton-estandar">Ver más</a>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
    <footer>
        <?php
        include '../app/views/layout/footer.php';
        ?>
    </footer>

    <script>
        // Esperar 2 segundos y luego ocultar el mensaje
        setTimeout(() => {
            const mensaje = document.getElementById('mensajeFlash');
            if (mensaje) {
                mensaje.style.transition = "opacity 0.5s ease";
                mensaje.style.opacity = 0;
                setTimeout(() => mensaje.remove(), 500); // eliminar del DOM tras fundido
            }
        }, 2000);
    </script>
</body>

</html>