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
    <title>Perfil</title>
    <link rel="icon" href="../../../assets/src/logoGROOMY-fondosin.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>

<body>
    <header class="header-inicio">
        <nav>
            <img src="../../../assets/src/logoGROOMY-fondoNegro.png" alt="Logo GROOMY">
            <ul>
                <li>
                    <a href="citas.php" class="boton-estandar"><i class="fa fa-calendar"></i>Citas</a>
                </li>
                <li>
                    <a href="editarPerfil.php" class="boton-estandar"><i class="fa fa-user-edit"></i>Editar</a>
                </li>
                <li>
                    <a href="../../../public/index.php" class="boton-estandar"><i class="fa fa-arrow-left"></i>Volver atrás</a>
                </li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="perfil">
            <h2>DATOS DEL USUARIO</h2>
            <section class="perfil-datos">
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                <?php if (!empty($usuario['imagen'])): ?>
                    <p><strong>Imagen:</strong><br>
                        <img src="<?= htmlspecialchars($usuario['imagen']); ?>" alt="Imagen de perfil" width="150">
                    </p>
                <?php else: ?>
                    <p><strong>Imagen:</strong><img src="../../../assets/src/sinImagen.png"></p>
                <?php endif; ?>
                
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