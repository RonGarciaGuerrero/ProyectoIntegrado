<!-- Creating New Session
========================== -->
<?php
//Se importa la clase usuario.
require_once(dirname(__FILE__).'/usuarios.php');

session_start();
/*session is started if you don't write this line can't use $_Session  global variable*/

$usuario=$_REQUEST["usuario"];
$contrasena=$_REQUEST["password"];

// intentar recuperar de la bbdd un usuario cuyo nombre de usuario sea $usuario y cuya contraseña sea $contrasena 
$objUsuario = Usuario::obtenerUsuario();

if($objUsuario == null){
    // print("el usuario no existe");
    header('Location: ../web/login.html?error=noexiste');
    //aqui se redirecciona a la pagina de logín con un parametro
}else{
    // print("el usuario si existe");
    $_SESSION['usuario']=$objUsuario;
    header('Location: ../web/admin.php');
    //cuando se valida el usuario y contraseña se redirecciona a la pagina de bienvenida de administración
}

?>
