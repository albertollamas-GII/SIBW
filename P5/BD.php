<?php

    class baseDatos {
        private $conexion;
        
        public function __construct(){
            $this->$conexion = new mysqli("mysql", "albertolg", "practicas,sibw", "SIBW");
            if ($this->$conexion->connect_errno){
                exit("Fallo al conectar"); //No se muestra error porque seria ilogico mostrarlo al resto de personas en caso de inyecciones
            }
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->$conexion->set_charset("utf8mb4");
        }

        public function getProducto($idPr){
            if (!is_numeric($idPr)){
                exit("El id debe ser un numero. Te has equivocado.");
            }

            //Para prevenir inyecciones de codigo
            $stmt = $this->$conexion->prepare("SELECT * FROM Producto WHERE idProducto = ?"); //Preparamos sentencia SQL
            $stmt->bind_param("i", $idPr);  //Pasamos el parametro
            $stmt->execute();   //Ejecutamos sentencia
            $res = $stmt->get_result(); //Almacenamos el resultado

            // $producto = array('nombreProducto' => "Producto no existente", 'marca' => "Sin marca" );

            if ($res->num_rows > 0){
                $fila = $res->fetch_assoc();
                $producto = array('idProducto'=>$fila['idProducto'], 'nombreProducto'=>$fila['nombreProducto'], 'marca'=>$fila['marca'],
                                  'precio' => $fila['precio'], 'cuerpo' => $fila['cuerpo'], 'idImagen' => $fila['idImagen'], 'publicado' => $fila['publicado']);
            }

            return $producto;
        }

        public function getImagenesProducto($idPr){
            if (!is_numeric($idPr)){
                exit("El id debe ser un numero. Te has equivocado.");
            }

            $stmt = $this->$conexion->prepare("SELECT idImagen,nombreImagen,href,src,class FROM OtrasImagenes NATURAL JOIN ImagenProducto WHERE idProducto = ?");
            $stmt->bind_param("i", $idPr);  //Pasamos el parametro
            $stmt->execute();   //Ejecutamos sentencia
            $res = $stmt->get_result(); //Almacenamos el resultado
            $imagenes = $res->fetch_all(MYSQLI_ASSOC);

            return $imagenes;
        }

        public function getImagenPortada($idProducto){
            if (!is_numeric($idProducto)){
                exit("El id debe ser un numero. Te has equivocado.");
            }

            $stmt = $this->$conexion->prepare("SELECT idImagen,nombreImagen,href,src,class FROM Imagen WHERE idImagen = ?");
            $stmt->bind_param("i", $idProducto);  //Pasamos el parametro
            $stmt->execute();   //Ejecutamos sentencia
            $res = $stmt->get_result(); //Almacenamos el resultado
            $imagen = $res->fetch_all(MYSQLI_ASSOC);

            return $imagen;
        }

        public function getComentariosProducto($idPr){
            if (!is_numeric($idPr)){
                exit("El id debe ser un numero. Te has equivocado.");
            }

            $stmt = $this->$conexion->prepare("SELECT idComentario,usname,fecha,cuerpo FROM Comentarios WHERE idProducto = ?");
            $stmt->bind_param("i", $idPr);  //Pasamos el parametro
            $stmt->execute();   //Ejecutamos sentencia
            $res = $stmt->get_result(); //Almacenamos el resultado
            $comentarios = $res->fetch_all(MYSQLI_ASSOC);

            return $comentarios;
        }

        public function getImagenesPortada(){
            $res = $this->$conexion->query("SELECT Imagen.idImagen, Imagen.nombreImagen, Imagen.href, Imagen.src, Imagen.class FROM Imagen NATURAL JOIN Producto WHERE (Imagen.idImagen = Producto.idImagen and publicado = 1) ORDER BY idImagen ASC");
            $imagenes = $res->fetch_all(MYSQLI_ASSOC);

            return $imagenes;
        }

        public function getImagenesPortadaTodas(){
            $res = $this->$conexion->query("SELECT Imagen.idImagen, Imagen.nombreImagen, Imagen.href, Imagen.src, Imagen.class FROM Imagen ORDER BY idImagen ASC");
            $imagenes = $res->fetch_all(MYSQLI_ASSOC);

            return $imagenes;
        }

        public function getPalabrasBaneadas(){
            $res = $this->$conexion->query("SELECT palabra FROM PalabraProhibida;");
            $palabras = array();

            while($fila = $res->fetch_assoc()){
                array_push($palabras, $fila['palabra']);
            }

            return $palabras;
        }

        public function compruebaInicioSesion($usuario, $passwd){
            $stmt = $this->$conexion->prepare("SELECT usname, passwd, rango FROM Usuario WHERE usname = ?");
            $stmt->bind_param("s", $usuario);  //Pasamos el parametro
            $stmt->execute();   //Ejecutamos sentencia
            $res = $stmt->get_result(); //Almacenamos el resultado
            
            if ($res->num_rows > 0){
                $almacenado = $res->fetch_assoc();
                if (password_verify($passwd, $almacenado['passwd']))
                    return true;
                else
                    return false;
            } else 
                return false;
        }

        public function getUser($username) {
            $stmt = $this->$conexion->prepare("SELECT * FROM Usuario WHERE usname = ?");
            $stmt->bind_param("s", $username);  //Pasamos el parametro
            $stmt->execute();   //Ejecutamos sentencia
            $res = $stmt->get_result(); //Almacenamos el resultado
            $user = $res->fetch_assoc();
            return $user;
        }

        public function registrar($user, $pass, $email) {
            if (empty($user) or empty($pass) or empty($email)) {
                return false;
            }

            
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                if ($this->$conexion->query("INSERT INTO Usuario(usname,passwd,email,rango,superusuario) VALUES ('$user', '$hash', '$email', 1,0)"))
                    return true;
                else 
                    return false;
            
        }

        public function getTodosUsuarios() {
            $res = $this->$conexion->query("SELECT usname, rango, superusuario FROM Usuario ORDER BY usname ASC;");
            $usuarios = $res->fetch_all(MYSQLI_ASSOC);
            return $usuarios;
        }

        public function cambiarRol($nick, $rol) {
            $rango = -1;
            
            if ($rol == "Superusuario")
                $rango = 4;
            else if ($rol == "Gestor de sitio")
                $rango = 3;
            else if ($rol == "Moderador")
                $rango = 2;
            else if ($rol == "Usuario")
                $rango = 1;
            
            $contador = $this->$conexion->query("SELECT count(rango) from Usuario where rango = 4");
            if ($rango == 4) $contador = $contador + 1;
            if (($contador == 1 and $rango != 4) or ($contador >= 1 and $rango >= 1)) {
                $this->$conexion->query("UPDATE Usuario SET rango = $rango WHERE usname = '$nick'");
                return true;
            } else {
                return false;
            }
        }

        public function getEtiquetasProducto($idPr) {
            if (!is_numeric($idPr)){
                exit("El id debe ser un numero. Te has equivocado.");
            }

            $res = $this->$conexion->query("SELECT etiqueta FROM Etiqueta WHERE idProducto = $idPr;");
            $etiquetas = array();

            while($fila = $res->fetch_assoc()){
                array_push($etiquetas, $fila['etiqueta']);
            }

            return $etiquetas;
        }

        public function getTodosComentarios() {
            $res = $this->$conexion->query("SELECT idComentario, usname, fecha, cuerpo, idProducto FROM Comentarios ORDER BY idProducto ASC;");
            $comentarios = $res->fetch_all(MYSQLI_ASSOC);
            return $comentarios;
        }

        public function deleteProducto($idPr){
            //Guardamos la imagen de la portada del producto para eliminarla posteriormente de la tabla Imagen
            $stmt1 = $this->$conexion->prepare("SELECT idImagen FROM Producto WHERE idProducto = ?");
            $stmt1->bind_param("i",$idPr);
            $stmt1->execute();
            $res = $stmt1->get_result();
            $idImgPortada = $res->fetch_assoc();
            $idImgPortada = $idImgPortada['idImagen'];

            //Lo mismo con las imagenes del cuerpo del producto
            $stmt2 = $this->$conexion->prepare("SELECT idImagen FROM ImagenProducto WHERE idProducto = ?");
            $stmt2->bind_param("i",$idPr);
            $stmt2->execute();
            $res = $stmt2->get_result();
            $idImgsCuerpo = $res->fetch_all(MYSQLI_ASSOC);

            //Borramos los comentarios del producto
            $stmt3 = $this->$conexion->prepare("DELETE FROM Comentarios WHERE idProducto = ?");
            $stmt3->bind_param("i",$idPr);
            $stmt3->execute();

            //Borramos la asociacion del producto y sus imagenes del cuerpo
            $stmt4 = $this->$conexion->prepare("DELETE FROM ImagenProducto WHERE idProducto = ?");
            $stmt4->bind_param("i",$idPr);
            $stmt4->execute();

            //Borramos la asociación entre el producto y sus etiquetas
            $stmt5 = $this->$conexion->prepare("DELETE FROM Etiqueta WHERE idProducto = ?");
            $stmt5->bind_param("i",$idPr);
            $stmt5->execute();

            //Borramos el producto en si
            $stmt6 = $this->$conexion->prepare("DELETE FROM Producto WHERE idProducto = ?");
            $stmt6->bind_param("i",$idPr);
            $stmt6->execute();

            //Y usando los ids guardados previamente, borramos las imágenes de su tabla y del servidor
            $res = $this->$conexion->query("SELECT src FROM Imagen WHERE idImagen=".$idImgPortada);
            $path = $res->fetch_assoc();
            unlink($_SERVER['DOCUMENT_ROOT']."/".$path['src']);
            $this->$conexion->query("DELETE FROM Imagen WHERE idImagen=".$idImgPortada);

            foreach($idImgsCuerpo as $imgCuerpo){
                $sql = "SELECT src FROM OtrasImagenes WHERE idImagen=".$imgCuerpo['idImagen'];
                $res = $this->$conexion->query($sql);
                $path = $res->fetch_assoc();
                unlink($_SERVER['DOCUMENT_ROOT']."/".$path['src']);
                $sql = "DELETE FROM OtrasImagenes WHERE idImagen=".$imgCuerpo['idImagen'];
                $this->$conexion->query($sql);
            }
        }

        public function addEtiquetas($tags,$idPr){
            foreach($tags as $tag){
                $stmt = $this->$conexion->prepare("INSERT INTO Etiqueta VALUES(?,?)");
                $stmt->bind_param("si",$tag,$idPr);
                $stmt->execute();
            }
        }

        public function updateProducto($producto){
            $stmt = $this->$conexion->prepare("UPDATE Producto SET nombreProducto = ?, marca = ?, precio = ?, cuerpo = ?, publicado = ? WHERE idProducto = ?");
            $stmt->bind_param("ssssii", $producto['nombreProducto'], $producto['marca'], $producto['precio'], $producto['cuerpo'], $producto['publicado'], $producto['id']);
            $stmt->execute();
        }

        public function updateImagenPortada($imagen,$idPr){
            $stmt = $this->$conexion->prepare("SELECT Imagen.idImagen,src FROM Imagen JOIN Producto ON Producto.idImagen = Imagen.idImagen WHERE idProducto = ?");
            $stmt->bind_param("i",$idPr);
            $stmt->execute();
            $res = $stmt->get_result();
            $oldImg = $res->fetch_assoc();

            $stmt2 = $this->$conexion->prepare("INSERT INTO Imagen(nombreImagen,href,src,class) VALUES (?,?,?,?)");
            $stmt2->bind_param("ssss",$imagen['nombreProd'], $imagen['href'],$imagen['src'],$imagen['class']);
            $stmt2->execute();
            
            $res = $this->$conexion->query("SELECT MAX(idImagen)max FROM Imagen");
            $idNew = $res->fetch_assoc();
            $idNew = $idNew['max'];

            $stmt3 = $this->$conexion->prepare("UPDATE Producto SET idImagen = ? WHERE idProducto = ?");
            $stmt3->bind_param("ii",$idNew,$idPr);
            $stmt3->execute();

            $this->$conexion->query("DELETE FROM Imagen WHERE idImagen=".$oldImg['idImagen']);
            unlink($oldImg['src']);
        }

        public function updateImagenProducto($imagen,$idPr){
            $stmt = $this->$conexion->prepare("SELECT Imagen.idImagen,src FROM Imagen JOIN Producto ON Producto.idImagen = Imagen.idImagen WHERE idProducto = ?");
            $stmt->bind_param("i",$idPr);
            $stmt->execute();
            $res = $stmt->get_result();
            $oldImg = $res->fetch_assoc();

            $stmt2 = $this->$conexion->prepare("INSERT INTO Imagen(nombreImagen,href,src,class) VALUES (?,?,?,?)");
            $stmt2->bind_param("ssss",$imagen['nombreProd'], $imagen['href'],$imagen['src'],$imagen['class']);
            $stmt2->execute();
            
            $res = $this->$conexion->query("SELECT MAX(idImagen)max FROM Imagen");
            $idNew = $res->fetch_assoc();
            $idNew = $idNew['max'];

            $stmt3 = $this->$conexion->prepare("UPDATE Producto SET idImagen = ? WHERE idProducto = ?");
            $stmt3->bind_param("ii",$idNew,$idPr);
            $stmt3->execute();

            $this->$conexion->query("DELETE FROM Imagen WHERE idImagen=".$oldImg['idImagen']);
            unlink($oldImg['src']);
        }


        public function deleteEtiqueta($tag,$idPr){
            $stmt = $this->$conexion->prepare("DELETE FROM Etiqueta WHERE etiqueta = ? AND idProducto = ?");
            $stmt->bind_param("si",$tag,$idPr);
            $stmt->execute();
        }

        public function updateComentario($idCo, $cuerpo){
            $stmt = $this->$conexion->prepare("UPDATE Comentarios SET cuerpo = ? WHERE idComentario = ?");
            $stmt->bind_param("si",$cuerpo,$idCo);
            $stmt->execute();
        }

        public function getComentario($idCo){
            $stmt = $this->$conexion->prepare("SELECT idComentario,usname,fecha,cuerpo FROM Comentarios WHERE idComentario = ?");
            $stmt->bind_param("i",$idCo);
            $stmt->execute();
            $res = $stmt->get_result();
            $comentario = $res->fetch_assoc();
            return $comentario;
        }

        public function deleteComentario($idCo) {
            $stmt = $this->$conexion->prepare("DELETE FROM Comentarios WHERE idComentario = ?");
            $stmt->bind_param("i",$idCo);
            $stmt->execute();
        }

        public function guardarComentario($uname,$cuerpo,$idPr){
            $stmt = $this->$conexion->prepare("INSERT INTO Comentarios(usname,fecha,cuerpo,idProducto) VALUES (?,now(),?,?)");
            $stmt->bind_param("ssi",$uname,$cuerpo,$idPr);
            $stmt->execute();
        }

        public function actualizaUsuario($usuario){
            $stmt = $this->$conexion->prepare("UPDATE Usuario SET email = ?, passwd = ?, nombre = ?, apellido = ?, sexo = ?, fechaNacimiento = ? WHERE usname = ?");
            $stmt->bind_param("sssssss", $usuario['email'], $usuario['pass'], $usuario['name'], $usuario['sname'], $usuario['genero'], $usuario['cumple'], $usuario['usname']);
            $stmt->execute();
        }
        
        public function addImagen($nombreImagen,$href,$src,$class){
            $stmt = $this->$conexion->prepare("INSERT INTO Imagen(nombreImagen,href,src,class) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss",$nombreImagen,$href,$src,$class);
            $stmt->execute();
            $res = $this->$conexion->query("SELECT MAX(idImagen)max FROM Imagen");
            $id = $res->fetch_assoc();
            return $id;
        }

        public function addProducto($producto){
            $stmt = $this->$conexion->prepare("INSERT INTO Producto(nombreProducto,marca,precio,cuerpo,idImagen) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss",$producto['nombreProd'],$producto['marca'],$producto['precio'],$producto['cuerpo'],$producto['imagenPortada']);
            $stmt->execute();
            $res = $this->$conexion->query("SELECT MAX(idProducto)max FROM Producto");
            $id = $res->fetch_assoc();
            return $id;
        }

        public function actualizarHrefImagen($href,$id){
            $stmt = $this->$conexion->prepare("UPDATE Imagen SET href = ? WHERE idImagen = ?");
            $stmt->bind_param("si",$href,$id);
            $stmt->execute();
        }

        public function addOtraImagen($nombreImagen,$href,$src,$class){
            $stmt = $this->$conexion->prepare("INSERT INTO OtrasImagenes(nombreImagen,href,src,class) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss",$nombreImagen,$href,$src,$class);
            $stmt->execute();
            $res = $this->$conexion->query("SELECT MAX(idImagen)max FROM OtrasImagenes");
            $id = $res->fetch_assoc();
            return $id;
        }

        public function addImagenAProducto($img,$pr){
            $stmt = $this->$con->prepare("INSERT INTO ImagenProducto VALUES (?,?)");
            $stmt->bind_param("ii",$pr,$img);
            $stmt->execute();
        }

        public function buscarProductos($str, $gestor) {
            if ($gestor){
                $sql = "SELECT idProducto, nombreProducto FROM Producto WHERE (nombreProducto LIKE ? or cuerpo LIKE ?)";
            } else {
                $sql = "SELECT idProducto, nombreProducto FROM Producto WHERE (publicado = 1 AND (nombreProducto LIKE ? or cuerpo LIKE ?))";
            }

            $str = "%".$str."%";
            $stmt = $this->$con->prepare($sql);
            $stmt->bind_param("ss", $str, $str);
            $stmt->execute();
            $res = $stmt->get_result();
            
            $datos = $res->fetch_all(MYSQLI_ASSOC);
            return $datos;
        }

    }


?>