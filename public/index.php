<?php
session_start();

$mostrarMensajeInicio = false;

if (isset($_SESSION['mostrar_mensaje_inicio']) && $_SESSION['mostrar_mensaje_inicio']) {
    $mostrarMensajeInicio = true;
    unset($_SESSION['mostrar_mensaje_inicio']);
}
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
    <link rel="icon" type="image/png" href="/dashboard/groomy/assets/src/logoGROOMY-fondosin.png">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
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
    <?php if ($mostrarMensajeInicio): ?>
        <script>
            Swal.fire({
                title: '¡Bienvenido a Groomy!',
                html: `<div style="font-family: 'Montserrat', sans-serif; text-align: center;">
                    <p style="font-size: 15px; margin-bottom: 15px;">
                        Para que los clientes puedan reservar contigo, necesitas completar tu perfil.
                    </p>
                    <a href="../app/views/barberia/editarPerfilBarber.php"
                        style="
                        display: inline-block;
                        background-color: #1abc9c;
                        color: white;
                        padding: 10px 20px;
                        border-radius: 8px;
                        font-weight: bold;
                        text-decoration: none;
                        font-family: 'Montserrat', sans-serif;
                        border: 2px solid rgb(3, 52, 42); 
                        ">
                        Completar perfil
                    </a>
                    </div>`,
                icon: 'info',
                showConfirmButton: false,
                background: '#f5f5f5',
                color: '#333',
                customClass: {
                    title: 'swal-title-custom'
                }
            });
        </script>

    <?php endif; ?>

</body>

</html>