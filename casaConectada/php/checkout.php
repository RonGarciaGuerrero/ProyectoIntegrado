<?php

//Requerimiento de acceso a base de datos.
require_once(dirname(__FILE__).'/../PHP/DB.php');

class Pedido{ 

    public $id;
    public $nombre;
    public $apellidos;
    public $email;
    public $direccion;
    public $cp;
    public $provincia;
    public $precioEnvio;
    public $estatus;
    public $productos;

    function __construct($id,$nombre,$apellidos,$email,$direccion,$cp,$provincia,$precioEnvio, $estatus, $productos){

        $this->id=$id;
        $this->nombre=$nombre;
        $this->apellidos=$apellidos;
        $this->email=$email;
        $this->direccion=$direccion;
        $this->cp=$cp;
        $this->provincia=$provincia;
        $this->precioEnvio=$precioEnvio;
        $this->estatus = $estatus;
        $this->productos=$productos;

    }

    static function guardarPedido(){
        $resultado=Array();
        $errores=Array(); 
        
        //print(json_encode($_REQUEST["idProductos"]));
        //print(json_encode($_REQUEST["cantidadProductos"]));
        
        //aqui se hacen las validaciones del formulario, el trim elimina espacio sal inicio y al final de la cadena

        if(!array_key_exists("nombre",$_REQUEST) || $_REQUEST['nombre'] == null || trim($_REQUEST['nombre'])== "" ){
            array_push($errores,'El nombre es obligatorio');
        }
        
        if(!array_key_exists("apellidos",$_REQUEST)|| $_REQUEST['apellidos'] == null || trim($_REQUEST['apellidos'])== "" ){
            array_push($errores,'El apellido es obligatorio');
        }

        if(!array_key_exists("email",$_REQUEST)|| $_REQUEST['email'] == null || trim($_REQUEST['email'])== "" ){
            array_push($errores,'El email es obligatorio');
        }else{//aqui viene el email pero se valida ahora que este escrito correctamente 
            $regex="/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/";
            if(!preg_match($regex, $_REQUEST['email'])){
                array_push($errores,'El formato de email es incorrecto');
            }
        }

        if(!array_key_exists("direccion",$_REQUEST)|| $_REQUEST['direccion'] == null || trim($_REQUEST['direccion'])== "" ){
            array_push($errores,'La dirección es obligatoria');
        }

        if(!array_key_exists("cp",$_REQUEST)|| $_REQUEST['cp'] == null || trim($_REQUEST['cp'])== "" ){
            array_push($errores,'El código postal es obligatorio');
        }else{
            $regex="/^(?:0[1-9]|[1-4]\d|5[0-2])\d{3}$/";
            if(!preg_match($regex, $_REQUEST['cp'])){
                array_push($errores,'El formato de codigo postal es incorrecto');
            }
        }

        if(!array_key_exists("provincia",$_REQUEST)|| $_REQUEST['provincia'] == null || trim($_REQUEST['provincia'])== "" ){
            array_push($errores,'La provincia es obligatoria');
        }else{
            $regex="/^[0-9]{2}$/";
            
            if(!preg_match($regex, $_REQUEST['provincia'])){
                array_push($errores,'La provincia tiene un formato incorrecto');
            }else{//la provincia tiene 2 digitos
                $provinciaInt=intval($_REQUEST['provincia']);
                if($provinciaInt<1||$provinciaInt>52){
                    array_push($errores,'El número de provincia es incorrecto');
                }
            }
        }

        //una vez terminadas las validaciones o se guarda el pedido o se devuelven errores
        if(count($errores)==0){
            
            //como no hay errores deberiamos guardar y meter en el resultado el id del pedido
            $resultadoBBDD=Pedido::guardarPedidoBbdd();
            $resultado=array_merge($resultadoBBDD,$resultado);
        }else{
            $resultado['guardado']=false;
            $resultado['errores']=$errores;
            

        }
        return json_encode($resultado);
    }

