<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    include("BD.php");

    $idPr = $_GET['pr'];
    $tag = $_GET['tag'];
    session_start();
    $user = "Empty";
    
    $baseDatos = new baseDatos();

    if (isset($_SESSION['usuario'])) {
      $user = $baseDatos->getUser($_SESSION['usuario']['usname']);
    } else {
      $errores['titulo'] = "403 - FORBIDDEN";
      $errores['descripcion'] = "Buen intento! No tienes permisos para acceder a esta página. Contacta con un administrador si crees que debes tener";
      echo $twig->render('error.html', ['errores' => $errores]);
    }

    if($user['rango'] >= 3){
        $baseDatos->deleteEtiqueta($tag,$idPr);
        header("Location: editProducto.php?pr=".$_GET['pr']);
    }else {
            $errores['titulo'] = "403 - FORBIDDEN";
            $errores['descripcion'] = "Buen intento! No tienes permisos para acceder a esta página. Contacta con un administrador si crees que debes tener";
            echo $twig->render('error.html', ['errores' => $errores]);
      }
    
?>