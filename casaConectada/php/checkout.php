<?php

//Requerimiento de acceso a base de datos.
require_once(dirname(__FILE__).'/../PHP/DB.php');

class Pedido{ 

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

    static function guardarPedido(){
        $resultado=Array();
        $errores=Array(); 
        if(!array_key_exists("nombre",$_REQUEST) || $_REQUEST['nombre']== null || $_REQUEST['nombre']== "" ){
            array_push($errores,'el nombre es obligatorio');
        }

        //una vez terminadas las validaciones o se guarda el pedido o se devuelven errores
        if(count($errores)==0){
            $resultado['guardado']=true;
            //como no hay errores deberiamos guardar y meter en el resultado el id del pedido
        }else{
            $resultado['guardado']=false;
            $resultado['errores']=$errores;
        }
        return json_encode($resultado);
    }



}
if($_REQUEST['funcion']=='guardarPedido'){
    print(Pedido::guardarPedido());
}


?>