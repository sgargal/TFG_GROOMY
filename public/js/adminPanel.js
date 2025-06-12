const { createApp } = Vue;

createApp({
    data() {
        return {
            mostrarModalBarberia: false,
            barberia: {
                nombre: '',
                email: '',
                password: ''
            }, 
            mensaje: '',
            tipoMensaje: ''
        };
    },
    methods: {
        generarPassword() {
            const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let password = '';
            for (let i = 0; i < 12; i++) {
                const randomIndex = Math.floor(Math.random() * caracteres.length);
                password += caracteres[randomIndex];
            }
            this.barberia.password = password;
        },
        copiarPassword() {
            if (this.barberia.password) {
                navigator.clipboard.writeText(this.barberia.password)
                    .then(() => {
                        alert('Contraseña copiada al portapapeles');
                    })
                    .catch(err => {
                        console.error('Error al copiar la contraseña:', err);
                        alert('No fue posible copiar la contraseña.');
                    });
            }
        },
        async registrarBarberia() {
            const datos = new FormData();
            datos.append('nombre', this.barberia.nombre);
            datos.append('email', this.barberia.email);
            datos.append('password', this.barberia.password);
            datos.append('action', 'registrarBarberia');

            try {
                const res = await fetch('../../../public/api/barberia.php', {
                    method: 'POST',
                    body: datos
                });

                const text = await res.text();
                console.log('Respuesta del servidor sin parsear:', text);

                let resultado;
                try {
                    resultado = JSON.parse(text);
                } catch (e) {
                    alert('Respuesta no válida del servidor:\n' + text);
                    return;
                }

                if (resultado.success) {
                    this.mensaje = '¡Barbería registrada con éxito!';
                    this.tipoMensaje = 'exito';

                    setTimeout(() => {
                        this.cerrarModalBarberia();
                        this.barberia = {
                            nombre: '',
                            email: '',
                            password: ''
                        };
                        this.mensaje = '';
                        this.tipoMensaje = '';
                    }, 2000);
                } else {
                    this.mensaje = resultado.mensaje || 'Error al registrar la barbería.';
                    this.tipoMensaje = 'error';
                }

            } catch (error) {
                console.error('Error de conexión:', error);
                alert('Error de conexión. Por favor, inténtalo de nuevo más tarde.');
            }
        },
        cerrarModalBarberia() {
            this.mostrarModalBarberia = false;
        }
    }
}).mount('#adminApp');
