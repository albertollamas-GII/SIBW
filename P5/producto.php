<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);
    
    include_once("BD.php");

    $baseDatos = new baseDatos();
    session_start();
    $user ="Empty";
    if (isset($_SESSION['usuario']))
      $user = $baseDatos->getUser($_SESSION['usuario']['usname']);

    if (isset($_GET['pr']))
      $idPr = $_GET['pr'];
    else 
      $idPr = -1;

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $comentario = $_POST['comentario'];
        
        $uname = $_SESSION['usuario']['usname'];
        
        $baseDatos->guardarComentario($uname,$comentario,$idPr);
        header("Location: producto.php?pr=" . $idPr);

        exit();
    }
    
    $producto = $baseDatos->getProducto($idPr);
    $etiquetas = $baseDatos->getEtiquetasProducto($idPr);
    if (empty($producto)){
      echo $twig->render('404.html', []);
    } else {
      $imagenes = $baseDatos->getImagenesProducto($idPr);
      $imagenes = $imagenes + $baseDatos->getImagenPortada($idPr);
      $comentarios = $baseDatos->getComentariosProducto($idPr);
      $baneadas = $baseDatos->getPalabrasBaneadas();
      $cadena = $_GET['cadena'];
      echo $twig->render('producto.html', ['etiquetas' => $etiquetas, 'producto' => $producto, 'imagenes' => $imagenes, 'comentarios' => $comentarios, 'baneadas'=> $baneadas, 'user' => $user, 'cadena' => $cadena]);
    }
  
?>
