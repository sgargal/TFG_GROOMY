const { createApp } = Vue;

createApp({
    data() {
        return {
            servicios: [
                { nombre: '', precio: ''}
            ],
            empleados: [
                {nombre: '', imagen: null}
            ],
            horarios: [
                { dia: '', inicio: '', fin: '' }
            ],
            redesSociales: [
                { tipo: '', url: '' }
            ]
                
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
            if (file) {
                this.empleados[index].imagen = file;
            }
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
    watch: {
        servicios(val) {
            console.log("Servicios actuales:", val);
        },
        empleados(val) {
            console.log("Empleados actuales:", val);
        }
    }

}).mount('#formBarberia');