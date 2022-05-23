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
        <td>${ped.provinciaTxt}</td>
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




});