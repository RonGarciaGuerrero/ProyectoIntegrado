$("document").ready( function () {
    //Se va a llamar esta funcion en cada iteracion del ajax cuando añade un producto, asi se va a ir calculando el total
    function calcularTotal(){
        let total = 0;
        $(".filaProducto td:nth-of-type(5)").each(function(){
            // alert(this);
            total+=parseFloat($(this).text());//se convierte a numero
        });
        total+=parseFloat($("#envio").text());
        $("#total").html(total+"€");
    }

    let carrito = localStorage.getItem('carrito');
        
        if (carrito){
            let carritoJSON = JSON.parse(carrito);
            let html = '';
            for(const [key, value] of Object.entries(carritoJSON)){
                // guardar solo codigo de producto y cantidad. el html del carrito debe leer de local sotorage y debe hacer una llamada ajax a un php al que le va a pasar lo que habia en el local storage y le va a devolver el detalle del carrito con los precios, la foto etc
                //el key es el codigo del producto con ese id se coje todo de
                
                html+= `<tr class="filaProducto" id="prod${key}"><td><img src="" height="100"></td><td></td><td class="text-end">${value.cantidad}</td><td class="text-end"></td><td class="text-end"></td></tr>`;
            }
            $('.tablaCarrito tbody').html(html);
            calcularTotal();//se llama a la función aqui para que cuando no haya ningún producto se ejecute también
            $(".filaProducto").each(
                
                function(){
                    let fila=this;
                    $.ajax({
                        type:"GET",
                        url: "../PHP/producto.php",
                        data: {'funcion':'obtenerDetalleProducto',
                                'idPro' : this.id.substring(4)},
                        dataType: "JSON",
                        success : function(infoProducto){
                            // alert(infoProducto.nombre);
                            // alert(fila);
                            //https://developer.mozilla.org/es/docs/Web/CSS/:nth-of-type

                            $(fila).find("td:nth-of-type(1) img").attr('src',`/PI/casaConectada/img/productos/prod_${infoProducto.id}_1.jpg`);

                            $(fila).find("td:nth-of-type(2)").html(infoProducto.nombre);
                            $(fila).find("td:nth-of-type(4)").html(infoProducto.precio+"€");
                            let precio = infoProducto.precio;
                            let cantidad = parseFloat( $(fila).find("td:nth-of-type(3)").text());
                            $(fila).find("td:nth-of-type(5)").html(cantidad*precio+"€");

                            calcularTotal();

                        },
                        error : function(XHR, status){
                            alert("No se ha podido conectar con la base de datos para obtener el detalle del producto");
                       }
                   });
                }
            );

        }

    $('#vaciarCarrito').click(function(){
        $('.tablaCarrito tbody').html('');
        localStorage.setItem('carrito','{}');
        calcularTotal();
        actualizarBadge();
    });    
});