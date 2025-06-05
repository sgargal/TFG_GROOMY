const { createApp } = Vue;

createApp({
    data() {
        return {
            servicios: [],
            empleados: [],
            horarios: [],
            redesSociales: []
        };
    },
    methods: {
        agregarServicio() {
            console.log("Agregando servicio...");
            this.servicios.push({ nombre: '', precio: '' });
        },
        agregarEmpleado() {
            console.log("Agregando empleado...");
            this.empleados.push({
                nombre: '',
                imagen: null
            });
        },
        cargarImagen(event, index) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('imagenEmpleado', file);

            fetch('../../../public/api/subirFotoEmpleado.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.empleados[index].imagen = data.ruta; // ← ya tienes la ruta
                    } else {
                        alert('Error al subir imagen');
                    }
                })
                .catch(error => {
                    console.error('Error subiendo imagen:', error);
                });
        },
        agregarHorario() {
            console.log("Agregando horario...");
            this.horarios.push({ dia: '', inicio: '', fin: '' });
        },
        agregarRed() {
            console.log("Agregando red social...");
            this.redesSociales.push({ tipo: '', url: '' });
        }
    },
    mounted() {
        fetch('../../../public/api/barberia.php?action=obtenerPerfil')
            .then(res => res.json())
            .then(data => {
                this.servicios = (data.servicios && data.servicios.length > 0) ? data.servicios : [{ nombre: '', precio: '' }];
                this.empleados = (data.empleados && data.empleados.length > 0) ? data.empleados : [{ nombre: '', imagen: null }];
                this.horarios = (data.horarios && data.horarios.length > 0) ? data.horarios : [{ dia: '', inicio: '', fin: '' }];
                this.redesSociales = (data.redesSociales && data.redesSociales.length > 0) ? data.redesSociales : [{ tipo: '', url: '' }];
            })
            .catch(error => {
                console.error("Error cargando perfil:", error);
            });
    }
}).mount('#formBarberia');