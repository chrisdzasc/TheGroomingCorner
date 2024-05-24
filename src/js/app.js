let paso = 1; // Variable
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: [] // Arreglo
}

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
});

function iniciarApp(){

    mostrarSeccion(); // Muestra y oculta las secciones
    tabs(); // Cambia la función cuando se presionen los tabs
    botonesPaginador(); // Agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();

    consultarAPI(); // Consulta la API en el backend de PHP

    idCliente(); // 
    nombreCliente(); // Añade el nombre del cliente al objeto de cita
    seleccionarFecha(); // Añade la fecha de la cita en el objeto de cita
    seleccionarHora(); // Añade la hora de la cita en el objeto de cita

    mostrarResumen(); // Muestra el resumen de la cita
}

function mostrarSeccion(){

    // Ocultar la sección que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar'); // Selecciona la clase que tenga mostrar
    if(seccionAnterior){
        seccionAnterior.classList.remove('mostrar'); 
    }

    // Seleccionar la seccion con el paso
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    // Quita la clase de actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior){
        tabAnterior.classList.remove('actual');
    }

    // Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs(){
    const botones = document.querySelectorAll('.tabs button'); // Vamos a seleccionar TODOS los botones = 3

    botones.forEach(boton =>{
        boton.addEventListener('click', function(e){
            paso = parseInt(e.target.dataset.paso);

            mostrarSeccion();
            botonesPaginador();

            if( paso === 3 ){
                mostrarResumen();
            }
        });
    })
}

function botonesPaginador(){

    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if(paso === 1){
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }else if(paso === 3){
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');

        mostrarResumen();
    }else{
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

function paginaAnterior(){
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function(){
        if(paso <= pasoInicial){
            return
        }
        paso--;

        botonesPaginador();
    })
}

function paginaSiguiente(){
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function(){
        if(paso >= pasoFinal){
            return
        }
        paso++;

        botonesPaginador();
    })
}

async function consultarAPI(){
    try {
        const url = '/api/servicios'; // La url que tiene mi API.
        const resultado = await fetch(url); // La funcion que nos va a permitir consumir ese servicio
        const servicios = await resultado.json(); // Vamos a guardar los JSON en la variable de servicios.

        mostrarServicios(servicios);

    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios){
    servicios.forEach(servicio =>{
        const{id, nombre, precio} = servicio; // Destructure. De esta forma ya tengo una variable y el valor de cada uno.

        const nombreServicio = document.createElement('P'); // Vamos a crear un parrafo.
        nombreServicio.classList.add('nombre-servicio'); // Le agregamos una clase para darle estilo css.
        nombreServicio.textContent = nombre; // El contenido que va a tener es el nombre. 
        
        const precioServicio = document.createElement('P'); // Vamos a crear un parrafo.
        precioServicio.classList.add('precio-servicio'); // Le agregamos una clase para darle estilo css.
        precioServicio.textContent = `$${precio}`; // El contenido que va a tener es el precio.

        const servicioDiv = document.createElement('DIV'); // Vamos a crear un div.
        servicioDiv.classList.add('servicio'); // Le agregamos una clase para darle estilo css.
        servicioDiv.dataset.idServicio = id; // Para seleccionar un atributo.
        servicioDiv.onclick = function(){
            seleccionarServicio(servicio);
        }

        servicioDiv.appendChild(nombreServicio); // Le metemos dentro del div todo el nombreServicio
        servicioDiv.appendChild(precioServicio); // Le metemos dentro del div todo el precioServicio

        document.querySelector('#servicios').appendChild(servicioDiv); // Le estamos inyectando servicioDiv a servicios de html.
    });
}

function seleccionarServicio(servicio){
    const { id } = servicio;
    const { servicios } = cita; // Estoy extrayendo los servicios del objeto de cita y lo extraigo porque vamos a estar escribiendo sobre él.
    
    // Identificar al elemento que se le da clic
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    // Comprobar su un servicio ya fue agregado
    if( servicios.some(agregado => agregado.id === id) ){ // Va a verificar sobre todo el arreglo y va a retornar true o false.
        // Eliminarlo
        cita.servicios = servicios.filter( agregado => agregado.id !== id );
        divServicio.classList.remove('seleccionado');
    }else{
        // Agregarlo
        cita.servicios = [...servicios, servicio]; // Toma una copia de los servicios y también le agrego el nuevo servicio.
        divServicio.classList.add('seleccionado');
    }

    console.log(cita);
}

function idCliente(){
    const id = document.querySelector('#id').value; // Obteniendo el id

    cita.id = id;
}

function nombreCliente(){
    const nombre = document.querySelector('#nombre').value; // Obteniendo el nombre

    cita.nombre = nombre; // Asignandole nombre al objeto de cita.
}

function seleccionarFecha(){
    const inputFecha = document.querySelector('#fecha'); // Seleccionamos id de fecha

    inputFecha.addEventListener('input', function(e){

        const dia = new Date(e.target.value).getUTCDay();

        if( [6, 0].includes(dia) ){
            e.target.value = '';
            mostrarAlerta('Fines de semana no permitidos', 'error', '.formulario');
        }else{
            cita.fecha = e.target.value;
        }
    });
}

function seleccionarHora(){
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e){

        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0]; // Separar la hora y los minutos

        if(hora < 10 || hora > 18){ // Si es antes de las 10 y después de las 18
            // No es hora válida
            e.target.value = '';
            mostrarAlerta('Hora no permitida', 'error', '.formulario');
        }else{
            // Si es hora válida
            cita.hora = e.target.value;
        }
    });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true){

    // Previene que se genere más de una alerta
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia){
        alertaPrevia.remove();
    }

    // Scripting para que genere una alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    // Eliminar alerta
    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparece){
        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }

}

