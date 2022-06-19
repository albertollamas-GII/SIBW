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
        if($user['rango'] >= 3){
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                
                $required = array('nombreProd', 'marca', 'precio','cuerpo');

                $errores;

                foreach($required as $field){
                    if(empty($_POST[$field])){
                        $errores['titulo'] = "NO PUEDE HABER CAMPOS VACIOS";
                        $errores['descripcion'] = "Por favor, para añadir un producto, introduzca un valor en todos los campos.";
                        echo $twig->render('error.html',['user', $user, 'errores' => $errores]);
                        exit();
                    }
                }

                if(!isset($_FILES['imagenPortada'])){
                    $errores['titulo'] = "DEBE AÑADIR UNA IMAGEN PARA LA PORTADA";
                    $errores['descripcion'] = "Por favor, para añadir un producto, introduzca un valor en todos los campos.";
                    echo $twig->render('error.html',['user', $user, 'errores' => $errores]);
                    exit();                
                }


                $file_name = $_FILES['imagenPortada']['name'];
                $file_name = str_replace(' ', '', $file_name);
                $file_tmp = $_FILES['imagenPortada']['tmp_name'];
                $file_ext = strtolower(end(explode('.',$_FILES['imagenPortada']['name'])));

                $extensions = array("jpeg","jpg","png","svg");

                if(!in_array($file_ext,$extensions)){
                    $errores['titulo'] = "IMAGEN NO VALIDA";
                    $errores['descripcion'] = "Extensión de imagen errónea - Debe ser jpeg, jpg, png o svg";
                    echo $twig->render('error.html',['user', $user, 'errores' => $errores]);
                    exit();
                }
                
                $path = 'images/' . $file_name;
                move_uploaded_file($file_tmp,"images/". $file_name);

                $imgId = $baseDatos->addImagen($_POST['nombreProd'],"",$path,$file_name);
                
                $producto = array();
                $producto['nombreProd'] = $_POST['nombreProd'];
                $producto['marca'] = $_POST['marca'];
                $producto['precio'] = $_POST['precio'];
                $producto['cuerpo'] = $_POST['cuerpo'];
                $producto['imagenPortada'] = $imgId['max'];

                $idPrAr = $baseDatos->addProducto($producto);
                $idPr = $idPrAr['max'];
                $href = "producto.php?pr=".$idPr;
                
                $baseDatos->actualizarHrefImagen($href, $imgId['max']);

                if(isset($_FILES['imagenesProducto'])){
                    $file_name = $_FILES['imagenesProducto']['name'];
                    $file_name = str_replace(' ', '', $file_name);
                    $file_tmp = $_FILES['imagenesProducto']['tmp_name'];
                    $file_ext = strtolower(end(explode('.',$_FILES['imagenesProducto']['name'])));

                    if(in_array($file_ext,$extensions)){
                        $path = 'images/' . $file_name;
                        move_uploaded_file($file_tmp,"images/". $file_name);
                        $imgId = $baseDatos->addOtraImagen($_POST['nombreProd'],"",$path,$file_name);
                        $baseDatos->addImagenAProducto($imgId['max'],$idPr);
                    }
                }

                if(!empty($_POST['etiquetas'])){
                    $stringTags = $_POST['etiquetas'];
                    $tags = explode(",",$stringTags);
                    $baseDatos->addEtiquetas($tags,$idPr);
                }

                header("Location: index.php");
                
                
                exit();
            }

            echo $twig->render('addProducto.html', ['user' => $user]);
            exit();
        } else {
            $errores['titulo'] = "403 - FORBIDDEN";
            $errores['descripcion'] = "Buen intento! No tienes permisos para acceder a esta página. Contacta con un administrador si crees que debes tener";
            echo $twig->render('error.html', ['errores' => $errores]);
        }
    } else {
            $errores['titulo'] = "403 - FORBIDDEN";
            $errores['descripcion'] = "Buen intento! No tienes permisos para acceder a esta página. Contacta con un administrador si crees que debes tener";
            echo $twig->render('error.html', ['errores' => $errores, 'user' => $user]);
        }
?>