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
            this.servicios.push({ nombre: '', precio: '' });
        },
        agregarEmpleado() {
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
            this.horarios.push({ dia: '', inicio: '', fin: '' });
        },
        agregarRed() {
            this.redesSociales.push({ tipo: '', url: '' });
        }
    }
}).mount('#formBarberia');