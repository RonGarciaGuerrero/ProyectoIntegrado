$("document").ready(function() {
    $("#estatus-pedido").submit(function(e){
        e.preventDefault();
//este actualiza el status del pedido
        $.ajax({
            type:"POST",//Porque se estan modificando datos en la bbdd
            url: "../PHP/checkout.php",
            data: {
                'funcion':'actualizar',
                'estatus':$('#estatus').val(),
                'id': $('#idpedido').val(),
                  },
            //traditional:"true",
            dataType: "json",
            success : function(result){
                console.log(result);
                alert("Actualizado");            
            },
            error : function(XHR, status){
                alert("No se ha podido conectar con la base de datos para realizar el pedido");
           }
          });
    })
})