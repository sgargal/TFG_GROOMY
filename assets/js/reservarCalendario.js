document.addEventListener('DOMContentLoaded', () => {
    const calendario = document.getElementById('calendario');
    const fechaInput = document.getElementById('fecha-seleccionada');
    const horasDiv = document.getElementById('horas-disponibles');
    const idBarberia = new URLSearchParams(window.location.search).get('id');

    const hoy = new Date();
    const año = hoy.getFullYear();
    const mes = hoy.getMonth(); // 0 = enero
    const diasSemana = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    // Cabecera con días de la semana
    diasSemana.forEach(d => {
        const titulo = document.createElement('div');
        titulo.textContent = d;
        titulo.classList.add('titulo-dia');
        calendario.appendChild(titulo);
    });

    const primerDia = new Date(año, mes, 1).getDay();
    const diasEnMes = new Date(año, mes + 1, 0).getDate();

    // Espacios en blanco hasta el primer día
    for (let i = 0; i < primerDia; i++) {
        calendario.appendChild(document.createElement('div'));
    }

    // Crear cada día del mes
    for (let dia = 1; dia <= diasEnMes; dia++) {
        const fechaCompleta = new Date(año, mes, dia);
        const fechaISO = fechaCompleta.toLocaleDateString('en-CA'); 

        const divDia = document.createElement('div');
        divDia.classList.add('dia');
        divDia.textContent = dia;
        divDia.dataset.fecha = fechaISO;

        divDia.addEventListener('click', () => {
            // Marcar seleccionado
            document.querySelectorAll('.dia').forEach(d => d.classList.remove('seleccionado'));
            divDia.classList.add('seleccionado');

            // Guardar en input hidden
            fechaInput.value = fechaISO;
            if (window.vm) {
                vm.fecha = fechaISO;
                vm.cargarHorasDisponibles();
            }

            // Cargar horas disponibles
            fetch(`/dashboard/groomy/app/controllers/api/getHorariosDisponibles.php?idBarberia=${idBarberia}&fecha=${fechaISO}`)
                .then(res => res.json())
                .then(horas => {
                    if (horas.length === 0) {
                        horasDiv.innerHTML = "<p>No hay horas disponibles para ese día.</p>";
                        document.getElementById('bloque-horas').style.display = "block";
                        return;
                    }

                    document.getElementById('bloque-horas').style.display = "block";

                    horasDiv.innerHTML = horas.map(hora => `
            <button type="button" class="boton-reserva" data-hora="${hora}">${hora}</button>
          `).join('');
                    document.querySelectorAll('.boton-reserva').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const horaSeleccionada = btn.dataset.hora;

                            // ACTUALIZAR Vue
                            if (window.vm) {
                                vm.hora = horaSeleccionada;
                            }

                            // (Si aún existe este input antiguo, puedes quitar esta línea si no se usa)
                            const inputHora = document.getElementById('hora-seleccionada');
                            if (inputHora) {
                                inputHora.value = horaSeleccionada;
                            }

                            document.querySelectorAll('.boton-reserva').forEach(b => b.classList.remove('activo'));
                            btn.classList.add('activo');
                        });
                    });

                });
        });
        calendario.appendChild(divDia);
    }
});
