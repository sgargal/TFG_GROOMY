<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Preguntas Frecuentes</title>
  <link rel="icon" href="../../../assets/src/logoGROOMY-fondosin.png">
  <link rel="stylesheet" href="../../../assets/css/style-etc.css">
  <link rel="stylesheet" href="../../../assets/css/responsives-etc.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
</head>
<body>
  <section id="app">
    <button class="btn-back">
      <a href="../../../public/index.php"><i class="fas fa-arrow-left"></i>Volver</a>
    </button>

    <section class="faq-header">
      <h1>PREGUNTAS FRECUENTES</h1>

      <section class="search-container">
        <i class="fas fa-search"></i> <input type="text" v-model="search" placeholder="Buscar...">
      </section>
    </section>

    <article class="faq-item" v-for="(faq, index) in filtrandoFaqs" :key="index">
      <h3> {{ faq.pregunta }}</h3>
      <p v-html="faq.respuesta"></p>
    </article>

    <article class="faq-contact">
      <h3>¿Tienes otra pregunta?</h3>
      <p>Si no has encontrado lo que buscabas, contáctanos directamente. Estaremos encantados de aclarar todas tus dudas.  <a href="contactanos.php">Pulsa aquí</a>  para contactar con nosotros.</p>
      
    </article>
  </section>

<script>
  const { createApp, ref, computed } = Vue;

  createApp({
    setup() {
      const search = ref('');

      const faqs =ref([
        {
          pregunta: '¿Cómo reservo una cita?',
          respuesta: 'Puede reservar una cita clicando a la barbería donde desee realizar el servicio y una vez dentro elija el servicio que más se ajuste a usted. Podrá elegir el barbero y la hora disponible que prefiera.'
        },
        {
          pregunta: '¿Necesito registrarme para pedir una cita?',
          respuesta: 'Sí, es necesario crear una cuenta para poder gestionar tus citas y recibir notificaciones.'
        },
        {
          pregunta: '¿Puedo cancelar o modificar una cita?',
          respuesta: 'Sí, desde tu perfil puedes ver tus citas próximas y cancelarlas o modificarlas.'
        },
        {
          pregunta: '¿Cómo puedo registrar mi barbería en GROOMY?',
          respuesta: 'Si tienes una barbería y quieres unirte a nuestra plataforma, contáctanos.  <a href="contactanos.php"><strong>Pulsa aquí</strong></a>   y encontrarás toda la información.'
        },
        {
          pregunta: '¿Puedo ver el historial de mis citas anteriores?',
          respuesta: 'Sí, en tu perfil encontrarás una sección donde puedes consultar todas tus citas pasadas.'
        },
        {
          pregunta: '¿Se pueden hacer pagos a través de la aplicación?',
          respuesta: 'Sí, puedes pagar tus servicios directamente desde la web usando PayPal de forma rápida y segura.'
        }
      ]);

      const filtrandoFaqs = computed(() => {
          if (!search.value.trim()) return faqs.value;
          return faqs.value.filter(faq =>
            faq.pregunta.toLowerCase().includes(search.value.toLowerCase())
          );
      });

      return { search, filtrandoFaqs };
    }   
  }).mount('#app');
</script>
</body>
</html>