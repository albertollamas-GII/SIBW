$(document).ready(function(){
    document.getElementById("barraBusqueda").onkeyup = busqueda;
    $("#barraBusqueda").focus(function(){
        $("#results").css("width", "75px");
        $("#results").css("display", "block");
        $("#results").css("margin-top", "20px");
        $("#results").css("padding", "0%");
        $("#results").animate({width: "260px"});
    });

    $("#barraBusqueda").focusout(function(){
        setTimeout(function(){
            $("#results").css("display", "none");
        },100)
    });
});

function busqueda() {
    var str = $("#barraBusqueda").val();
    if (str.length == 0) {
        $("#results").html("");
        return;
    }

    $.ajax({
        data: {str},
        url: 'busqueda.php',
        type: 'get',
        success: function(respuesta){
            mostrarResultados(respuesta,str);
        }
    });
}


function mostrarResultados(respuesta,str){
    var html = "";
    if (respuesta != null && respuesta.length > 0) {
        for (var i = 0; i < respuesta.length - 1; i++) {
            html += "<li><a href='producto.php?pr=" + respuesta[i].idProducto + "' class='searchLink'>" + respuesta[i].nombreProducto + "</a></li><br>";
        }
        html += "<li><a href='producto.php?pr=" + respuesta[respuesta.length - 1].idProducto + "' class='searchLink'>" + respuesta[respuesta.length - 1].nombreProducto + "</a></li>";
        $("#results").html(html);


    } else {
        $("#results").html("No se han encontrado productos");
    }
}