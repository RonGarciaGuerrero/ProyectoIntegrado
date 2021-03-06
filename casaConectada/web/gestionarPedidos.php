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
        <link rel="stylesheet" href="../css/styles.css" />
        <meta charset="UTF-8" />
        <script src="../scripts/jquery-3.6.0.js"></script>
        <script src="../scripts/bootstrap.bundle.min.js"></script>
        <script src="../scripts/gestionarPedidos.js"></script>
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        
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
            <h2 class="mt-4">Gestionar Pedidos</h2>
            <h4 class="py-1">Usuario:</h4>
            <h6 class="py-1">
              <?php 
                print($usuario -> nombre . ' ' . $usuario -> apellidos);
              ?>
            </h6>
            
            <div class="table-responsive" id="tablaPedidos">
              <table class="table table-striped table-hover table-bordered" >
                <thead>
                  <th>Id</th>
                  <th>Nombre</th>
                  <th>Apellidos</th>
                  <th>Email</th>
                  <th>Direccion</th>
                  <th>C??digo Postal</th>                  
                  <th>Provincia</th>
                  <th>Precio Env??o</th>
                  <th>Acciones</th>
                </thead>
                <tbody>
                  <!-- este tbody se pinta con AJAX en  -->
                </tbody>
              </table>
            </div>
            
            <!-- Inserto el modal que alerta antes de eliminar un producto -->
            <div id="myModal" class="modal" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Eliminar pedido</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <p>??Esta seguro de eliminar el pedido con id <span id="idPedido"></span> de la BBDD?</p>
                        
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button id="confirmarEliminar" type="button" class="btn btn-primary" data-bs-dismiss="modal">Eliminar</button>
                      </div>
                    </div>
                  </div>
                </div>
                
                
                <!-- Fin modal -->

                <a href="./admin.php" type="button" class="my-5 btn btn-outline-primary btn-lg">Volver a Administraci??n</a>

            <div style="height: 10px;"></div>        
        </main>
        
        <!-- Footer responsive con bootstrap -->
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-3 col-sm-6  contacto" >
                  <table>Contacto
                    <tr>
                      <td rowspan="3"><img src="../img/icons/contacto.png" alt="contacto" height="40px"></td>
                      <td>Fijo: (+34) 900 00 00 00 <br/> 
                        M??vil: (+34) 600 00 00 00</td>
                    </tr>
                    <tr>
                      <td> Calle, Feria 100 <br>41000 <br/> Sevilla - Sevilla, Espa??a</td>
                    </tr>
                    <tr>
                      <td>info@domoticasistemas.com</td>
                    </tr>
                  </table>
            </div>
            
            <div class="col-lg-3 col-sm-6 atribuciones">
              <div><br/><br/><br/><br/>Atribuciones:<br/> Icons made by <a href="https://www.flaticon.com/authors/ilham-fitrotul-hayat" title="Ilham Fitrotul Hayat">Ilham Fitrotul Hayat</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
              <div> Iconos dise??ados por <a href="https://www.freepik.com" title="Freepik"> Freepik </a> from <a href="https://www.flaticon.es/" title="Flaticon">www.flaticon.es'</a></div>
              
          
              <div>Iconos dise??ados por <a href="https://www.flaticon.es/autores/smashicons" title="Smashicons">Smashicons</a> from <a href="https://www.flaticon.es/" title="Flaticon">www.flaticon.es</a></div>
              <div>Iconos dise??ados por <a href="https://www.flaticon.es/autores/srip" title="srip">srip</a> from <a href="https://www.flaticon.es/" title="Flaticon">www.flaticon.es</a></div>
            </div>
            <div class="col-lg-6 col-sm-12 licencia">
              <br/><br/><br/><div><img src="../img/licencia.png" alt="creativecommons" height="50px"></div><div>Attribution 4.0 International (CC BY 4.0) <br/><br/> Derechos reservados ?? 2021 Casa Dom??tica </div>
            </div>
          </div>
        </div>

    </body>
</html>