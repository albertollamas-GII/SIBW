<?php
  /* Cargamos plantilla Twig */
  
  require_once "/usr/local/lib/php/vendor/autoload.php";

  $loader = new \Twig\Loader\FilesystemLoader('templates');
  $twig = new \Twig\Environment($loader);

    include_once("BD.php");
    $baseDatos = new baseDatos();

    session_start();
    $user;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $rol = $_POST['cambio'];
        if ($rol != "default") {
            $nickk = $_POST['nick'];
            if ($baseDatos->cambiarRol($nickk, $rol) != true) {
                $error = "No tienes más de un superusuario. Deja al menos uno.";
            }
        }
    }

    if (isset($_SESSION['usuario'])) {
        $user = $baseDatos->getUser($_SESSION['usuario']['usname']);

        if($user['rango'] >= 4) {
            $usuarios = $baseDatos->getTodosUsuarios();
            echo $twig->render('listaUsuarios.html', ['user' => $user, 'usuarios' => $usuarios]);
        } else header("Location: index.php");
    } else {
        $errores['titulo'] = "403 - FORBIDDEN";
        $errores['descripcion'] = "Buen intento! No tienes permisos para acceder a esta página. Contacta con un administrador si crees que debes tener";
        echo $twig->render('error.html', ['errores' => $errores]);
    }


?>