function actualizarBadge(){
    // Código para el badge del carrito con localStorage
   
    //primero se intenta obtener el carrito del local storage
    let carrito = localStorage.getItem('carrito');
    //si el carrito no existe se crea vacío
    if (carrito){
        let carritoJSON = JSON.parse(localStorage.getItem('carrito'));
        let numProductos = 0;
        for(const [key, value] of Object.entries(carritoJSON)){
            numProductos += value.cantidad;
        }
        $("#badge").html(numProductos);
    }
}

$('document').ready(function(){
    actualizarBadge();
});