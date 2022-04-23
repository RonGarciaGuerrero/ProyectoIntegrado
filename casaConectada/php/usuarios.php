<?php
require_once(dirname(__FILE__).'/../PHP/DB.php');

class Usuario{ 

    public $id;
    public $nombre;
    public $apellidos;
    public $contrasena;
    

    function __construct($id,$nombre,$apellidos,$contrasena){

        $this->id=$id;
        $this->nombre=$nombre;
        $this->apellidos=$apellidos;
        $this->contrasena=$contrasena;

    }

    static function obtenerUsuario(){
        $usuario=$_REQUEST["usuario"];
        $contrasena=$_REQUEST["password"];
        $sentencia = "SELECT * FROM usuarios WHERE id='$usuario' AND contrasena='$contrasena'";
        
        $result = mysqli_fetch_all(DB::query($sentencia),MYSQLI_ASSOC);
        if(count($result)>0){
            return new Usuario($result[0]['id'],$result[0]['nombre'],$result[0]['apellidos'],$result[0]['contrasena']);
        }else{
            return null;//si no hay usuarios que cumplan las condiciones se devuelve null
        }
        
    }
    
}

?>