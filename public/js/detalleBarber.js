const { createApp } = Vue;

createApp({
    data() {
        return {
            vistaActiva: 'servicios',
            servicioSeleccionado: null,
            servicioSeleccionadoId: null,
            mostrarModalLogin: false,
            mostrarModalConfirmar: false,
            idBarberia: window.idBarberia
        }
    },
    methods: {
        seleccionarServicio(id, nombre) {
            if(!window.usuarioPHP) {
                this.mostrarModalLogin = true;
            } else {
                this.servicioSeleccionado = nombre;
                this.servicioSeleccionadoId = id;
                this.mostrarModalConfirmar = true;
            }
        },
        reservarAhora() {
            window.location.href = `reservar.php?id=${this.idBarberia}&servicio=${this.servicioSeleccionadoId}`;
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
