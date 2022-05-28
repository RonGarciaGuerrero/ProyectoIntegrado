//Función para mostrar los objetos producto 
var rutaImagenes = "/PI/casaConectada/img/";
//esta pendiente de solucionar lo de mostrar imagenes

function crearTablaProductos(productos,prefijo) {
var cadena = "";

//Itero entre cada objeto producto
for(var i=0; i<productos.length;i++){
    var prod = productos[i];
    cadena += `<tr>
        <td>${prod.id}</td>
        <td><img src="${prefijo}/${prod.foto1}" class="miniatura" /></td>
        <td>${prod.nombre}</td>
        <td>${prod.marca}</td>
        <td>${prod.categoria}</td>
        <td>${prod.unidades}</td>
        <td>${prod.resumen}</td>
        <td>${prod.descripcion}</td>
        <td>${prod.precio}</td>
        <td><button id="eliminar_${prod.id}" type="button" class="botonEliminar btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#myModal">Eliminar</button></td>

    </tr>`;
}  
return cadena;//devuelve una cadena con el html que pinta cada producto
}

//Aqui se empieza con la funcion para el botonEliminar
function borrarFila(){
     
}
$(".botonEliminar").click(borrarFila);


function pintarListaEntera(){
    $.ajax({
        type:"GET",
        url: "../PHP/producto.php",
        data: {'funcion':'obtenerProductos'},
        dataType: "JSON",
        success : function(infoProductos){
            //console.log(infoProductos);
            //a la variable html se le asigna el resultado de evaluar la funcion crearTablaProductos con el parametro que son todos los productos en formato JSON
            let html = crearTablaProductos(infoProductos, "../img/productos");
            $('#tablaProductos tbody').html(html);
            
            //cuando se haga click a un boton eliminar se debera meter en el modal el id del producto que se intenta eliminar
            $('.botonEliminar').click(function(){
                //alert('se ha hecho click');
                $('#idProducto').html(this.id.substring(9));
            });
        },
        error : function(XHR, status){
             alert("No se ha podido conectar con la base de datos para obtener los productos");
        }
    })
}
//aqui se pinta la tabla de productos
$("document").ready( function () {
    pintarListaEntera();
    $('#confirmarEliminar').click(function(){
        $.ajax({
            type:"GET",
            url: "../PHP/producto.php",
            data: {'funcion':'eliminarProductoBbdd',
        'idEliminar':$('#idProducto').text()},//lo que se pasa como data es lo que en el PHP se lee en el REQUEST 
            dataType: "text",//en este caso el tipo no es JSON sino text porque no devuelve nada
            success : function(infoProductos){
                pintarListaEntera();
            },
            error : function(XHR, status){
                 alert("No se ha podido conectar con la base de datos para obtener los productos");
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

    $("form.tablaAniadir").submit(function(e){

        e.preventDefault();
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

        if($('#file')[0].files.length!==3){
            errores.push('debe adjuntar tres fotos para el producto');
        }

        if(errores.length==0){

            var fd = new FormData();
            fd.append('nombre', $('#nombre').val());
            fd.append('marca', $('#marca').val());
            fd.append('categoria', $('#categoria').val());
            fd.append('cantidad', $('#cantidad').val());
            fd.append('precio', $('#precio').val());
            fd.append('descripcion', $('#descripcion').val());
            fd.append('resumen', $('#resumen').val());
            fd.append('funcion', 'guardarProducto');

            var files = $('#file')[0].files;

            fd.append('file1',files[0]);
            fd.append('file2',files[1]);
            fd.append('file3',files[2]);

            $.ajax({
                type:"POST",//Porque se estan modificando datos en la bbdd
                url: "../PHP/producto.php",
                data: fd,
                //traditional:"true",
                //dataType: "json",
                contentType: false,
              processData: false,
                success : function(response){
                    console.log(response);
                    const infoProducto = JSON.parse(response);
                  
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

            


        }else{
            e.preventDefault();
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