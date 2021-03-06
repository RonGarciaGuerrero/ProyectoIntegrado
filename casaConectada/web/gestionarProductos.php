<?php 
  //Se importa la clase usuario.
  require_once(dirname(__FILE__).'/../PHP/usuarios.php');
  session_start();
  $usuario = $_SESSION['usuario'];
  if($usuario == null){
    header('Location: ../web/login.html?error=noautenticado');//se redirige a login
    die();
  }
  require_once(dirname(__FILE__).'/../PHP/producto.php');
  $categorias = Producto::obtenerCategoriasArray();
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
      <script src="../scripts/gestionarProductos.js"></script>
      <link rel="stylesheet" href="../css/bootstrap.min.css">
      
  </head>
  <body>
  
    <header>
      <div class="logo">
        <div class="container">
          <a href="../index.html"><img src="../img/logoimagen.png" class="logo" height="50px"></a>
          <a href="../index.html"><img src="../img/logoTexto.png" alt="Casa Conectada" height="25px" class="casa"></a>
          <div class="login">
            
            <a href="./login.html"><img src="../img/icons/padlock.png" alt="login" height="15px" title="Login"></a>
          </div>
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
          <h2 class="mt-4">Gestionar Productos</h2>
          <?php 
            print($usuario -> nombre . ' ' . $usuario -> apellidos);
          ?><br>
          <a href="./admin.php" type="button" class="my-5 btn btn-outline-primary btn-lg">Volver a Administraci??n</a>

          <div class="divAniadir"> <button class="btn btn-dark btn-lg" id="botonAniadir">A??adir</button></div>
          <div style="clear: both; height: 10px;"></div>
          
          <div class="container-fluid border border-dark m-auto my-4 bg-warning p-2 bg-opacity-25" id="formulario">
            <h3 class="text-center">A??adir Productos</h3>
            <form class="tablaAniadir" enctype="multipart/form-data" >
                  <div class="mb-3">
                    <label class="form-label" for="nombre">Nombre</label>
                    <input class="form-control" id="nombre" name="nombre" type="text" required>
                    <label class="form-label" for="marca">Marca</label>
                    <input class="form-control" id="marca" name="marca" type="text" required>
                    <label class="form-label" for="categoria">Categoria</label>
                    <select class="form-select" aria-label="Default select example" name="categoria" id="categoria">
                      <option value="" selected="selected">-Selecionar-</option>
                      <?php
                        for($i=0; $i<count($categorias); $i++){
                          echo "<option>".$categorias[$i]."</option>";
                        }
                      ?>
                    </select>
                    <label class="form-label" for="cantidad">Cantidad</label>
                    <input class="form-control" id="cantidad" type="number" name="cantidad" required min="0">
                    <label class="form-label" for="precio">Precio</label>
                    <input class="form-control" name="precio" id="precio" type="number" required min="0">
                    <label class="form-label" for="resumen">Resumen</label>
                    <input class="form-control" id="resumen" name="resumen" type="text" required maxlength="500">
                    <label class="form-label" for="file">Foto</label>
                    <input class="form-control" id="file" name="file" type="file" multiple>
                    <label class="form-label" for="descripcion">Descripci??n</label>
                    <textarea class="form-control" name="descripcion" placeholder="Escribe aqui la descripci??n" id="descripcion" rows="2" cols="23" required></textarea> <br/>
                    
                    <div id="errores"></div>
                    <button class="btn btn-outline-dark" id="cancelar">Cancelar</button>&nbsp;<button class="btn btn-outline-dark" id="aceptar">Aceptar</button><br/>

                  </div>
            </form>

          </div>
          
          

          <div class="table-responsive" id="tablaProductos">
            <table class="table table-striped table-hover table-bordered" >
              <thead>
                <th>Id</th>
                <th>Foto</th>
                <th>Nombre</th>
                <th>Marca</th>
                <th>Categoria</th>
                <th>Unidades</th>
                <th>Resumen</th>                  
                <th>Descripci??n</th>
                <th>Precio</th>
                <th>Acciones</th>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
          
          <!-- Inserto el modal que alerta antes de eliminar un producto -->
          <div id="myModal" class="modal" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Eliminar producto</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p>??Esta seguro de eliminar el producto con id <span id="idProducto"></span> de la BBDD?</p>
                      <!-- <ul id="ulErrores"> -->
                        
                      </ul>
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