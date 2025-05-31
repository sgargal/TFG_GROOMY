const { createApp, defineComponent } = Vue;

const App = defineComponent({
  data() {
    return {
      mostrarLogin: false,
      mostrarSign: false,
      mostrarPerfil: false,
      mostrarCerrarSesion:false,
      registro: {
        nombre: '',
        email: '',
        password: ''
      },
      datosLogin: {
        email: '',
        password: ''
      },
      usuario: {
        nombre: '',
        email: '',
        imagen: '',
      },
      mensaje: '',
      tipoMensaje: ''
    };
  },
  mounted() {
    const usuarioPHP = window.usuarioPHP;
    if (usuarioPHP) {
      if (usuarioPHP.imagen) {
        usuarioPHP.imagen = `/dashboard/groomy/assets/src/users/${usuarioPHP.imagen}`;
      }
      this.usuario = usuarioPHP;
    }
  },
  methods: {
    async registrar() {
      try {
        const formData = new FormData();
        formData.append('action', 'registrar');
        formData.append('nombre', this.registro.nombre);
        formData.append('email', this.registro.email);
        formData.append('password', this.registro.password);
        formData.append('rol', 'user');

        const res = await fetch('/dashboard/groomy/public/index.php', {
          method: 'POST',
          body: formData
        });

        const data = await res.json();

        if (data.success) {
          this.mensaje = data.mensaje;
          this.tipoMensaje = 'exito';

          setTimeout(() => {
            location.reload();
          }, 2200);
        } else {
          this.mensaje = data.mensaje || 'Error al registrar';
          this.tipoMensaje = 'error';
        }
      } catch (error) {
        console.error(error);
        this.mensaje = 'Error de conexión con el servidor.';
        this.tipoMensaje = 'error';
      }
    },
    login() {
      const formData = new FormData();
      formData.append('action', 'login');
      formData.append('email', this.datosLogin.email);
      formData.append('password', this.datosLogin.password);

      fetch('/dashboard/groomy/public/index.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            this.tipoMensaje = 'exito';
            this.mensaje = 'Inicio de sesión exitoso.';

            setTimeout(() => {
              location.reload();
            }, 2200);
          } else {
            this.tipoMensaje = 'error';
            this.mensaje = data.mensaje || 'Credenciales incorrectas';


            setTimeout(() => {
              this.mensaje = '';
              this.tipoMensaje = '';
            }, 2200);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          this.tipoMensaje = 'error';
          this.mensaje = 'Error de conexión con el servidor';
          setTimeout(() => {
            this.mensaje = '';
            this.tipoMensaje = '';
          }, 2200);
        });
    },
    cerrarSesion(){
      window.location.href = '../public/api/logout.php';
    },
    cerrarLogin() {
      this.mostrarLogin = false;
    },
    cerrarRegistro() {
      this.mostrarSign = false;
    },
    cerrarPerfil() {
      this.mostrarPerfil = false;
    },
    cerrarCerrarSesion(){
      this.mostrarCerrarSesion = false;
    }
  }
});

createApp(App).mount('#app');
