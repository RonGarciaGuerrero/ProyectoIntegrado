$('document').ready(function(){
    // Código para el badge del carrito con localStorage
   
    //primero se intenta obtener el carrito del local storage
    let carrito = localStorage.getItem('carrito');
    //si el carrito no existe se crea vacío
    if (carrito){
        let carritoJSON = JSON.parse(localStorage.getItem('carrito'));
        $("#badge").html(Object.keys(carritoJSON).length);
    }
});