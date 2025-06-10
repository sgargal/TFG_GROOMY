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
      const formData = new FormData(this.$refs.formulario);

      fetch('../../../public/api/crearCita.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            this.estadoReserva = 'exito';

            setTimeout(() => {
                        window.location.href = '/dashboard/groomy/app/views/usuario/citas.php?estado=pendiente';
                    }, 1500);
          } else {
              this.estadoReserva = 'error';
              console.error(data.message || 'Error desconocido');
          }
        })
        .catch(error => {
          console.error('Error en la petición:', error);
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
