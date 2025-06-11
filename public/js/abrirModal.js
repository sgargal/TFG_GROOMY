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
                    estado: this.accion
                })
            })
                .then(res => {
                    if (!res.ok) throw new Error('Error HTTP');
                    return res.json();
                })
                .then(data => {
                    if (data.message && data.message.includes('Cita actualizada')) {
                        this.mostrarModal = false;
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Respuesta inesperada'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error: ' + err.message);
                });
        }
    }
}).mount('#appCita');