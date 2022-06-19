<?php

    header('Content-Type: application/json');
    
    include_once("BD.php");
    $baseDatos = new baseDatos();

    session_start();
    $user = "Empty";

    if (isset($_SESSION['usuario'])) {
      $user = $baseDatos->getUser($_SESSION['usuario']['usname']);
      if ($user['rango'] >= 3)
        $gestor = true;
    } else 
        $gestor = false;

    $busqueda = $_GET['str'];
    $results = $baseDatos->buscarProductos($busqueda, $gestor);

    echo(json_encode($results));

?>