    static function guardarPedidoBbdd(){
        $errores=[];//se crea un array que contendrá los errores
        $nombre=$_REQUEST['nombre'];
        $apellidos=$_REQUEST['apellidos'];
        $email=$_REQUEST['email'];
        $direccion=$_REQUEST['direccion'];
        $cp=$_REQUEST['cp'];
        $provincia=$_REQUEST['provincia'];
        $precio_envio=5;//de momento se pone el precio de envío directamente
        $idPedido = null;
        try{//se meten los datos del pedido en la bbdd
            $sentencia = " INSERT INTO pedidos (id,nombre,apellidos,email,direccion,codigo_postal,provincia,precio_envio) VALUES ('','$nombre','$apellidos','$email','$direccion','$cp','$provincia','$precio_envio')";

            $idPedido=DB::insert($sentencia);
            

            //se itera sobre los productos y se insertan uno a uno

            for($i=0;$i<count($_REQUEST["idProductos"]);$i++){
                //print("Del producto ".$_REQUEST["idProductos"][$i]." Se han pedido ".$_REQUEST["cantidadProductos"][$i]." unidades. ");
                $cant=$_REQUEST["cantidadProductos"][$i];
                $idProd=$_REQUEST["idProductos"][$i];
                $sentencia = "INSERT INTO pedidos_productos(id_pedido,id_producto,cantidad,precio_unitario)
                VALUES ($idPedido,$idProd,$cant,(SELECT precio FROM productos WHERE id = $idProd))";
                DB::query($sentencia);
                $sentencia = "UPDATE productos SET unidades = unidades - $cant WHERE id = $idProd";
                DB::query($sentencia);
                //QUEDA PENDIENTE MEJORAR EL CODIGO PARA QUE NO QUEDEN LAS UNIDADES EN NEGATIVO
                
            }


        }catch(Exception $e){
            $errores[]=$e->getMessage();//añado el mensaje del error
        }
        //se imprime un objeto json haya errores o no
        if(sizeof( $errores) > 0){
            return array('guardado'=>false,'mensaje'=>$errores);
        }else{
            return array('guardado'=>true,'idPedido'=>$idPedido);
        }
    }

    static function mostrarPedidos(){
        
        
        $sentencia = "SELECT * FROM pedidos";
        
        

        $result = mysqli_fetch_all(DB::query($sentencia),MYSQLI_ASSOC);
        $pedidos = Array();
        foreach($result as $ped){
            array_push($pedidos, new Pedido($ped["id"],$ped["nombre"],$ped["apellidos"],$ped["email"],$ped["direccion"],$ped["codigo_postal"],$ped["provincia"],$ped["precio_envio"],$ped['estatus'],null));
        }
        return json_encode($pedidos);
    
    }

    static function eliminarPedidoBbdd(){
        $idEliminar = $_REQUEST['idEliminar'];
        
        $sentencia = "DELETE FROM pedidos_productos WHERE id_pedido=$idEliminar";
        DB::query($sentencia);
        
        $sentencia = "DELETE FROM pedidos WHERE id=$idEliminar";
        DB::query($sentencia);
    
    }

    static function obtenerPedido($idPedido){
        $sentencia = "SELECT * FROM pedidos WHERE id=$idPedido";
        $result = mysqli_fetch_array(DB::query($sentencia),MYSQLI_ASSOC);
        $productos = Array();

        $sentenciaDetalle = "SELECT * FROM pedidos_productos JOIN productos ON pedidos_productos.id_producto = productos.id WHERE pedidos_productos.id_pedido=$idPedido";

        $queryDetalle = DB::query($sentenciaDetalle);
        while( $resultDetalle = mysqli_fetch_array($queryDetalle, MYSQLI_ASSOC)){
            array_push($productos, $resultDetalle);
        }

        return new Pedido($result['id'],$result['nombre'],$result['apellidos'],$result['email'],$result['direccion'],$result['codigo_postal'],$result['provincia'],$result['precio_envio'], $result['estatus'], $productos);

    }

    static function actualizarEstado(){
        $errores=[];//se crea un array que contendrá los errores
        $estatus=$_REQUEST['estatus'];
        $id=$_REQUEST['id'];
        
        try{//se meten los datos del pedido en la bbdd
            $sentencia = "UPDATE pedidos SET estatus = '$estatus' WHERE id = $id";
    
            DB::query($sentencia);
    
        }catch(Exception $e){
            $errores[]=$e->getMessage();//añado el mensaje del error
        }

        //se imprime un objeto json haya errores o no
        if(sizeof( $errores) > 0){
            return json_encode(array('guardado'=>false,'mensaje'=>$errores));
        }else{
            return json_encode(array('guardado'=>true,'idPedido'=>$id));
        }
    }

}




//cuando esta clase se usa directamente sin llamada AJAX los if dan error porque el parametro funcion no esta en el request, para eso se añade el array_key_exist 
if(array_key_exists('funcion',$_REQUEST) && $_REQUEST['funcion']=='guardarPedido'){
    print(Pedido::guardarPedido());
}

if(array_key_exists('funcion',$_REQUEST) && $_REQUEST['funcion']=='mostrarPedidos'){
    print(Pedido::mostrarPedidos());
}

if(array_key_exists('funcion',$_REQUEST) && $_REQUEST['funcion']=='eliminarPedidoBbdd'){
    print(Pedido::eliminarPedidoBbdd());
}


if(array_key_exists('funcion',$_REQUEST) && $_REQUEST['funcion']=='actualizar'){
    print(Pedido::actualizarEstado());
}

?>