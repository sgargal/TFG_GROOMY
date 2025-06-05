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
</body>

</html>