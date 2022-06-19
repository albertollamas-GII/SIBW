<?php
  /* Cargamos plantilla Twig */
  
  require_once "/usr/local/lib/php/vendor/autoload.php";

  $loader = new \Twig\Loader\FilesystemLoader('templates');
  $twig = new \Twig\Environment($loader);

    include_once("BD.php");
    $baseDatos = new baseDatos();

    session_start();
    $user = "Empty";

    if (isset($_SESSION['usuario'])) {
      $user = $baseDatos->getUser($_SESSION['usuario']['usname']);
      if ($user['rango'] < 3) header("Location: index.php");

      $listaProductos = $baseDatos->getImagenesPortada();
      
      echo $twig->render('listaProductos.html', ['user' => $user, 'productos' => $listaProductos]);
    } else {
      $errores['titulo'] = "403 - FORBIDDEN";
      $errores['descripcion'] = "Buen intento! No tienes permisos para acceder a esta página. Contacta con un administrador si crees que debes tener";
      echo $twig->render('error.html', ['errores' => $errores]);
    }
    


?>