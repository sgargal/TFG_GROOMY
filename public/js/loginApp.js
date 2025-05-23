const { createApp, defineComponent } = Vue;

const App = defineComponent({
  data() {
    return {
      mostrarLogin: false,
      mostrarSign: false,
      registro: {
        nombre: '',
        email: '',
        password: ''
      },
      login: {
        email: '',
        password: ''
      },
      mensaje: ''
    };
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
    async login() {
      try {
        const formData = new FormData();
        formData.append('action', 'login');
        formData.append('email', this.login.email);
        formData.append('password', this.login.password);

        const res = await fetch('/dashboard/groomy//public/api/usuario.php', {
          method: 'POST',
          body: formData
        });

        const texto = await res.text();
        this.mensaje = texto;
        alert(texto);

        if (texto.includes("Login exitoso")) {
          window.location.href = '../index.php';
        }
      } catch (error) {
        console.error(error);
      }
    },
    cerrarLogin() {
      this.mostrarLogin = false;
    },
    cerrarRegistro() {
      this.mostrarSign = false;
    }
  }
});

createApp(App).mount('#app');
