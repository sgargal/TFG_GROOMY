const { createApp } = Vue;

createApp({
    data() {
        return {
            vistaActiva: 'servicios',
            servicioSeleccionado: null,
            mostrarModalLogin: false,
            mostrarModalConfirmar: false
        }
    },
    methods: {
        seleccionarServicio(nombre) {
            if(!window.usuarioPHP) {
                this.mostrarModalLogin = true;
            } else {
                this.servicioSeleccionado = nombre;
                this.mostrarModalConfirmar = true;
            }
        },
        reservarAhora() {
            window.location.href = 'reservar.php';
        },
        cerrarModal() {
            this.mostrarModalLogin = false;
            this.mostrarModalConfirmar = false;
        }, 
        irAlInicio() {
            window.location.href = '/dashboard/groomy/public/index.php';
        }
    }
}).mount('#appDetalleBarberia');
