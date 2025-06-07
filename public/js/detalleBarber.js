const { createApp } = Vue;

createApp({
    data() {
        return {
            vistaActiva: 'servicios',
            servicioSeleccionado: null,
            mostrarModalLogin: false,
            mostrarModalConfirmar: false,
            idBarberia: window.idBarberia
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
            window.location.href = `reservar.php?id=${this.idBarberia}`;
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
