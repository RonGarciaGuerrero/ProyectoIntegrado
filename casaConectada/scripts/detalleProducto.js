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
            
            $("#resPro").html(infoProducto.resumen);
            $("#fotoPro1").attr('src',`/PI/casaConectada/img/productos/${infoProducto.foto1}`);
            $("#fotoPro2").attr('src',`/PI/casaConectada/img/productos/${infoProducto.foto2}`);
            $("#fotoPro3").attr('src',`/PI/casaConectada/img/productos/${infoProducto.foto3}`);
            $("#idProducto").val(idProducto);
            
            //alert('otro hola');
            $("#precioPro").html(infoProducto.precio+" €");
            $("#descPro").html(infoProducto.descripcion);
            
        },
        error : function(XHR, status){
             alert("No se ha podido conectar con la base de datos para obtener el detalle del producto");
        }
    });
    
    // Código del carrito con localStorage
    $('#botonAniadir').click(function(){
        //primero se intenta obtener el carrito del local storage
        let carrito = localStorage.getItem('carrito');
        //si el carrito no existe se crea vacío
        if (!carrito){
            localStorage.setItem('carrito', '{}');//diccionario de productos, la clave es el id del producto y el valor son los datos de producto y la cantidad
        }
        //se obtiene el carrito del local storage y se traduce a javascript usando json
        let carritoJSON = JSON.parse(localStorage.getItem('carrito'));
        //se obtiene el id del producto que sera la clave en el diccionario
        let idProducto = $('#idProducto').val();
        //si ya existe ese id en el carrito le suma 1 
        if(idProducto in carritoJSON){
            carritoJSON[idProducto]['cantidad'] += parseInt($("#cantidad").val());
        }else{//se inserta un producto en el carrito usando el id de producto como clave y como valor un objeto javascript con los detalles solo se guarda la cantidad y el id proque el producto puede cambiar de precio
            carritoJSON[idProducto]={"cantidad":parseInt($("#cantidad").val())};//se hace un parseInt porque los valores del select es texto y se cambia a número
        }
        
        //una vez modificado el carrito se vuelve a guardar en el local storage
        localStorage.setItem('carrito',JSON.stringify(carritoJSON));
        actualizarBadge();
    });
    


});