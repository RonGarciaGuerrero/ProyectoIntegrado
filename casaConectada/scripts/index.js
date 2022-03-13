
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
            <a href="./producto.html?idProd=${prod.id}" class="btn btn-outline-dark">Detalle</a>
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
    
    
    


    //FILTROS


    function filtrarLuces() {
        var lucesProds=[];//creo un array vacio y le voy metiendo los productos filtrados por una categoria específica
        for(var i=0;i<objArrayProducto.length;i++){
        var prod=objArrayProducto[i];
        if (prod.categoria === "luces"){
            lucesProds.push(prod);
        }
        }
        var filteredProducts = crearProductos(lucesProds);//llamo a la funcion crear productos y le paso los productos filtrados para que los vaya pintando
        
        $("#seccionProductos").html(filteredProducts);
    }


    function filtrarCerraduras() {
        var cerradurasProds=[];//creo un array vacio y le voy metiendo los productos filtrados por una categoria específica
        for(var i=0;i<objArrayProducto.length;i++){
        var prod=objArrayProducto[i];
        if (prod.categoria === "cerraduras"){
            cerradurasProds.push(prod);
        }
        }
        var filteredProducts = crearProductos(cerradurasProds);
        $("#seccionProductos").html(filteredProducts);
    }
    function filtrarAires() {
        var airesProds=[];//creo un array vacio y le voy metiendo los productos filtrados por una categoria específica
        for(var i=0;i<objArrayProducto.length;i++){
        var prod=objArrayProducto[i];
        if (prod.categoria === "aires acondicionados"){
            airesProds.push(prod);
        }
        }
        var filteredProducts = crearProductos(airesProds);
        $("#seccionProductos").html(filteredProducts);
    }
    function filtrarBotones() {
        var botonesProds=[];//creo un array vacio y le voy metiendo los productos filtrados por una categoria específica
        for(var i=0;i<objArrayProducto.length;i++){
        var prod=objArrayProducto[i];
        if (prod.categoria === "botones"){
            botonesProds.push(prod);
        }
        }
        var filteredProducts = crearProductos(botonesProds);
        // document.getElementById("seccionProductos").innerHTML = filteredProducts;
        $("#seccionProductos").html(filteredProducts);
    }
    //filtroLu.addEventListener("click", filtrarLuces);
    var filtroLu = $("#filtro-luces");//]document.getElementById("filtro-luces");//asigno a una variable el boton de filtrar
    
    //llamo a la función de filtrar luces
    //filtroLu.addEventListener("click", filtrarLuces);
    filtroLu.click(filtrarLuces);
    
    $("#filtro-botones").click(filtrarBotones);

    $("#filtro-aires").click(filtrarAires);
    
    $("#filtro-cerraduras").click(filtrarCerraduras);

    //Quitar Filtros

    $("#limpiar-filtros").click(function(event){
        $("#seccionProductos").html(crearProductos(objArrayProducto))
    } );
    
    //pintar botones de forma dinámica
    $.ajax({
        type:"GET",
        url: "./PHP/producto.php",
        data: {'funcion':'obtenerCategorias'},
        dataType: "JSON",
        success : function(infoCategorias){
            
            //TODOS LAS CATEGORIAS
            // var todasLasCategorias = ;

            $(".filtros").html(
                
            );
            
        },
        error : function(XHR, status){
             alert("No se ha podido conectar con la base de datos para obtener las categorias");
        }
    })
    
    

    
    

});
    