const app = Vue.createApp({
  data() {
    return {
      usuarioId: null,
      barberiaId: null,
      servicios: [],
      barberos: [],
      servicioSeleccionado: null,
      barberoSeleccionado: 0,
      metodoPago: 'paypal',
      fecha: '',
      hora: '',
      mostrarResumen: false,
      estadoReserva: 'resumen'
    };
  },
  computed: {
    servicioNombre() {
      const servicio = this.servicios.find(s => s.id == this.servicioSeleccionado);
      return servicio ? servicio.nombre : '';
    },
      barberoNombre() {
          if (this.barberoSeleccionado == 0) return 'Cualquiera';
          const barbero = this.barberos.find(b => b.id == this.barberoSeleccionado);
          return barbero ? barbero.nombre : '';
      }
  },
  methods: {
    abrirResumen() {
      if (!this.fecha || !this.hora || !this.servicioSeleccionado || !this.metodoPago) {
        alert('Por favor, completa todos los campos.');
        return;
      }
      this.mostrarResumen = true;
    },
    enviarFormulario() {
        const form = this.$refs.formulario;
        const formData = new FormData(form);

        fetch('/dashboard/groomy/public/api/crearCita.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(respuesta => {
                if (respuesta.includes('ok')) {
                    this.estadoReserva = 'exito';

                    setTimeout(() => {
                        window.location.href = '/dashboard/groomy/app/views/usuario/citas.php';
                    }, 5500);
                } else {
                    this.estadoReserva = 'error';
                }
            })
            .catch(() => {
                this.estadoReserva = 'error';
            });
    },
    inicializar(datos) {
        this.usuarioId = datos.usuarioId;
        this.barberiaId = datos.barberiaId;
        this.servicios = datos.servicios;
        this.barberos = datos.barberos;
        // Si servicioSeleccionado viene de la URL, usarlo, si no usar el primero
        this.servicioSeleccionado = Number(datos.servicioSeleccionado) || (datos.servicios[0]?.id || null);
        console.log("servicioSeleccionado inicial:", this.servicioSeleccionado, typeof this.servicioSeleccionado);
    }
  }

});

window.vm = app.mount('#app-reserva'); 
