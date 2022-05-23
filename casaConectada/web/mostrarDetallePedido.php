<?php 
  //Se importa la clase usuario y checkout con la clase pedido.
  require_once(dirname(__FILE__).'/../PHP/usuarios.php');
  // require_once(dirname(__FILE__).'/../PHP/DB.php');
  require_once(dirname(__FILE__).'/../PHP/checkout.php');
  session_start();
  $usuario = $_SESSION['usuario'];
  if($usuario == null){
    header('Location: ../web/login.html?error=noautenticado');//se redirige a login
    die();
  }

  $idPedido = $_REQUEST['idPedido'];
  if($idPedido == null){
    header('Location: ../web/login.html?error=noautenticado');//se redirige a login
    die();
  }
  $pedido = Pedido::obtenerPedido($idPedido); 
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Casa Conectada Domotica</title>
        <link rel="stylesheet" href="../css/styles.css" />
        <meta charset="UTF-8" />
        <script src="../scripts/jquery-3.6.0.js"></script>
        <script src="../scripts/bootstrap.bundle.min.js"></script>
        <script src="../scripts/gestionarPedidosAdmin.js"></script>
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        
    </head>
    <body>
    
        <header>
            <div class="logo">
              <div class="container">
              <a href="./index.html"><img src="../img/logoimagen.png" class="logo" height="50px"></a>
              <a href="./index.html"><img src="../img/logoTexto.png" alt="Casa Conectada" height="25px" class="casa"></a>
              <div class="login"><a href="./carrito.html"><img src="../img/icons/shopping-cart.png" alt="carrito" height="15px" title="Carrito"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="./login.html"><img src="../img/icons/padlock.png" alt="login" height="15px" title="Login"></a></div>
              </div>
            </div>
            <nav>
                <a href="#"><div class="secciones">
                  <div class="container">
                  <a href="./laTienda.html"><div class="sec">La tienda</div></a></a><a href="./seccionProductos.html"><div class="sec">Productos</div></a><a href="./marcas.html"><div class="sec">Marcas</div></a><a href="./servicios.html"><div class="sec">Servicios</div></a><div style="clear: both;"></div></div>
                </div>
              </nav>
          </header>
        <main>
            <h2 class="px-4">Detalle del Pedido</h2>
            <h4 class="px-4">Usuario:</h4>
            <h6 class="px-4">
            <?php 
              print($usuario -> nombre . ' ' . $usuario -> apellidos);
            ?>
            </h6>
            <!-- Aqui muestro los datos del cliente -->
            <div class="table-responsive px-4" id="tablaCliente">
              Estatus: 
              <form id="estatus-pedido">
                <input type="hidden" id="idpedido" value="<?php echo $idPedido?>">

                <select class="form-select form-select-lg w-25" id="estatus">
                  <option value="CREADO" <?php if($pedido -> estatus == "CREADO"){ echo "selected"; }?>>Pedido creado</option>
                  <option value="PAGADO" <?php if($pedido -> estatus == "PAGADO"){ echo "selected"; }?>>Pedido pagado y listo para procesar</option>
                  <option value="ENVIADO" <?php if($pedido -> estatus == "ENVIADO"){ echo "selected"; }?>>Pedido enviado al cliente</option>
                  <option value="ENTREGADO" <?php if($pedido -> estatus == "ENTREGADO"){ echo "selected";}?>>Pedido entregado al cliente</option>
                </select>
              <button class="my-4 btn btn-outline-primary" type="submit">Actualizar estado</button>
              </form>
              <table class="table table-striped table-hover table-bordered" >
                <thead>
                  <th>Id</th>
                  <th>Nombre</th>
                  <th>Apellidos</th>
                  <th>Email</th>
                  <th>Dirección</th>
                  <th>Código Postal</th>                  
                  <th>Provincia</th>
                  <th>Precio envío</th>
                </thead>
                <tbody>
                  <tr>
                    <td><?php print($pedido -> id)?></td>
                    <td><?php print($pedido -> nombre)?></td>
                    <td><?php print($pedido -> apellidos)?></td>
                    <td><?php print($pedido -> email)?></td>
                    <td><?php print($pedido -> direccion)?></td>
                    <td><?php print($pedido -> cp)?></td>
                    <td><?php print($pedido -> provinciaTxt)?></td>
                    <td class="text-end"><?php print($pedido -> precioEnvio)?>€</td>
                  </tr>

                </tbody>
              </table>
            </div>
            <!-- Aqui muestro los productos -->
            <div class="table-responsive px-4" id="tablaProductos">
              <table class="table table-striped table-hover table-bordered" >
                <thead>
                  <th>Id</th>
                  <th>Nombre</th>
                  <th>Marca</th>
                  <th>Categoria</th>
                  <th>Unidades</th>
                  <!-- <th>Resumen</th>                  
                  <th>Descripción</th> -->
                  <th>Precio</th>
                </thead>
                <tbody>
                  <?php foreach($pedido -> productos as $item){ ?>
                    <tr>
                      <td><?php print($item['id_producto'])?></td>
                      <td><?php print($item['nombre'])?></td>
                      <td><?php print($item['marca'])?></td>
                      <td><?php print($item['categoria'])?></td>
                      <td class="text-end"><?php print($item['cantidad'])?></td>
                      <td class="text-end"><?php print($item['precio_unitario'])?>€</td>
                      
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            
            <a href="./gestionarPedidos.php" type="button" class="mx-4 btn btn-outline-primary">Volver a Gestionar pedidos </a>

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