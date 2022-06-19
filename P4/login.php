<?php

  /* Cargamos plantilla Twig */
  
  require_once "/usr/local/lib/php/vendor/autoload.php";

  $loader = new \Twig\Loader\FilesystemLoader('templates');
  $twig = new \Twig\Environment($loader);

  include_once ("BD.php");
  $error = 0;

  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nick = $_POST['userName'];
    $pass = $_POST['passWord'];
    $baseDatos = new baseDatos();
    
    if ($baseDatos->compruebaInicioSesion($nick, $pass)){
      session_start();
      
      $_SESSION['usuario'] = $baseDatos->getUser($nick);
    }
    
    if (isset($_SESSION['usuario']['usname'])) header ("Location: index.php");
    else header("Location: login.php");

    exit();
  }
  
  echo $twig->render('login.html', []);
?>
