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
                                  'precio' => $fila['precio'], 'cuerpo' => $fila['cuerpo'], 'idImagen' => $fila['idImagen']);
            }

            return $producto;
        }

        public function getImagenesProducto($idPr){
            if (!is_numeric($idPr)){
                exit("El id debe ser un numero. Te has equivocado.");
            }

            $stmt = $this->$conexion->prepare("SELECT idImagen,nombreImagen,href,src,class FROM Imagen NATURAL JOIN ImagenProducto WHERE idProducto = ?");
            $stmt->bind_param("i", $idPr);  //Pasamos el parametro
            $stmt->execute();   //Ejecutamos sentencia
            $res = $stmt->get_result(); //Almacenamos el resultado
            $imagenes = $res->fetch_all(MYSQLI_ASSOC);

            return $imagenes;
        }

        public function getComentariosProducto($idPr){
            if (!is_numeric($idPr)){
                exit("El id debe ser un numero. Te has equivocado.");
            }

            $stmt = $this->$conexion->prepare("SELECT autor,fecha,cuerpo FROM Comentarios WHERE idProducto = ?");
            $stmt->bind_param("i", $idPr);  //Pasamos el parametro
            $stmt->execute();   //Ejecutamos sentencia
            $res = $stmt->get_result(); //Almacenamos el resultado
            $comentarios = $res->fetch_all(MYSQLI_ASSOC);

            return $comentarios;
        }

        public function getImagenesPortada(){
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
    }


?>