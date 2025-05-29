<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$usuario = $_SESSION['usuario'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GROOMY</title>
    <link rel="icon" href="../../../assets/src/logoGROOMY-fondosin.png">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
    <script>
        window.usuarioPHP = <?= json_encode($_SESSION['usuario'] ?? null) ?>;
    </script>
</head>

<body>
    <div id="app">
        <header class="header-inicio">
            <nav>
                <img src="../assets/src/logoGROOMY-fondoNegro.png" alt="Logo GROOMY">
                <ul>
                    <?php if ($usuario): ?>
                        <li>
                            <button @click="mostrarPerfil = true" class="boton-estandar">
                                <?php if (!empty($_SESSION['usuario']['imagen'])): ?>
                                    <img src="<?= htmlspecialchars($_SESSION['usuario']['imagen']) ?>">
                                <?php else: ?>
                                    <i class="fa fa-user"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?>
                            </button>
                        </li>
                        <li><a href="../app/views/usuario/citas.php" class="boton-perfil"><i class="fa fa-calendar"></i>Citas</a></li>
                        <li><button class="boton-cerrar">Cerrar Sesión</button></li>
                    <?php else: ?>
                        <li><button @click="mostrarSign = true" class="boton-estandar">REGÍSTRATE</button></li>
                        <li><button @click="mostrarLogin = true" class="boton-estandar">INICIA SESIÓN</button></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>
        <main>
            <section v-if="mostrarSign" class="modal-overlay">
                <section class="modal-content">
                    <h2>Registrar</h2>
                    <form @submit.prevent="registrar">
                        <label for="nombreRegistro">Nombre: </label>
                        <input type="text" id="nombreRegistro" v-model="registro.nombre" required>
                        <label for="emailRegistro">Email:</label>
                        <input type="email" id="emailRegistro" v-model="registro.email" required>
                        <label for="passwordRegistro">Contraseña:</label>
                        <input type="password" id="passwordRegistro" v-model="registro.password" required>
                        <button type="submit" class="boton-estandar">REGISTRAR</button>
                    </form>
                    <button @click="cerrarRegistro">Cerrar</button>
                </section>
            </section>
            <section v-if="mostrarLogin" class="modal-overlay">
                <section class="modal-content">
                    <h2>Iniciar Sesión</h2>
                    <form @submit.prevent="login">
                        <label for="emailLogin">Email:</label>
                        <input type="email" id="emailLogin" v-model="datosLogin.email" required>
                        <label for="passwordLogin">Contraseña:</label>
                        <input type="password" id="passwordLogin" v-model="datosLogin.password" required>
                        <button type="submit" class="boton-estandar">INICIAR SESIÓN</button>
                    </form>
                    <button @click="cerrarLogin">Cerrar</button>
                </section>
            </section>
            <section v-if="mostrarPerfil" class="modal-overlay-perfil">
                <section class="modal-content-perfil">
                    <h2>Perfil de Usuario</h2>
                    <p><strong>Nombre:</strong> {{ usuario.nombre }}</p>
                    <p><strong>Email:</strong> {{ usuario.email }}</p>
                    <p v-if="usuario.imagen"><strong>Imagen:</strong><br>
                        <img :src="usuario.imagen" alt="Imagen de perfil" width="150">
                    </p>
                    <p v-else><strong>Imagen:</strong><br><br><img src="../assets/src/sinImagen.png"></p>
                    <button type="submit" class="boton-estandar" onclick="window.location.href='../app/views/usuario/perfil.php'"><i class="fa fa-user-edit"></i> EDITAR</button>
                    <button @click="cerrarPerfil">Cerrar</button>
                </section>
            </section>
        </main>
    </div>
    <script src="js/loginApp.js" defer></script>
</body>

</html>