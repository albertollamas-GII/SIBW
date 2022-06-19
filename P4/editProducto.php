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
    } else {
            $errores['titulo'] = "403 - FORBIDDEN";
            $errores['descripcion'] = "Buen intento! No tienes permisos para acceder a esta página. Contacta con un administrador si crees que debes tener";
            echo $twig->render('error.html', ['errores' => $errores]);
        }
    if ($user['rango'] >= 3) {
        if (isset($_GET['pr']))
            $idPr = $_GET['pr'];
        else 
            $idPr = -1;

        if(isset($_GET['rmv'])){
            $baseDatos->deleteProducto($idPr);
            header("Location: index.php");
            exit();
        }

        $producto = $baseDatos->getProducto($idPr);
        $etiquetas = $baseDatos->getEtiquetasProducto($idPr);
        $imagenes = $baseDatos->getImagenesProducto($idPr);
        $imagenesPortada = $baseDatos->getImagenesPortada();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if($_FILES['imagenPortada']['name'] != ''){
                $file_name = $_FILES['imagenPortada']['name'];
                $file_name = str_replace(' ', '', $file_name);
                $file_tmp = $_FILES['imagenPortada']['tmp_name'];
                $file_ext = strtolower(end(explode('.',$_FILES['imagenPortada']['name'])));

                $extensions = array("jpeg","jpg","png","svg");

                if(!in_array($file_ext,$extensions)){
                    $errores['imagenPortada'] = "Invalid file extension";
                    echo $twig->render('404.html',[]);
                    exit();
                }

                $path = 'images/'.$file_name;
                $imagen['nombreProd'] = $_POST['nombreProd'];
                $imagen['src'] = $path;
                $imagen['class'] = $file_name;
                $imagen['href'] = "/producto.php?pr=".$idPr;
                $baseDatos->updateImagenPortada($imagen,$idPr);
                move_uploaded_file($file_tmp,$path);
            }

            // if($_FILES['imagenesProducto']['name'] != ''){
            //     $file_name = $_FILES['imagenesProducto']['name'];
            //     $file_name = str_replace(' ', '', $file_name);
            //     $file_tmp = $_FILES['imagenesProducto']['tmp_name'];
            //     $file_ext = strtolower(end(explode('.',$_FILES['imagenesProducto']['name'])));

            //     $extensions = array("jpeg","jpg","png","svg");

            //     if(!in_array($file_ext,$extensions)){
            //         $errores['imagenesProducto'] = "Invalid file extension";
            //         echo $twig->render('404.html',[]);
            //         exit();
            //     }

            //     $path = 'images/'.$file_name;
            //     $imagen['nombreProd'] = $_POST['nombreProd'];
            //     $imagen['src'] = $path;
            //     $imagen['class'] = $file_name;
            //     $imagen['href'] = "/producto.php?pr=".$idPr;
            //     $baseDatos->updateImagenProducto($imagen,$idPr);
            //     move_uploaded_file($file_tmp,$path);
            // }

            if(isset($_FILES['addImagenesProducto'])){
                $file_name = $_FILES['addImagenesProducto']['name'];
                $file_name = str_replace(' ', '', $file_name);
                $file_tmp = $_FILES['addImagenesProducto']['tmp_name'];
                $file_ext = strtolower(end(explode('.',$_FILES['addImagenesProducto']['name'])));
                
                $extensions = array("jpeg","jpg","png","svg");

                if(in_array($file_ext,$extensions)){
                    $path = 'images/' . $file_name;
                    move_uploaded_file($file_tmp,"images/". $file_name);
                    $imgId = $baseDatos->addOtraImagen($_POST['nombreProd'],"",$path,$file_name);
                    $baseDatos->addImagenAProducto($imgId['max'],$idPr);
                }
            }

            $modified = array();
            $modified['id'] = $idPr;
            $modified['nombreProducto'] = $_POST['nombreProd'];
            $modified['marca'] = $_POST['marca'];
            $modified['precio'] = $_POST['precio'];
            $modified['cuerpo'] = $_POST['cuerpo'];
            $baseDatos->updateProducto($modified);
        
            if(!empty($_POST['etiquetas'])){
                $stringTags = $_POST['etiquetas'];
                $tags = explode(",",$stringTags);
                $baseDatos->addEtiquetas($tags,$idPr);
            }
            header("Location: producto.php?pr=".$idPr);
            exit();
        }
    } else {
            $errores['titulo'] = "403 - FORBIDDEN";
            $errores['descripcion'] = "Buen intento! No tienes permisos para acceder a esta página. Contacta con un administrador si crees que debes tener";
            echo $twig->render('error.html', ['errores' => $errores]);
        }
?>