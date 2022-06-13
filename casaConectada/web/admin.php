<?php 
  //Se importa la clase usuario.
  require_once(dirname(__FILE__).'/../PHP/usuarios.php');
  session_start();
  $usuario = $_SESSION['usuario'];
  if($usuario == null){
    header('Location: ../web/login.html?error=noautenticado');//se redirige a login
    die();
  }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Casa Conectada Domotica</title>
        <link rel="icon" type="image/x-icon" href="../img/miniLogo.PNG">
        <script src="../scripts/jquery-3.6.0.js"></script>
        <script src="../scripts/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/styles.css" />
        <meta charset="UTF-8" />
    </head>
    <body>
        <header>
            <div class="logo">
              <div class="container">
              <a href="../index.html"><img src="../img/logoimagen.png" class="logo" height="50px"></a>
              <a href="../index.html"><img src="../img/logoTexto.png" alt="Casa Conectada" height="25px" class="casa"></a>
              <div class="login">
                <a href="./login.html"><img src="../img/icons/padlock.png" alt="login" height="15px" title="Login"></a></div>
              </div>
            </div>
            <nav>
                <a href="#"><div class="secciones">
                  <div class="container">
                  <a href="./laTienda.html"><div class="sec">La tienda</div></a></a><a href="./seccionProductos.html"><div class="sec">Productos</div></a><a href="./servicios.html"><div class="sec">Servicios</div></a><div style="clear: both;"></div></div>
                </div>
              </nav>
          </header>
        <main> 
            <!--  -->
          <div class="container mt-5 bg-light py-5">
            <h2 class="text-center">Bienvenido a Administración de Casa Conectada</h2>
            <h4 class="py-1">Usuario:</h4>
            <h6 class="py-1">
              <?php 
                print($usuario -> nombre . ' ' . $usuario -> apellidos);
              ?>
            </h6>
            <br><br>
            <div class="container">
              <div class="row">
                <div class="col d-grid my-4">
                  <a href="./gestionarPedidos.php" type="button" class="mx-4 btn btn-outline-primary btn-lg">Gestionar Pedidos</a>
                </div>
                <div class="col d-grid my-4">
                  <a href="./gestionarProductos.php" type="button" class="mx-4 btn btn-outline-primary btn-lg">Gestionar Productos</a>  
                </div>
              </div>
            </div>
          </div>  
          
            

        </main>
        <!-- Footer responsive con bootstrap -->
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-3 col-sm-6  contacto" >
              <table>Contacto
                <tr>
                  <td rowspan="3"><img src="../img/icons/contacto.png" alt="contacto" height="40px"></td>
                  <td>Fijo: (+34) 900 00 00 00 <br/> 
                    Móvil: (+34) 600 00 00 00</td>
                </tr>
                <tr>
                  <td> Calle, Feria 100 <br>41000 <br/> Sevilla - Sevilla, España</td>
                </tr>
                <tr>
                  <td>info@domoticasistemas.com</td>
                </tr>
              </table>
        </div>
        
        <div class="col-lg-3 col-sm-6 atribuciones">
          <div><br/><br/><br/><br/>Atribuciones:<br/> Icons made by <a href="https://www.flaticon.com/authors/ilham-fitrotul-hayat" title="Ilham Fitrotul Hayat">Ilham Fitrotul Hayat</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
          <div> Iconos diseñados por <a href="https://www.freepik.com" title="Freepik"> Freepik </a> from <a href="https://www.flaticon.es/" title="Flaticon">www.flaticon.es'</a></div>
          
      
          <div>Iconos diseñados por <a href="https://www.flaticon.es/autores/smashicons" title="Smashicons">Smashicons</a> from <a href="https://www.flaticon.es/" title="Flaticon">www.flaticon.es</a></div>
          <div>Iconos diseñados por <a href="https://www.flaticon.es/autores/srip" title="srip">srip</a> from <a href="https://www.flaticon.es/" title="Flaticon">www.flaticon.es</a></div>
        </div>
        <div class="col-lg-6 col-sm-12 licencia">
          <br/><br/><br/><div><img src="../img/licencia.png" alt="creativecommons" height="50px"></div><div>Attribution 4.0 International (CC BY 4.0) <br/><br/> Derechos reservados © 2021 Casa Domótica </div>
        </div>
      </div>
    </div>
    </body>
</html>
