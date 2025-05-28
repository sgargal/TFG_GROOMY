<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
var_dump($_SESSION); 
$usuario = $_SESSION['usuario'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GROOMY</title>
    <link rel="icon" href="../../../src/logoGROOMY-fondosin.png">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
</head>

<body>
    <div id="app">
        <header class="header-inicio">
            <nav>
                <img src="../assets/src/logoGROOMY-fondoNegro.png" alt="Logo GROOMY">
                <ul>
                    <?php if ($usuario): ?>
                        <li><button>Perfil</button></li>
                        <li><button>Cerrar Sesión</button></li>
                    <?php else: ?>
                        <li><button @click="mostrarSign = true">REGÍSTRATE</button></li>
                        <li><button @click="mostrarLogin = true">INICIA SESIÓN</button></li>
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
                        <button type="submit">REGISTRAR</button>
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
                        <button type="submit">INICIAR SESIÓN</button>
                    </form>
                    <button @click="cerrarLogin">Cerrar</button>
                </section>
            </section>
        </main>
    </div>
    <script src="js/loginApp.js" defer></script>
</body>
</html>
