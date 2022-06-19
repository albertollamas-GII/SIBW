$(document).ready(function(){
    var cadena = $("#cadenaBusqueda").html();
    var reemplaza = "<em>" + cadena + "</em>";
    var cuerpo = $("#cuerpoProducto");

    var regexitem = new RegExp(cadena,"gi");
    if (cadena.length > 0){
        var destacado = cuerpo.html().replace(regexitem,reemplaza);
        cuerpo.html(destacado);
    }
});