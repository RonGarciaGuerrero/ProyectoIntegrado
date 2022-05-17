$("document").ready( function () {
    var error = new URLSearchParams (window.location.search).get('error');//parametro que hay que cogerlo de la url
    if(error=='noexiste'){
        $('#errorusuario').html('El usuario o la contraseña son incorrectos');
    }

});