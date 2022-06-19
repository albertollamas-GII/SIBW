<?php

  /* Cargamos plantilla Twig */
  
  require_once "/usr/local/lib/php/vendor/autoload.php";

  $loader = new \Twig\Loader\FilesystemLoader('templates');
  $twig = new \Twig\Environment($loader);
  
  function compruebaCorreo($email) {
    $hayArroba = strpos($email, '@');
    $hayPunto = strpos($email, '.');

    return ($hayArroba !== false && $hayPunto !== false);
  }

  include_once("BD.php");

  $baseDatos = new baseDatos();
  session_start();
  $user ="Empty";


  if ($_SERVER['REQUEST_METHOD'] === 'POST'){

      if (!empty($_POST['email'])){
        if (!compruebaCorreo($_POST['email'])) {
            $errores['titulo'] = "CORREO NO VALIDO";
            $errores['descripcion'] = "Ha habido un problema con el correo. Introduce uno válido (xxxxx@xxxxx.xx)";
            echo $twig->render('error.html', ['errores' => $errores]);
            exit();
        }
      }

        $usuario['usname'] = $_SESSION['usuario']['usname'];
        $oldPass = $_POST['contraseniaAntigua'];
        $newPass = $_POST['contraseniaNueva'];
        if (!empty($newPass)){
          if($newPass != $_POST['repeatContraseniaNueva']){
            $errores['titulo'] = "CONTRASEÑAS DISTINTAS";
            $errores['descripcion'] = "Error al repetir contraseña";
            echo $twig->render('error.html', ['errores' => $errores]);
            exit();
          }
        }
        if(!empty($oldPass)){
          if (!password_verify($oldPass,$user['passwd'])){
            $errores['titulo'] = "CONTRASEÑA ANTIGUA INCORRECTA";
            $errores['descripcion'] = "¿No recuerdas la contraseña? Haz un poco de memoria...";
            echo $twig->render('error.html', ['errores' => $errores]);
            exit();
          }
        }
        $usuario['pass'] = password_hash($newPass, PASSWORD_DEFAULT);
        $usuario['email'] = $_POST['email'];
        $usuario['name'] = $_POST['name'];
        $usuario['sname'] = $_POST['sname'];
        $usuario['cumple'] = $_POST['cumple'];
        $usuario['genero'] = $_POST['genero'];

        $baseDatos->actualizaUsuario($usuario);

        header("Location: perfil.php");


  }

    if (isset($_SESSION['usuario'])) {
      $user = $baseDatos->getUser($_SESSION['usuario']['usname']);
      echo $twig->render('perfil.html', ['user' => $user]);

  } else {
      $errores['titulo'] = "403 - FORBIDDEN";
      $errores['descripcion'] = "Buen intento! No tienes permisos para acceder a esta página. Contacta con un administrador si crees que debes tener";
      echo $twig->render('error.html', ['errores' => $errores]);
  }
?>