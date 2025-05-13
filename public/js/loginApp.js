const { createApp } = Vue;

createApp({
    data() {
        return {
            mostrarLogin: false,
            mostrarSign: false
        };
    },
    methods: {
        abrirLogin() {
            this.mostrarLogin = true;
            this.mostrarSign = false;
        },
        cerrarLogin() {
            this.mostrarLogin = false;
        },
        abrirRegistro() {
            this.mostrarSign = true;
            this.mostrarLogin = false;
        },
        cerrarRegistro() {
            this.mostrarSign = false;
        }
    }
}).mount('#app');
