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
    },
    cargarHorasDisponibles() {
      const idBarbero = Number(this.barberoSeleccionado ?? 0); // fuerza a número
      const url = `../../../app/controllers/api/getHorariosDisponibles.php?id_barberia=${this.barberiaId}&fecha=${this.fecha}&id_barbero=${idBarbero}`;

      console.log("⏳ Cargando horas para:", this.fecha, "Barbero:", idBarbero);

      fetch(url)
        .then(response => response.json())
        .then(data => {
          const horasContainer = document.getElementById('horas-disponibles');
          horasContainer.innerHTML = '';

          data.forEach(hora => {
            const btn = document.createElement('button');
            btn.textContent = hora;
            btn.classList.add('boton-hora');
            btn.addEventListener('click', () => {
              this.hora = hora;
              document.querySelectorAll('#bloque-horas button').forEach(b => b.classList.remove('seleccionado'));
              btn.classList.add('seleccionado');
            });
            horasContainer.appendChild(btn);
          });

          document.getElementById('bloque-horas').style.display = data.length > 0 ? 'block' : 'none';
        })
        .catch(error => {
          console.error(' Error cargando horas:', error);
        });
    }

  }
});

window.vm = app.mount('#app-reserva'); 
