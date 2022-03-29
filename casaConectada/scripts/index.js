
//Función para mostrar los objetos producto 
var rutaImagenes = "/PI/casaConectada/img/";
function crearProductos(productos,prefijo) {
var cadena = "";
//Itero entre cada objeto producto
//se ha puesto al div que contiene el producto un onclick que redirige a la pagina producto.html?idProd="+i+" añade un parametro idProd a la url y asi despues compararlo con al atributo del objeto
for(var i=0; i<productos.length;i++){
    var prod = productos[i];

    //Uso de String template --> Javascrip ECMA6
    cadena +=`<div class="card prod bg-warning" style="width: 18rem;">
        
        <div style="background-image:url(${prefijo}/productos/prod_${prod.id}_1.jpg)" class="card-img-top" alt="${prod.nombre}"></div>
        <div class="card-body">
            <h5 class="card-title">${prod.nombre}</h5>
            <p class="card-text">${prod.resumen}</p>
            <a href="./web/producto.html?idProd=${prod.id}" class="btn btn-outline-dark">Detalle</a>
        </div>
    </div>`
}  
return cadena;//devuelve una cadena con el html que pinta cada producto
}
//window.addEventListener("DOMContentLoaded", function () {//todo lo que debe esperar a que se cargue la pagina se mete en esta función
$("document").ready( function () {
    
    $.ajax({
        type:"GET",
        url: "./PHP/producto.php",
        data: {'funcion':'obtenerProductos'},
        dataType: "JSON",
        success : function(infoProductos){
            //console.log(infoProductos);
            
            //TODOS LOS PRODUCTOS
            var todosLosProductos = crearProductos(infoProductos,rutaImagenes);//le paso por parametro la variable a la que le asigné todos los productos

            $("#seccionProductos").html(todosLosProductos);    

            
        },
        error : function(XHR, status){
             alert("No se ha podido conectar con la base de datos para obtener los productos");
        }
    })
    

    

    //Quitar Filtros
    $("#limpiar-filtros").click(function(event){
        $("#seccionProductos").html(crearProductos(objArrayProducto))
    } );
    
    
   

});
    