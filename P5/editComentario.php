<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    include_once("BD.php");
    $baseDatos = new baseDatos();

    session_start();
    $user = "Empty";
    $id = $_GET['id'];
    $idPr = $_GET['pr'];

    if (isset($_SESSION['usuario'])) {
      $user = $baseDatos->getUser($_SESSION['usuario']['usname']);
    } else {
            $errores['titulo'] = "403 - FORBIDDEN";
            $errores['descripcion'] = "Buen intento! No tienes permisos para acceder a esta página. Contacta con un administrador si crees que debes tener";
            echo $twig->render('error.html', ['errores' => $errores]);
    }
    if($user['rango'] >= 2){
        if($idPr > 0)
            $prod = $idPr;
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $cuerpo = $_POST['cuerpo'];
            if(strpos($cuerpo, "Editado por moderador --") == false){
                $cuerpo = "Editado por moderador -- " . $cuerpo;
            }
            $baseDatos->updateComentario($id,$cuerpo);
            $comment = $baseDatos->getComentario($id);

            header("Location: index.php");
            exit();
        }

        if(isset($_GET['rmv'])){
            $baseDatos->deleteComentario($id);
            header("Location: producto.php?pr=" . $idPr);
            exit();
        }
        else{
            $comment = $baseDatos->getComentario($id);
            
            echo $twig->render('editComentario.html', ['coment' => $comment, 'user' => $user, 'idPr' => $idPr]);
        }
    }
    else {
            $errores['titulo'] = "403 - FORBIDDEN";
            $errores['descripcion'] = "Buen intento! No tienes permisos para acceder a esta página. Contacta con un administrador si crees que debes tener";
            echo $twig->render('error.html', ['errores' => $errores]);
        }
?>