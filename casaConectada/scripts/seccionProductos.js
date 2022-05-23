//Función para mostrar los objetos producto 
var rutaImagenes = "/PI/casaConectada/img";
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
            <h6 class="card-title">${prod.marca}</h6>
            <h6 class="card-title">${prod.precio}€</h6>
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
            //pintar botones de forma dinámica
            let cadena ="";
            for(i=0;i<infoCategorias.length;i++){
                // cada tres botones metemos una fila nueva
                if (i % 3 == 0){
                    if (i != 0){ 
                        cadena += `</div>`;
                    }
                    cadena += `<div class="row">`;
                }
                cadena+=`
                <div class="col"><button id="filtro-${infoCategorias[i]}" class="btn-filtroCategoria btn btn-dark mb-1 ">Filtrar por ${infoCategorias[i]}</button></div>`
            
            }
            cadena += `</div><div class="row"><div class="col"><button id="limpiar-filtros" class="eliminarFiltro btn btn-dark">Eliminar Filtro</button></div></div>`;

            $(".filtros").html(cadena);
            $(".btn-filtroCategoria").click(function(event){
                //event.target.id.substring(7) lo que hace es que le quita los caracteres 'filtro-'
                filtrarPorCategoria(event.target.id.substring(7));
            });
            $(".eliminarFiltro").click(todosLosProductos);



        },
        error : function(XHR, status){
             alert("No se ha podido conectar con la base de datos para obtener las categorias");
        }
    })
    
    

    
    

});
    