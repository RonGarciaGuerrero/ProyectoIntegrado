//Función para mostrar los objetos producto 
var rutaImagenes = "/PI/casaConectada/img/";
function crearTablaProductos(productos,prefijo) {
var cadena = "";
//Itero entre cada objeto producto

for(var i=0; i<productos.length;i++){
    var prod = productos[i];
    cadena += `<tr>
        <td>${prod.id}</td>
        <td>${prod.nombre}</td>
        <td>${prod.marca}</td>
        <td>${prod.categoria}</td>
        <td>${prod.unidades}</td>
        <td>${prod.resumen}</td>
        <td>${prod.descripcion}</td>
        <td>${prod.precio}</td>
    </tr>`;
}  
return cadena;//devuelve una cadena con el html que pinta cada producto
}

function pintarListaEntera(){
    $.ajax({
        type:"GET",
        url: "../PHP/producto.php",
        data: {'funcion':'obtenerProductos'},
        dataType: "JSON",
        success : function(infoProductos){
            //console.log(infoProductos);
            let html = crearTablaProductos(infoProductos);
            $('#tablaProductos tbody').html(html);

            
        },
        error : function(XHR, status){
             alert("No se ha podido conectar con la base de datos para obtener los productos");
        }
    })
}

$("document").ready( function () {
    pintarListaEntera();

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