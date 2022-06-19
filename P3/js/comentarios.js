window.onload = bindFunctions;
function bindFunctions() {
    //Cuando se pulse muestra los comentarios
    document.getElementById("mostrarComentarios").onclick = mostrarComentarios;
    
    if (document.getElementById("seccionComentarios").style.display == "") {
        document.getElementById("seccionComentarios").style.display = "none";
    }

    //Cuando se pulse se envia el comentario
    document.getElementById("enviarComentario").onclick = enviarComentarios;

    //Mientras se escribe se censuran las palabras llamanndo a la funcion de censurar
    document.getElementById("comentario").oninput = censurar;

    //Reseteamos lo escrito en correo al recargar la página
    document.getElementById("comentario").value = "";
    document.getElementById("username").value = "";
    document.getElementById("email").value = "";

    //Ocultamos el boton de eviar comentarios
    document.getElementById("enviarComentario").style.display = "none";

}



function mostrarComentarios() {
    //console.log(commentSection.style.display) Prueba para ver qué soltaba

    //En función de si la sección era visible o no, se muestra u oculta y se cambia el texto del botón
    if (document.getElementById("seccionComentarios").style.display == "none") {
        document.getElementById("seccionComentarios").style.display = "block";
        document.getElementById("mostrarComentarios").innerText = "Ocultar Comentarios";
    } else {
        document.getElementById("seccionComentarios").style.display = "none";
        document.getElementById("mostrarComentarios").innerText = "Mostrar Comentarios";
    }

}

function enviarComentarios() {

    var feed = document.getElementById("feedComentarios");
    var username = document.getElementById("username");
    var email = document.getElementById("email");
    var textComment = document.getElementById("comentario");
    var modal = document.getElementById("modal");
    var spanModal = document.getElementsByClassName("cerrar")[0];
    
    var ilegal = false;


    //Validacion nombre de usuario
    if (username.value == "") {
        username.style.borderColor = "rgb(208, 0, 0)";
        ilegal = true;
        // alert("No has puesto un nombre de usuario");
        var errorUser = document.getElementById("usuarioErroneo");
        errorUser.textContent = "INTRODUZCA UN NOMBRE";
    } else{
        username.style.borderColor = "black";
        var errorUser = document.getElementById("usuarioErroneo");
        errorUser.textContent = "";
    }

    //Validacion Email
    if (!validateEmail(email.value)) {
        ilegal = true;
        email.style.borderColor = "rgb(208, 0, 0)";
        var errorCorreo = document.getElementById("emailErroneo");
        errorCorreo.textContent = "INTRODUZCA UN CORREO VÁLIDO: johndoe@mail.com";
        // alert("El correo no es valido");
    } else {
        email.style.borderColor = "white"
        var errorCorreo = document.getElementById("emailErroneo");
        errorCorreo.textContent = "";
    }

    //Validacion texto comentario
    if (textComment.value == "") {
        ilegal = true;
        textComment.style.borderColor = "rgb(208, 0, 0)";
    } else {
        textComment.style.borderColor = "black"
    }

    //Fecha
    var hoy = new Date();
    var dia = String(hoy.getDate()).padStart(2, '0');
    var mes = String(hoy.getMonth() + 1).padStart(2, '0');
    var anio = hoy.getFullYear();
    var hora = hoy.getHours();
    var minutos = hoy.getMinutes();

    if (minutos < 10) {
        minutos = '0' + minutos;
    }

    fecha = dia + ' de ' + mesPalabra(mes) + ' ' + anio + ', ' + hora + ':' + minutos;

    //Si no es un comentario ilegal, lo publicamos
    if (!ilegal) {

        var comentarioPublicado = document.createElement("div");
        comentarioPublicado.innerHTML = ' <div class="container-username">\n  <img src="static/images/userPhoto.jpg"><p class="autor">'
            + username.value.toUpperCase() + '</p>\n</div>\n' + '<p class="fecha">' + fecha + '</p>\n' +
            '<p class="text">' + textComment.value + '</p>\n';

        comentarioPublicado.setAttribute('class', 'comentarioPublicado');
        feed.appendChild(comentarioPublicado);
        
        textComment.value = "";
        username.value = "";
        email.value = "";
    } else {
        modal.style.display = "block";
        spanModal.onclick = function(){
           modal.style.display = "none";
        }
        window.onclick = function(event){
           if (event.target == modal) {
              modal.style.display = "none";
           }
        }
    }

}

//Funcion para validar el email con una expresion regular
function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+$/;
    return re.test(email);
}

//Funcion que dependiendo del mes lo sustituye por su correspondiente palabra
function mesPalabra(numeroMes) {
    switch (numeroMes) {
        case '01':
            return 'enero';
        case '02':
            return 'febrero';
        case '03':
            return 'marzo';
        case '04':
            return 'abril';
        case '05':
            return 'mayo';
        case '06':
            return 'junio';
        case '07':
            return 'julio';
        case '08':
            return 'agosto';
        case '09':
            return 'septiembre';
        case '10':
            return 'octubre';
        case '11':
            return 'noviembre';
        case '12':
            return 'diciembre';
    }
}

function censurar() {
    var comentario = document.getElementById("comentario");


    var ocultarBoton = document.getElementById("enviarComentario");
    //Ocultamos el boton si no hay nada escrito
    if(comentario.value === ""){
      ocultarBoton.style.display = "none";
    }
    else{
      ocultarBoton.style.display = "block";
    }
    console.log(document.getElementById('palabrasBan').innerHTML.split("-"));
    var palabrasBaneadas = document.getElementById('palabrasBan').innerHTML.split("-");
    var texto = comentario.value;

    //Se construye la cadena con el número de * correspondientes a cada palabra
    for (var i = 0; i < palabrasBaneadas.length; i++) {
        if (texto.toLowerCase().includes(palabrasBaneadas[i]))
            document.getElementById("comentario").value = document.getElementById("comentario").value.replace(palabrasBaneadas[i],'*'.repeat (palabrasBaneadas[i].length));
    }

}
