window.onload = bindFunctions;

function bindFunctions() {
    if (document.getElementById("formularioInicio").style.display == "")
        document.getElementById("cambioAInicio").onclick = cambiarAInicio;
    
    if (document.getElementById("formularioRegistro").style.display == "")
        document.getElementById("cambioARegistro").onclick = cambiarARegistro;
}

function cambiarAInicio(){
    document.getElementById("formularioRegistro").style.display = "none";
    document.getElementById("formularioInicio").style.display = "block";

}

function cambiarARegistro() {
    document.getElementById("formularioRegistro").style.display = "block";
    document.getElementById("formularioInicio").style.display = "none";

}