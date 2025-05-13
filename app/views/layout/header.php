<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GROOMY</title>
    <link rel="icon" href="../../../src/logoGROOMY-fondosin.png">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    <script src="js/loginApp.js" defer></script>
</head>

<body>
    <div id="app">
        <header class="header-inicio">
            <nav>
                <img src="../src/logoGROOMY-fondoNegro.png" alt="Logo GROOMY">
                <ul>
                    <li><a href="#" @click="mostrarSign = true">REGÍSTRATE</a></li>
                    <li><a href="#" @click="mostrarLogin = true">INICIA SESIÓN</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <section v-if="mostrarSign" class="modal-overlay">
                <section class="modal-content">
                    <h2>Registrar</h2>
                    <form>
                        <label for="nombre">Nombre: </label>
                        <input type="text" id="nombre" required>
                        <label for="email">Email:</label>
                        <input type="email" id="email" required>
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" required>
                        <button type="submit">Registrar</button>
                    </form>
                    <button @click="cerrarRegistro">Cerrar</button>
                </section>
            </section>
            <section v-if="mostrarLogin" class="modal-overlay">
                <section class="modal-content">
                    <h2>Iniciar Sesión</h2>
                    <form>
                        <label for="email">Email:</label>
                        <input type="email" id="email" required>
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" required>
                        <button type="submit">Entrar</button>
                    </form>
                    <button @click="cerrarLogin">Cerrar</button>
                </section>
            </section>
        </main>
    </div>
</body>

</html>