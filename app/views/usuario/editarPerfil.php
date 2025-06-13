<?php
session_start();

$usuario = $_SESSION['usuario'] ?? null;

if (!$usuario) {
    header("Location: ../../../public/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="icon" href="../../../assets/src/logoGROOMY-fondosin.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <link rel="stylesheet" href="../../../assets/css/responsives.css">
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
    <main>
        <section class="formulario-container">
            <h1>Editar Perfil</h1>
            <form action="/dashboard/groomy/public/index.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="editarPerfil">

                <label for="nombre">Nombre: </label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>

                <label for="email">Email: </label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

                <label for="img">Imagen actual:</label><br>
                <?php if (!empty($usuario['imagen'])): ?>
                    <img src="../../../assets/src/users/<?= htmlspecialchars($usuario['imagen']) ?>" alt="Imagen actual" width="100">
                <?php else: ?>
                    <img src="../../../assets/src/sinImagen.png" alt="Sin imagen" width="100"><br>
                <?php endif; ?>

                <label for="img">Subir nueva imagen:</label>
                <input type="file" id="img" name="img" accept="image/*" onchange="console.log(this.files)">

                <input type="submit" value="Actualizar Perfil" class="boton-estandar">
            </form>
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