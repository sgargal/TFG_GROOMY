const { createApp } = Vue;

createApp({
    data() {
        return {
            mostrarModalBarberia: false,
            barberia: {
                nombre: '',
                email: '',
                password: '',
            },
            mensaje: ''
        };
    },
    methods: {
        generarPassword(){
            const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let password = '';
            for (let i = 0; i < 12; i++) {
                const randomIndex = Math.floor(Math.random() * caracteres.length);
                password += caracteres[randomIndex];
            }
            this.barberia.password = password;
        },
        copiarPassword() {
            if(this.barberia.password) {
                navigator.clipboard.writeText(this.barberia.password)
                .then(() => {
                    alert('Contraseña copiada al portapapeles');

                    setTimeout(() => {
                        this.mensaje = '';
                    }
                    , 2000);
                })
                .catch(err => {
                    console.error('Error al copiar la contraseña: ', err);
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

                const text = await res.json();
                console.log('Respuesta del servidor:', text);
                const resultado = JSON.parse(text);

                if(resultado.success) {
                    alert('¡Barbería registrada con éxito!');
                    this.cerrarModalBarberia();
                    this.barberia = {
                        nombre: '',
                        email: '',
                        password: ''
                    };
                }else {
                    alert ('Error: ' + resultado.mensaje);
                }
            }catch (e) {
                console.error('Error de conexion:', e);
                alert('Error de conexión. Por favor, inténtalo de nuevo más tarde.');
            }
        },
        cerrarModalBarberia() {
            this.mostrarModalBarberia = false;
        }
    },
}).mount('#adminApp');