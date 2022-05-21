<?php

//Requerimiento de acceso a base de datos.
require_once(dirname(__FILE__).'/../PHP/DB.php');

class Producto{ 

    public $id;
    public $nombre;
    public $marca;
    public $categoria;
    public $unidades;
    public $resumen;
    public $descripcion;
    public $precio;

    function __construct($id,$nombre,$marca,$categoria,$unidades,$resumen,$descripcion,$precio){

        $this->id=$id;
        $this->nombre=$nombre;
        $this->marca=$marca;
        $this->categoria=$categoria;
        $this->unidades=$unidades;
        $this->resumen=$resumen;
        $this->descripcion=$descripcion;
        $this->precio=$precio;

    }

    static function obtenerProductos(){
        
        if(array_key_exists("categoria",$_REQUEST)){//se ha pasado una categoria para filtrar
            $categoriaSeleccionada = $_REQUEST["categoria"];
            $sentencia = "SELECT * FROM productos where categoria ='$categoriaSeleccionada' ";
        }else{
            $sentencia = "SELECT * FROM productos";
        }
        

        $result = mysqli_fetch_all(DB::query($sentencia),MYSQLI_ASSOC);
        $productos = Array();
        foreach($result as $prod){
            array_push($productos, new Producto($prod["id"],$prod["nombre"],$prod["marca"],$prod["categoria"],$prod["unidades"],$prod["resumen"],$prod["descripcion"],$prod["precio"]));
        }
        return json_encode($productos);
    }

    static function obtenerCategorias(){
        $sentencia = "SELECT distinct categoria FROM productos";
        $result = mysqli_fetch_all(DB::query($sentencia),MYSQLI_ASSOC);
        $categorias = Array();
        foreach($result as $cat){
            array_push($categorias, $cat["categoria"]);
        }
        return json_encode($categorias);
    }

    static function obtenerDetalleProducto(){
        $idProd=$_REQUEST['idPro'];
        $sentencia = "SELECT * FROM productos where id = $idProd";
        $result = mysqli_fetch_all(DB::query($sentencia),MYSQLI_ASSOC);

        return json_encode($result[0]);//el primer resultado
    }

    static function guardarProducto(){
        $resultado=Array();
        $errores=Array(); 
        
         
        //aqui se hacen las validaciones del formulario, el trim elimina espacio sal inicio y al final de la cadena

        if(!array_key_exists("nombre",$_REQUEST) || $_REQUEST['nombre'] == null || trim($_REQUEST['nombre'])== "" ){
            array_push($errores,'El nombre es obligatorio');
        }
        
        if(!array_key_exists("marca",$_REQUEST)|| $_REQUEST['marca'] == null || trim($_REQUEST['marca'])== "" ){
            array_push($errores,'La marca es obligatoria');
        }

        if(!array_key_exists("categoria",$_REQUEST)|| $_REQUEST['categoria'] == null || trim($_REQUEST['categoria'])== "" ){
            array_push($errores,'La categoria es obligatoria');
        }

        if(!array_key_exists("cantidad",$_REQUEST)|| $_REQUEST['cantidad'] == null || trim($_REQUEST['cantidad'])== "" ){
            array_push($errores,'La cantidad son obligatorias');
        }

        if(!array_key_exists("resumen",$_REQUEST)|| $_REQUEST['resumen'] == null || trim($_REQUEST['resumen'])== "" ){
            array_push($errores,'El resumen es obligatorio');
        }

        if(!array_key_exists("descripcion",$_REQUEST)|| $_REQUEST['descripcion'] == null || trim($_REQUEST['descripcion'])== "" ){
            array_push($errores,'La descripcion es obligatoria');
        }

        if(!array_key_exists("precio",$_REQUEST)|| $_REQUEST['precio'] == null || trim($_REQUEST['precio'])== "" ){
            array_push($errores,'El precio es obligatorio');
        }else{
            if($_REQUEST['precio']<0){
                array_push($errores,'El precio no puede ser negativo');   
            }
        }

        /*if(!array_key_exists("file1",$_REQUEST)){
            array_push($errores,'Debe agregar tres fotos');
        }*/

        //una vez terminadas las validaciones o se guarda el producto o se devuelven errores
        if(count($errores)==0){
            
            //como no hay errores deberiamos guardar y meter en el resultado el id del producto
            $resultadoBBDD=Producto::guardarProductoBbdd();
            $resultado=array_merge($resultadoBBDD,$resultado);
        }else{
            $resultado['guardado']=false;
            $resultado['errores']=$errores;
            

        }


        return json_encode($resultado);
    }

