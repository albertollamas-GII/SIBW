<?php

  /* Cargamos plantilla Twig */
  
  require_once "/usr/local/lib/php/vendor/autoload.php";

  $loader = new \Twig\Loader\FilesystemLoader('templates');
  $twig = new \Twig\Environment($loader);
  
  include_once ("BD.php");
  
  $baseDatos = new baseDatos();
  $imagenes = $baseDatos->getImagenesPortada();

  echo $twig->render('portada.html', ['imagenes' => $imagenes]);
?>
