<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario = $_SESSION['usuario'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="icon" href="../../../assets/src/logoGROOMY-fondosin.png">
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
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
    <main>
        <section class="formulario-barberia">
            <h1>Editar Perfil</h1>
            <form id="formBarberia">
                <label for="nombre">NOMBRE</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>

                <label for="email">EMAIL</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

                <label for="img">IMAGEN ACTUAL:</label><br>
                <?php if (!empty($usuario['imagen'])): ?>
                    <img src="../../../assets/src/users/<?= htmlspecialchars($usuario['imagen']) ?>" alt="Imagen actual" width="100">
                <?php else: ?>
                    <img src="../../../assets/src/sinImagen.png" alt="Sin imagen" width="100"><br>
                <?php endif; ?>

                <label for="img">SUBIR NUEVA IMAGEN:</label>
                <input type="file" id="img" name="img" accept="image/*" onchange="console.log(this.files)">

                <label for="password">NUEVA CONTRASEÑA (opcional):</label>
                <input type="password" id="password" name="password" placeholder="Nueva contraseña">

                <label for="direccion">DIRECCIÓN</label>
                <input type="text" id="direccion" name="direccion" value="<?= isset($usuario['direccion']) ? htmlspecialchars($usuario['direccion']) : '' ?>">

                <label for= "servicios">SERVICIOS + PRECIOS</label>
                <div v-for="(servicio, index) in servicios" :key="index" class="grupo-servicio">
                    <input type="text" v-model="servicio.nombre" placeholder="Nombre del servicio" required>
                    <input type="number" v-model="servicio.precio" placeholder="€" min="0" step="0.01" required>
                    <button type="button" class="boton-add" @click="agregarServicio"><i class="fa fa-plus"></i></button>
                </div>

                <label for="empleados">EMPLEADOS</label>
                <div v-for="(empleado, index) in empleados" :key="index" class="grupo-empleado">
                    <input type="text" v-model="empleado.nombre" placeholder="Nombre del empleado" required>

                    <label class="boton-subir">
                        <i class="fa fa-upload"></i> Subir Imagen
                        <input type="file" @change="cargarImagen($event, index)" accept="image/*" hidden>
                    </label>

                    <button type="button" class="boton-add" @click="agregarEmpleado"><i class="fa fa-plus"></i></button>
                </div>

                <label for="horarios">HORARIOS</label>
                <div v-for="(horario, index) in horarios" :key="index" class="grupo-horario">
                    <select v-model="horario.dia" required>
                        <option disabled value="">Selecciona un día</option>
                        <option>Lunes</option>
                        <option>Martes</option>
                        <option>Miércoles</option>
                        <option>Jueves</option>
                        <option>Viernes</option>
                        <option>Sábado</option>
                        <option>Domingo</option>

                    </select>
                    <input type="time" v-model="horario.inicio" required>
                    <span class="separador">-</span>
                    <input type="time" v-model="horario.fin" required>
                    <button type="button" class="boton-add" @click="agregarHorario"><i class="fa fa-plus"></i></button>
                </div>

                <label for="redes">REDES SOCIALES</label>
                <div v-for="(red,index) in redesSociales" :key="index" class="grupo-redes">
                    <select v-model="red.tipo" required>
                        <option disabled value="">Selecciona una red social</option>
                        <option>Facebook</option>
                        <option>Instagram</option>
                        <option>X</option>
                    </select>

                    <input type="text" v-model="red.url" placeholder="URL de la red social" required>

                    <button type="button" class="boton-add" @click="agregarRed"><i class="fa fa-plus"></i></button>
                </div>

                <label for="descripcion">INFORMACIÓN</label>
                <input type="text" id="descripcion" name="descripcion" value="<?= isset($usuario['descripcion']) ? htmlspecialchars($usuario['descripcion']) : '' ?>" placeholder="Añade información interesante sobre tu barberia">

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


    <script src="../../../public/js/editarPerfilBarber.js" defer></script>
</body>

</html>