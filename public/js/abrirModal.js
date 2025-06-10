const { createApp } = Vue;

createApp ({
    data() {
        return {
            mostrarModal: false,
            accion: '',
            citaSeleccionada: null
        }
    },
    methods: {
        abrirModal(accion, idCita){
            this.accion = accion;
            this.citaSeleccionada = idCita;
            this.mostrarModal = true;
        },
        confirmarAccion() {
            const url = '../../../public/api/estadoCitas.php';
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_cita: this.citaSeleccionada,
                    estado: this.accion // 'realizada' o 'cancelada'
                })
            })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    this.mostrarModal = false;
                    location.reload();
                });

        }
    }
}).mount('#appCita');