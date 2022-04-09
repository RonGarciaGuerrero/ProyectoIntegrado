$("document").ready( function () {
     //Se va a llamar esta funcion en cada iteracion del ajax cuando añade un producto, asi se va a ir calculando el total
    $("form button[type='submit']").click(function(event){
      event.preventDefault();
      $("form").addClass("enviado");
      $.ajax({
        type:"GET",
        url: "../PHP/checkout.php",
        data: {'funcion':'guardarPedido',
                "nombre":$('#firstName').val(),
                "apellidos":$('#lastName').val(),
                "email":$('#email').val(),
                "direccion":$('#direccion').val(),
                "cp":$('#cp').val(),
                "provincia":$('#provincia').val()

              },
        dataType: "text",
        success : function(infoPedido){
          console.log(infoPedido);
          
        },
        error : function(XHR, status){
            alert("No se ha podido conectar con la base de datos para realizar el pedido");
       }
      });

    });
    
     function calcularTotal(){
        let total = 0;
        $("#listaProductos li span.precio").each(function(){
            // alert($(this).text());
            total+=parseFloat($(this).text());//se convierte a numero
        });
        // total+=parseFloat($("#envio").text());
        $("#total").html(total+"€");
    }
    let carrito = localStorage.getItem('carrito');
        
        if (carrito){
            let carritoJSON = JSON.parse(carrito);
            let html = '';
            for(const [key, value] of Object.entries(carritoJSON)){
                // guardar solo codigo de producto y cantidad. el html del carrito debe leer de local sotorage y debe hacer una llamada ajax a un php al que le va a pasar lo que habia en el local storage y le va a devolver el detalle del carrito con los precios, la foto etc
                //el key es el codigo del producto con ese id se coje todo de
                
                html+= `<li id="prod${key}" class="filaProducto list-group-item d-flex justify-content-between lh-sm">
                <div>
                  <h6 class="my-0 pe-2"><span class="cantidad">${value.cantidad}</span>x<span class="productName"></span></h6>
                  
                </div>
                <span class="precio text-muted">8€</span>
                </li>`;
            }
            html+=`<li class="list-group-item d-flex justify-content-between lh-sm">
            <div>
              <h6 class="my-0">Envío nacional</h6>
              <small class="text-muted">Envío exprés por mensajeria</small>
            </div>
            <span class="precio text-muted">5€</span>
          </li>
          
          <li class="list-group-item d-flex justify-content-between">
            <span>Total (Euros)</span>
            <strong id="total">20€</strong>
          </li>`;
            $('#listaProductos').html(html);
            $(".filaProducto").each(
                
                function(){
                    let fila=this;
                    $.ajax({
                        type:"GET",
                        url: "../PHP/producto.php",
                        data: {'funcion':'obtenerDetalleProducto',
                                'idPro' : fila.id.substring(4)},
                        dataType: "JSON",
                        success : function(infoProducto){
                            // alert(infoProducto.nombre);
                            // alert(fila);
                            //https://developer.mozilla.org/es/docs/Web/CSS/:nth-of-type

                        

                            $(fila).find("span.productName").html(infoProducto.nombre);

                            let precio = infoProducto.precio;
                            let cantidad = parseFloat( $(fila).find(".cantidad").text());
                            $(fila).find(".precio").html(cantidad*precio+"€");

                            calcularTotal();

                        },
                        error : function(XHR, status){
                            alert("No se ha podido conectar con la base de datos para obtener el detalle del producto");
                       }
                   });
                }
            );

        }   
});