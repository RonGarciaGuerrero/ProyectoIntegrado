//Función para mostrar los objetos producto 
var rutaImagenes = "/PI/casaConectada/img/";
//esta pendiente de solucionar lo de mostrar imagenes

function crearTablaPedidos(pedidos,prefijo) {
var cadena = "";

//Itero entre cada objeto pedido
for(var i=0; i<pedidos.length;i++){
    var ped = pedidos[i];
    cadena += `<tr>
        <td><a href="./mostrarDetallePedido.php?idPedido=${ped.id}"> ${ped.id}</a></td>
        <td>${ped.nombre}</td>
        <td>${ped.apellidos}</td>
        <td>${ped.email}</td>
        <td>${ped.direccion}</td>
        <td>${ped.cp}</td>
        <td>${ped.provincia}</td>
        <td class="text-end">${ped.precioEnvio}€</td>
        <td><button id="eliminar_${ped.id}" type="button" class="botonEliminar btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#myModal">Eliminar</button></td>

    </tr>`;
}  
return cadena;//devuelve una cadena con el html que pinta cada pedido
}

//Aqui se empieza con la funcion para el botonEliminar
function borrarFila(){
     
}
$(".botonEliminar").click(borrarFila);


function pintarListaEntera(){
    $.ajax({
        type:"GET",
        url: "../PHP/checkout.php",
        data: {'funcion':'mostrarPedidos'},
        dataType: "JSON",
        success : function(infoPedidos){
            //console.log(infoPedidos);
            //a la variable html se le asigna el resultado de evaluar la funcion crearTablaPedidos con el parametro que son todos los pedidos en formato JSON
            let html = crearTablaPedidos(infoPedidos);
            $('#tablaPedidos tbody').html(html);
            
            //cuando se haga click a un boton eliminar se debera meter en el modal el id del producto que se intenta eliminar
            $('.botonEliminar').click(function(){
                //alert('se ha hecho click');
                $('#idPedido').html(this.id.substring(9));
            });
        },
        error : function(XHR, status){
             alert("No se ha podido conectar con la base de datos para obtener los productos");
        }
    })
}

//aqui se pinta la tabla de pedidos
$("document").ready( function () {
    pintarListaEntera();
    $('#confirmarEliminar').click(function(){
        $.ajax({
            type:"GET",
            url: "../PHP/checkout.php",
            data: {'funcion':'eliminarPedidoBbdd',
        'idEliminar':$('#idPedido').text()},//lo que se pasa como data es lo que en el PHP se lee en el REQUEST 
            dataType: "text",//en este caso el tipo no es JSON sino text porque no devuelve nada
            success : function(infoProductos){
                pintarListaEntera();
            },
            error : function(XHR, status){
                 alert("No se ha podido conectar con la base de datos para obtener los pedidos");
            }
        })
    }
        
    );


    //funcion limpiar campos /LIMPIAR CAMPOS .VALUE VACIO
    function limpiarCampos(){
        $("#nombre").val('');
        $("#marca").val('');
        $("#categoria").val('seleccionar');
        $("#marca").val('');
        $("#cantidad").val('');
        $("#descripcion").val('');
        $("#precio").val('');
    }

    //AÑADIR Oculto el boton
    document.getElementById("botonAniadir").addEventListener('click',function(){
        
        $("#formulario").fadeIn();
        $("#botonAniadir").fadeOut();
    });
    document.getElementById("cancelar").addEventListener('click',function(){
    //llamar a la funcion que limpia los campos
        limpiarCampos();
        $("#formulario").fadeOut();
        $("#botonAniadir").fadeIn();
    });

    $("#aceptar").click(function(){
        var errores =[];
        if($('#nombre').val().length==0){
            errores.push('El nombre es obligatorio');
        }
        if($('#marca').val().length==0){
            errores.push('La marca es obligatoria');
        }
        if($('#categoria').val().length==0){
            errores.push('La categoria es obligatoria');
        }
        
        if($('#cantidad').val().length==0){
            errores.push('La cantidad es obligatoria');
        }
        if($('#cantidad').val() <= 0){
            errores.push('La cantidad no puede ser menor que 0');
        }
        if($('#resumen').val().length==0){
            errores.push('El resumen es obligatorio');
        }
        if($('#descripcion').val().length==0){
            errores.push('La descripcion es obligatoria');
        }
        if($('#precio').val().length==0){
            errores.push('El precio es obligatoria');
        }
        if(errores.length==0){
            $.ajax({
                type:"POST",//Porque se estan modificando datos en la bbdd
                url: "../PHP/producto.php",
                data: {'funcion':'guardarProducto',
                        "nombre":$('#nombre').val(),
                        "marca":$('#marca').val(),
                        "categoria":$('#categoria').val(),
                        "cantidad":$('#cantidad').val(),
                        "resumen":$('#resumen').val(),
                        "descripcion":$('#descripcion').val(),
                        "precio":$('#precio').val()
                      },
                //traditional:"true",
                dataType: "json",
                success : function(infoProducto){
                  console.log(infoProducto);
                  if(!infoProducto.guardado){
                    
                    let errores = $("#errores");
                    let htmlErrores = "<ul>";
                    for(let i=0;i<infoProducto.errores.length;i++){
                      htmlErrores+=`<li>${infoProducto.errores[i]}</li>`;
                    }
                    htmlErrores+='</ul>';
                    errores.html(htmlErrores);
                    
        
                  }else{
                    //el pedido se ha realizado correctamente y se ha confirma con el modal
                    $("#idPedidoConfirmado").html(infoProducto.idPedido);
                    $("#formulario").fadeOut();
                    // document.getElementById("botonAniadir").style.display="block";
                    $("#botonAniadir").fadeIn();
                    limpiarCampos();//LIMPIAR CAMPOS
                    pintarListaEntera();
                  }
                  
                },
                error : function(XHR, status){
                    alert("No se ha podido conectar con la base de datos para realizar el pedido");
               }
              });

            
            
            
            // $(".eliminar").click(borrarFila);
            //al añadir un nuevo elemento que tiene un boton eliminar que no existia antes, se vuelve a asociar el listener

            // document.getElementById("formulario").style.display="none";
            


        }else{
            var htmlErrores='<p>El formulario tiene errores: </p><ul>';
            for(var i=0;i<errores.length;i++){
                htmlErrores += 
                '<li>'+errores[i]+'</li>';
            }
            htmlErrores += '</ul>';
            document.getElementById('errores').innerHTML=htmlErrores;         
        }
        
    });

});