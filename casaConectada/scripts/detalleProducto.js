$("document").ready( function () {
    var idProducto = parseInt(new URLSearchParams (window.location.search).get('idProd'));//parametro que hay que cogerlo de la url
    $.ajax({
        type:"GET",
        url: "../PHP/producto.php",
        data: {'funcion':'obtenerDetalleProducto',
                'idPro' : idProducto},
        dataType: "JSON",
        success : function(infoProducto){
            $("#nomPro").html(infoProducto.nombre);
            // <div style="background-image:url(/PI/casaConectada/img/productos/prod_${prod.id}_1.jpg)" class="card-img-top" alt="${prod.nombre}"></div>
            $("#resPro").html(infoProducto.resumen);
            $("#fotoPro1").attr('src',`/PI/casaConectada/img/productos/prod_${infoProducto.id}_1.jpg`);
            $("#fotoPro2").attr('src',`/PI/casaConectada/img/productos/prod_${infoProducto.id}_1.jpg`);
            $("#fotoPro3").attr('src',`/PI/casaConectada/img/productos/prod_${infoProducto.id}_1.jpg`);
            
            //alert('otro hola');
            $("#precioPro").html(infoProducto.precio+" €");
            $("#descPro").html(infoProducto.descripcion);
            
        },
        error : function(XHR, status){
             alert("No se ha podido conectar con la base de datos para obtener el detalle del producto");
        }
    }) 
});