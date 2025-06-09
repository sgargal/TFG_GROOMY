const { createApp } = Vue;

const app = createApp({
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
      this.servicios.push({ nombre: '', precio: '' });
    },
    agregarEmpleado() {
      this.empleados.push({ nombre: '', imagen: null });
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
            this.empleados[index].imagen = data.ruta;
          } else {
            alert('Error al subir imagen');
          }
        })
        .catch(error => console.error('Error subiendo imagen:', error));
    },
    agregarHorario() {
      this.horarios.push({ dia: '', inicio: '', fin: '' });
    },
    agregarRed() {
      this.redesSociales.push({ tipo: '', url: '' });
    }
  },
  mounted() {
    fetch('../../../public/api/barberia.php?action=obtenerPerfil')
      .then(res => res.json())
      .then(data => {
        this.servicios = (data.servicios || []).map(s => ({
          nombre: s.nombre,
          precio: s.precio
        }));
        this.empleados = (data.empleados || []).map(e => ({
          nombre: e.nombre,
          imagen: e.imagen
        }));
        this.horarios = (data.horarios || []).map(h => ({ ...h }));
        this.redesSociales = (data.redesSociales || []).map(r => ({ ...r }));
      })
      .catch(error => {
        console.error("Error cargando perfil:", error);
      });
  }
});

const vm = app.mount('#formBarberia');

// 🔁 Forzar que Vue termine de reaccionar antes de enviar
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form');

  form.addEventListener('submit', (e) => {
  // ✋ Evitamos que el navegador lo envíe antes de tiempo
  e.preventDefault();

  // 📝 Metemos los datos actualizados en los inputs ocultos
  document.getElementById('input-servicios').value = JSON.stringify(vm.servicios);
  document.getElementById('input-empleados').value = JSON.stringify(vm.empleados);
  document.getElementById('input-horarios').value = JSON.stringify(vm.horarios);
  document.getElementById('input-redes').value = JSON.stringify(vm.redesSociales);

  // ✅ Una vez puestos los datos, ahora sí enviamos el formulario
  form.submit();
});

});