function mostrarResumen(){
    const resumen = document.querySelector('.contenido-resumen');

    // Limpiar el contenido de Resumen
    while(resumen.firstChild){
        resumen.removeChild(resumen.firstChild);
    }

    if( Object.values(cita).includes('') || cita.servicios.length === 0 ){
        mostrarAlerta('Faltan datos de servicios, Fecha u Hora', 'error', '.contenido-resumen', false);

        return;
    }

    // Formatear el div de resumen
    const { nombre, fecha, hora, servicios } = cita;

    // Heading para Servicios en Resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);

    // Iterando y mostrando los servicios
    servicios.forEach( servicio => {
        const { id, precio, nombre} = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P'); // Nombre del servicio
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    });

    // Heading para Servicios en Resumen
    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de Cita';
    resumen.appendChild(headingCita);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre: </span> ${nombre}`;

    // Formatear la fecha en español
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth(); // Retorna el mes
    const dia = fechaObj.getDate() + 2; // Retorna el dia de la semana
    const year = fechaObj.getFullYear(); // Retorna el año

    const fechaUTC = new Date( Date.UTC(year, mes, dia) ); // Instanciamos la nueva fecha

    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones);

    const FechaCita = document.createElement('P');
    FechaCita.innerHTML = `<span>Fecha: </span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora: </span> ${hora} Horas`;

    // Botón para crear una Cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(FechaCita);
    resumen.appendChild(horaCita);

    resumen.appendChild(botonReservar);
}

async function reservarCita(){

    const { fecha, hora, servicios, id } = cita; // Extraer del objeto de cita.
    const idServicio = servicios.map( servicio => servicio.id );

    const datos = new FormData();
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios', idServicio);

    try {
        // Petición hacia la API
        const url = '/api/citas';

        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();

        if(resultado.resultado){
            Swal.fire({
                icon: "success",
                title: "Cita Creada",
                text: "Tu cita fue creada correctamente",
                button: 'OK'
            }).then(() =>{
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un error al guardar la cita"
          });
    }

    console.log(resultado);
    
}

