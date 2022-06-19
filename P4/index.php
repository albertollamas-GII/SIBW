<?php

  /* Cargamos plantilla Twig */
  
  require_once "/usr/local/lib/php/vendor/autoload.php";

  $loader = new \Twig\Loader\FilesystemLoader('templates');
  $twig = new \Twig\Environment($loader);
  
  include_once ("BD.php");
  
    $baseDatos = new baseDatos();
      $imagenes = $baseDatos->getImagenesPortada();
  

  session_start();


  $user ="Empty";
  if (isset($_SESSION['usuario'])) {
    $user = $_SESSION['usuario'];
  }  

  echo $twig->render('portada.html', ['imagenes' => $imagenes, 'user' => $user]);
?>
