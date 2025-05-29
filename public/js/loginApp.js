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
      mensaje: ''
    };
  },
   mounted() {
    const usuarioPHP = window.usuarioPHP;
    if (usuarioPHP) {
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

        const res = await fetch('/dashboard/groomy/public/api/usuario.php', {
          method: 'POST',
          body: formData
        });

        const text = await res.text();
        this.mensaje = text;
        alert(text);
      } catch (error) {
        console.error(error);
      }
    },
    login() {
      const formData = new FormData();
      formData.append('email', this.datosLogin.email);
      formData.append('password', this.datosLogin.password);

      fetch('api/login.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
      .then(res => {
        if (res.redirected) {
          window.location.href = res.url;
        } else {
          return res.text().then(text => {
            if(text.trim() === 'ok') {
              alert('Credenciales incorrectas');
            }
          });
        }
      })
        .catch (error => console.error('Error: ', error));
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