    static function guardarProductoBbdd(){
        $errores=[];//se crea un array que contendrá los errores

        $nombre=$_REQUEST['nombre'];
        $marca=$_REQUEST['marca'];
        $categoria=$_REQUEST['categoria'];
        $unidades=$_REQUEST['cantidad'];
        $resumen=$_REQUEST['resumen'];
        $descripcion=$_REQUEST['descripcion'];
        $precio=$_REQUEST['precio'];

        if(isset($_FILES['file1']['name'])){

            /* Getting file name */
            $filename = $_FILES['file1']['name'];
         
            /* Location */
            $location = '../img/productos/'.$filename;
            $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
            $imageFileType = strtolower($imageFileType);
         
            /* Valid extensions */
            $valid_extensions = array("jpg","jpeg","png");
         
            $response = 0;
            /* Check file extension */
            if(in_array(strtolower($imageFileType), $valid_extensions)) {
               /* Upload file */
               if(move_uploaded_file($_FILES['file1']['tmp_name'],$location)){
                  $response = $location;
               }
            }
        }

        if(isset($_FILES['file2']['name'])){

            /* Getting file name */
            $filename = $_FILES['file2']['name'];
         
            /* Location */
            $location = $filename;
            $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
            $imageFileType = strtolower($imageFileType);
         
            /* Valid extensions */
            $valid_extensions = array("jpg","jpeg","png");
         
            $response = 0;
            /* Check file extension */
            if(in_array(strtolower($imageFileType), $valid_extensions)) {
               /* Upload file */
               if(move_uploaded_file($_FILES['file2']['tmp_name'],$location)){
                  $response = $location;
               }
            }
        }

        if(isset($_FILES['file3']['name'])){

            /* Getting file name */
            $filename = $_FILES['file3']['name'];
         
            /* Location */
            $location = $filename;
            $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
            $imageFileType = strtolower($imageFileType);
         
            /* Valid extensions */
            $valid_extensions = array("jpg","jpeg","png");
         
            $response = 0;
            /* Check file extension */
            if(in_array(strtolower($imageFileType), $valid_extensions)) {
               /* Upload file */
               if(move_uploaded_file($_FILES['file3']['tmp_name'],$location)){
                  $response = $location;
               }
            }
        }
        $id = null;
        $file1 = $_FILES['file1']['name'];
        $file2 = $_FILES['file2']['name'];
        $file3 = $_FILES['file3']['name'];
        try{//se meten los datos del producto en la bbdd
            $sentencia = " INSERT INTO productos (id,nombre,marca,categoria,unidades,resumen,descripcion,precio, file1, file2, file3) VALUES ('','$nombre','$marca','$categoria','$unidades','$resumen','$descripcion','$precio', '$file1', '$file2', '$file3')";

            $id=DB::insert($sentencia);
         

        }catch(Exception $e){
            $errores[]=$e->getMessage();//añado el mensaje del error
        }
        //se imprime un objeto json haya errores o no
        if(sizeof( $errores) > 0){
            return array('guardado'=>false,'mensaje'=>$errores);
        }else{
            return array('guardado'=>true,'id'=>$id);
        }
    }

    static function eliminarProductoBbdd(){
        $idEliminar = $_REQUEST['idEliminar'];
        $sentencia = "DELETE FROM productos WHERE id=$idEliminar";
        DB::query($sentencia);

    }

}
if($_REQUEST['funcion']=='obtenerCategorias'){
    print(Producto::obtenerCategorias());
}
if($_REQUEST['funcion']=='obtenerProductos'){
    print(Producto::obtenerProductos());//imprime todos los productos en JSON, llamada a obtenerProductos()
}
if($_REQUEST['funcion']=='obtenerDetalleProducto'){
    print(Producto::obtenerDetalleProducto());//imprime un producto identificado por id
}
if($_REQUEST['funcion']=='guardarProducto'){
    print(Producto::guardarProducto());
}
if($_REQUEST['funcion']=='eliminarProductoBbdd'){
    Producto::eliminarProductoBbdd();
}

?>