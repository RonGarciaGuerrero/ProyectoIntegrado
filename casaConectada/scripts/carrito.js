$("document").ready( function () {
    let carrito = localStorage.getItem('carrito');
        
        if (carrito){
            let carritoJSON = JSON.parse(carrito);
            let html = '';
            for(const [key, value] of Object.entries(carritoJSON)){
                // console.log(key);
                // console.log(value);
                html+= `<tr><td><img src="${value.foto}" height="100"></td><td>${value.nombre}</td><td>${value.cantidad}</td><td>${value.precio}</td><td>${parseFloat(value.precio)*parseInt(value.cantidad)}</td></tr>`;
            }
            $('.tablaCarrito tbody').html(html);
        }

    $('#vaciarCarrito').click(function(){
        $('.tablaCarrito tbody').html('');
        localStorage.setItem('carrito','{}');
    });    
});