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

function todosLosProductos(){
    $.ajax({
        type:"GET",
        url: "../PHP/producto.php",
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
}

$("document").ready( function () {

    todosLosProductos();
    
    
    
    


    //FILTROS
    //pintar botones de forma dinámica
    
    function filtrarPorCategoria(categoria) {
        //alert("estoy filtrando por la categoría "+categoria);
        $.ajax({
            type:"GET",
            url: "../PHP/producto.php",
            data: {'funcion':'obtenerProductos','categoria':categoria},
            dataType: "JSON",
            success : function(infoProductos){
                //console.log(infoProductos);
                
                //TODOS LOS PRODUCTOS
                var todosLosProductos = crearProductos(infoProductos,rutaImagenes);//le paso por parametro la variable a la que le asigné todos los productos
    
                $("#seccionProductos").html(todosLosProductos);    
    
                
            },
            error : function(XHR, status){
                 alert("No se ha podido conectar con la base de datos para obtener las categorias");
            }
        })
    }

    $.ajax({
        type:"GET",
        url: "../PHP/producto.php",
        data: {'funcion':'obtenerCategorias'},
        dataType: "JSON",
        success : function(infoCategorias){
            console.log(infoCategorias);
            //TODOS LAS CATEGORIAS
            // var todasLasCategorias = ;
            let cadena ="";
            for(i=0;i<infoCategorias.length;i++){
                cadena+=`
                <button id="filtro-${infoCategorias[i]}" class="btn-filtroCategoria btn btn-dark">Filtrar por ${infoCategorias[i]}</button>`
            
            }
            cadena+='<button id="limpiar-filtros" class="eliminarFiltro btn btn-dark">Eliminar Filtro</button>';

            $(".filtros").html(cadena);
            $(".btn-filtroCategoria").click(function(event){
                filtrarPorCategoria(event.target.id.substring(7));
            });
            $(".eliminarFiltro").click(todosLosProductos);



        },
        error : function(XHR, status){
             alert("No se ha podido conectar con la base de datos para obtener las categorias");
        }
    })
    
    

    
    

});
    