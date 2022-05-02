//Crear la tabla en gestionar productos
function crearTablaProductos(productos) {
    
    var cadena = "<table class='table table-hover' id='tableProd'><thead><tr><th>Nombre</th><th>Marca</th><th>Categoria</th><th>Cantidad</th><th>Precio</th><th>Resumen</th><th class='d-sm-none d-md-table-cell'>Descripción</th><th>Acciones</th></tr></thead><tbody id='tableProdBody'>";//SEDIVIDE LA TABLA ENTRE THEAD Y TBODY PARA QUE AL INSERTAR UN ELEMENTO NUEVO SE PUEDA USAR EL INNERHTML DEL TBODY
    

    //Itero entre cada objeto producto
    for(var i=0; i<productos.length;i++){
    var prod = productos[i];
    cadena +=
        "<tr><td>" + prod.nombre + "</td><td>" + prod.marca + "</td><td>" + prod.categoria + "</td><td>" + prod.unidades + "</td><td>" + prod.precio + "</td><td>" + prod.resumen + "</td><td class='d-sm-none d-md-table-cell'>" + prod.descripcion + "</td><td><button class='btn btn-outline-dark eliminar'>Eliminar</button></td></tr>";
    }

    cadena += "</tbody></table>";  
    return cadena;//devuelve una cadena con el html que pinta cada producto

}

//todo lo que debe esperar a que se cargue la pagina se mete en esta función
$("document").ready( function () {
    //TODOS LOS PRODUCTOS
    $("#tablaProductos").html(crearTablaProductos(objArrayProducto));
    //AQUI HAGO LA TRADUCCION A JQUERY, COMO EL $(".eliminar") DEVUELVE UN ARRAY DE TODOS LOS ELEMENTOS CON ESA CLASE, AL APLICARLE EL EVENTO CLICK SE LE APLICA A CADA UNO DE ELLOS Y NO NECESITO UN FOR, ACTUA COMO UN FOREACH
    function borrarFila(){
        let row = $(this).closest("tr");//closest busca el elemento tr mas cercano hacia arriba en el DOM
        row.find("td").fadeOut(1000,function(){row.remove()});//
    }
    $(".eliminar").click(borrarFila);

    //funcion limpiar campos /LIMPIAR CAMPOS .VALUE VACIO
    function limpiarCampos(){
        // document.getElementById('nombre').value='';
        $("#nombre").val('');
        //document.getElementById('marca').value='';
        $("#marca").val('');
        //document.getElementById('categoria').value='seleccionar';
        $("#categoria").val('seleccionar');
        //document.getElementById('cantidad').value='';
        $("#marca").val('');
        //document.getElementById('resumen').value='';
        $("#cantidad").val('');
        //document.getElementById('descripcion').value='';
        $("#descripcion").val('');
        //document.getElementById('precio').value='';
        $("#precio").val('');
    }
    //AÑADIR Oculto el boton
    document.getElementById("botonAniadir").addEventListener('click',function(){
        // document.getElementById("formulario").style.display="block";
        // document.getElementById("botonAniadir").style.display="none";
        $("#formulario").fadeIn();
        $("#botonAniadir").fadeOut();
    });
    document.getElementById("cancelar").addEventListener('click',function(){
    //llamar a la funcion que limpia los campos
        limpiarCampos();
        // document.getElementById("formulario").style.display="none";
        // document.getElementById("botonAniadir").style.display="block";
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
    
            //pendiente para la PI: escapar posible codigo html en los inputs
            document.getElementById('tableProdBody').innerHTML="<tr><td>" + document.getElementById('nombre').value + "</td><td>" + document.getElementById('marca').value + "</td><td>" + document.getElementById('categoria').value + "</td><td>" + document.getElementById('cantidad').value + "</td><td>" + document.getElementById('precio').value + "</td><td>" + document.getElementById('resumen').value + "</td><td class='d-sm-none d-md-table-cell'>" + document.getElementById('descripcion').value + "</td><td><button class='btn btn-outline-dark eliminar'>Eliminar</button></td></tr>" + document.getElementById('tableProdBody').innerHTML;
            
            $(".eliminar").click(borrarFila);//al añadir un nuevo elemento que tiene un boton eliminar que no existia antes, se vuelve a asociar el listener

            // document.getElementById("formulario").style.display="none";
            $("#formulario").fadeOut();
            // document.getElementById("botonAniadir").style.display="block";
            $("#botonAniadir").fadeIn();
            limpiarCampos();//LIMPIAR CAMPOS
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