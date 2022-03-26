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

    // static function filtrarPorCategoria(nombreCategoria){
    //     $sentencia = "SELECT distinct categoria FROM productos";
    //     $result = mysqli_fetch_all(DB::query($sentencia),MYSQLI_ASSOC);
    //     $categorias = Array();
    //     foreach($result as $cat){
    //         array_push($categorias, $cat["categoria"]);
    //     }
    //     if(){

    //     }
    //     return json_encode($categoria);
    // }

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

?>