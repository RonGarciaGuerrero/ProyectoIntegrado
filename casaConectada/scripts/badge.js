function actualizarBadge(){
    // Código para el badge del carrito con localStorage
   
    //primero se intenta obtener el carrito del local storage
    let carrito = localStorage.getItem('carrito');
    //si el carrito no existe se crea vacío
    if (!carrito){
        localStorage.setItem('carrito', '{}');//diccionario de productos, la clave es el id del producto y el valor son los datos de producto y la cantidad
        carrito={};
    }
    
    let carritoJSON = JSON.parse(localStorage.getItem('carrito'));
    let numProductos = 0;
    for(const [key, value] of Object.entries(carritoJSON)){
        numProductos += value.cantidad;
    }
    $("#badge").html(numProductos);
    $("#badge2").html(numProductos);
    
}

$('document').ready(function(){
    actualizarBadge();
});
