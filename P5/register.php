<?php

  /* Cargamos plantilla Twig */
  
  require_once "/usr/local/lib/php/vendor/autoload.php";

  $loader = new \Twig\Loader\FilesystemLoader('templates');
  $twig = new \Twig\Environment($loader);

  include_once ("BD.php");
  $error = 0;

  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nick = $_POST['userName'];
    $pass = $_POST['passwd'];
    $email = $_POST['email'];

    $baseDatos = new baseDatos();
    if ($baseDatos->registrar($nick, $pass, $email)){
      session_start();
      $_SESSION['usuario'] = $baseDatos->getUser($nick);
        header("Location: index.php");
    } else {
      
      header("Location: register.php");
      
      
    }
      

    exit();
  }
  
  echo $twig->render('login.html', []);
?>
