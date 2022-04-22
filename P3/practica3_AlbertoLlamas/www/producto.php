<?php
  require_once "/usr/local/lib/php/vendor/autoload.php";

  $loader = new \Twig\Loader\FilesystemLoader('templates');
  $twig = new \Twig\Environment($loader);
  
  include_once("BD.php");

  $baseDatos = new baseDatos();
  
  if (isset($_GET['pr']))
    $idPr = $_GET['pr'];
  else 
    $idPr = -1;
  $producto = $baseDatos->getProducto($idPr);

  if (empty($producto)){
    echo $twig->render('404.html', []);
  } else {
    $imagenes = $baseDatos->getImagenesProducto($idPr);
    $comentarios = $baseDatos->getComentariosProducto($idPr);
    $baneadas = $baseDatos->getPalabrasBaneadas();
    echo $twig->render('producto.html', ['producto' => $producto, 'imagenes' => $imagenes, 'comentarios' => $comentarios, 'baneadas'=> $baneadas]);
  }
  
?>
