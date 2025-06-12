<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin</title>
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
    <main id="adminApp">
        <section class="botones-admin">
            <h1>Panel de Administración</h1>
            <section class="grid-admin">
                <section class="bloque-admin">
                    <h3>Registrar Barberías</h3>
                    <button class="boton-estandar" @click="mostrarModalBarberia = true"><i class="fa fa-plus"></i> Añadir Barbería</button>
                </section>
                <section class="bloque-admin">
                    <h3>Ver cosas</h3>
                    <button class="boton-estandar"><i class="fa fa-plus"></i> Añadir Barbería</button>
                </section>
                <section class="bloque-admin">
                    <h3>Ver cosas</h3>
                    <button class="boton-estandar"><i class="fa fa-plus"></i> Añadir Barbería</button>
                </section>
                <section class="bloque-admin">
                    <h3>Ver cosas</h3>
                    <button class="boton-estandar"><i class="fa fa-plus"></i> Añadir Barbería</button>
                </section>
                <section class="bloque-admin">
                    <h3>Ver cosas</h3>
                    <button class="boton-estandar"><i class="fa fa-plus"></i> Añadir Barbería</button>
                </section>
            </section>
        </section>

        <section class="modal-overlay" v-if="mostrarModalBarberia">
            <section class="modal-content">
                <h2>Registrar Barbería</h2>
                <form @submit.prevent="registrarBarberia">
                    <label for="nombreBarberia">Nombre</label>
                    <input type="text" id="nombreBarberia" v-model="barberia.nombre" required>

                    <label for="emailBarberia">Email</label>
                    <input type="email" id="emailBarberia" v-model="barberia.email" required>

                    <label for="passwordBarberia">Contraseña generada</label>
                    <div class="campo-password">
                        <input type="text" id="passwordBarberia" v-model="barberia.password" readonly>
                        <button type="button" @click="copiarPassword" class="boton-copiar" title="Copiar">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>

                    <button type="button" class="boton-estandar" @click="generarPassword">Generar contraseña</button>

                    <div v-if="mensaje" :class="['mensaje-flash-admin', tipoMensaje]">
                        {{ mensaje }}
                    </div>

                    <button type="submit" class="boton-estandar"><i class="fa fa-plus"></i> Crear Barbería</button>
                </form>
                <button @click="cerrarModalBarberia">Cerrar</button>
            </section>
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
    <script src="../../../public/js/adminPanel.js" defer></script>
</body>

</html>