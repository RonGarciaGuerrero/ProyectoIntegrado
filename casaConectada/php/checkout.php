<?php

//Requerimiento de acceso a base de datos.
require_once(dirname(__FILE__).'/../PHP/DB.php');



class Pedido{ 
    public static $PROVINCIAS = [
        null,
        "Álava",
"Albacete",
"Alicante",
"Almería",
"Ávila",
"Badajoz",
"Baleares",
"Barcelona",
"Burgos",
"Cáceres",
"Cádiz",
"Castellón",
"Ciudad Real",
"Córdoba",
"La Coruña",
"Cuenca",
"Gerona/Girona",
"Granada",
"Guadalajara",
"Guipúzcoa",
"Huelva",
"Huesca",
"Jaén",
"León",
"Lérida/Lleida",
"La Rioja",
"Lugo",
"Madrid",
"Málaga",
"Murcia",
"Navarra",
"Orense",
"Asturias",
"Palencia",
"Las Palmas",
"Pontevedra",
"Salamanca",
"Santa Cruz de Tenerife",
"Cantabria",
"Segovia",
"Sevilla",
"Soria",
"Tarragona",
"Teruel",
"Toledo",
"Valencia",
"Valladolid",
"Vizcaya",
"Zamora",
"Zaragoza",
"Ceuta",
"Melilla"
    ]
    ;

    public $id;
    public $nombre;
    public $apellidos;
    public $email;
    public $direccion;
    public $cp;
    public $provincia;
    public $provinciaTxt;
    public $precioEnvio;
    public $estatus;
    public $productos;

    function __construct($id,$nombre,$apellidos,$email,$direccion,$cp,$provincia,$provinciaTxt,$precioEnvio, $estatus, $productos){

        $this->id=$id;
        $this->nombre=$nombre;
        $this->apellidos=$apellidos;
        $this->email=$email;
        $this->direccion=$direccion;
        $this->cp=$cp;
        $this->provincia=$provincia;
        $this->provinciaTxt=$provinciaTxt;
        $this->precioEnvio=$precioEnvio;
        $this->estatus = $estatus;
        $this->productos=$productos;

    }

    static function guardarPedido(){
        $resultado=Array();
        $errores=Array(); 
        
        //print(json_encode($_REQUEST["idProductos"]));
        //print(json_encode($_REQUEST["cantidadProductos"]));
        
        //aqui se hacen las validaciones del formulario, el trim elimina espacio sal inicio y al final de la cadena

        if(!array_key_exists("nombre",$_REQUEST) || $_REQUEST['nombre'] == null || trim($_REQUEST['nombre'])== "" ){
            array_push($errores,'El nombre es obligatorio');
        }
        
        if(!array_key_exists("apellidos",$_REQUEST)|| $_REQUEST['apellidos'] == null || trim($_REQUEST['apellidos'])== "" ){
            array_push($errores,'El apellido es obligatorio');
        }

        if(!array_key_exists("email",$_REQUEST)|| $_REQUEST['email'] == null || trim($_REQUEST['email'])== "" ){
            array_push($errores,'El email es obligatorio');
        }else{//aqui viene el email pero se valida ahora que este escrito correctamente 
            $regex="/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/";
            if(!preg_match($regex, $_REQUEST['email'])){
                array_push($errores,'El formato de email es incorrecto');
            }
        }

        if(!array_key_exists("direccion",$_REQUEST)|| $_REQUEST['direccion'] == null || trim($_REQUEST['direccion'])== "" ){
            array_push($errores,'La dirección es obligatoria');
        }

        if(!array_key_exists("cp",$_REQUEST)|| $_REQUEST['cp'] == null || trim($_REQUEST['cp'])== "" ){
            array_push($errores,'El código postal es obligatorio');
        }else{
            $regex="/^(?:0[1-9]|[1-4]\d|5[0-2])\d{3}$/";
            if(!preg_match($regex, $_REQUEST['cp'])){
                array_push($errores,'El formato de codigo postal es incorrecto');
            }
        }

        if(!array_key_exists("provincia",$_REQUEST)|| $_REQUEST['provincia'] == null || trim($_REQUEST['provincia'])== "" ){
            array_push($errores,'La provincia es obligatoria');
        }else{
            $regex="/^[0-9]{2}$/";
            
            if(!preg_match($regex, $_REQUEST['provincia'])){
                array_push($errores,'La provincia tiene un formato incorrecto');
            }else{//la provincia tiene 2 digitos
                $provinciaInt=intval($_REQUEST['provincia']);
                if($provinciaInt<1||$provinciaInt>52){
                    array_push($errores,'El número de provincia es incorrecto');
                }
            }
        }

        //una vez terminadas las validaciones o se guarda el pedido o se devuelven errores
        if(count($errores)==0){
            
            //como no hay errores deberiamos guardar y meter en el resultado el id del pedido
            $resultadoBBDD=Pedido::guardarPedidoBbdd();
            $resultado=array_merge($resultadoBBDD,$resultado);
        }else{
            $resultado['guardado']=false;
            $resultado['errores']=$errores;
            

        }
        return json_encode($resultado);
    }

    static function guardarPedidoBbdd(){
        $errores=[];//se crea un array que contendrá los errores
        $nombre=$_REQUEST['nombre'];
        $apellidos=$_REQUEST['apellidos'];
        $email=$_REQUEST['email'];
        $direccion=$_REQUEST['direccion'];
        $cp=$_REQUEST['cp'];
        $provincia=$_REQUEST['provincia'];
        $precio_envio=5;//de momento se pone el precio de envío directamente
        $idPedido = null;
        try{//se meten los datos del pedido en la bbdd
            $sentencia = " INSERT INTO pedidos (id,nombre,apellidos,email,direccion,codigo_postal,provincia,precio_envio) VALUES ('','$nombre','$apellidos','$email','$direccion','$cp','$provincia','$precio_envio')";

            $idPedido=DB::insert($sentencia);
            

            //se itera sobre los productos y se insertan uno a uno

            for($i=0;$i<count($_REQUEST["idProductos"]);$i++){
                //print("Del producto ".$_REQUEST["idProductos"][$i]." Se han pedido ".$_REQUEST["cantidadProductos"][$i]." unidades. ");
                $cant=$_REQUEST["cantidadProductos"][$i];
                $idProd=$_REQUEST["idProductos"][$i];
                $sentencia = "INSERT INTO pedidos_productos(id_pedido,id_producto,cantidad,precio_unitario)
                VALUES ($idPedido,$idProd,$cant,(SELECT precio FROM productos WHERE id = $idProd))";
                DB::query($sentencia);
                $sentencia = "UPDATE productos SET unidades = unidades - $cant WHERE id = $idProd";
                DB::query($sentencia);
                //QUEDA PENDIENTE MEJORAR EL CODIGO PARA QUE NO QUEDEN LAS UNIDADES EN NEGATIVO
            
            }
            //print('Antes de enviar el correo');
            //envío de email de confirmación
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message = '<style>
            .body{
                font-family:sans-serif;
            }
            table th, table td{
                border: 1px solid black;
            }
        </style>
        
        <div class="body">
            <img style="width: 150px; display:block; margin-left: auto; margin-right: auto;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAL8AAADACAAAAABw9e3OAAAeCXpUWHRSYXcgcHJvZmlsZSB0eXBlIGV4aWYAAHjapZtplhy5boX/cxVeAmeQy+F4jnfg5fu7zJKsVpee+9lSS1nKjIwgCeAOINud//rP6/6DX5ZTdLlYq71Wz6/cc4+DH5r//Brv7+Dz+/v9mvfrs/DX9936et9H3kq8ps8/W/26/sf74ecNPi+Dn8ovN2pfdwrzrx/0/HX/9tuNvh6UNKLID/vrRv3rRil+PghfNxifafnam/1laufzun/MpH3+OP01f7xbvi7+7d/ZWL1deE6K8aSQPH/H1D4DSPoTXBp8EPg7psqFIfX3s97RKD8jYUG+W6efvzojuhpq/vaiv0Tlnu+j9eMn93u0cvy6JP22yPXn67fvu1C+j8pb+l+enNvXT/Gv789BvN+Iflt9/bl3t/vmwixGrix1/ZrUj6m8n7iOm2Q9ujmGVr3xp3ALe787vxtZvUiF7Zef/F6hh0i4bshhhxFuOO91hcUQczwuGj/EuAiU3mzJYo8rKX5Zv8ONRiR3asRyEfbEu/HnWMJ7bPfLvac1nrwDl8bAzQJf+bd/u3/3C/eqFELwX4vPUjKuGLXYDEOR099cRkTC/VrU8hb4x+/ffymuiQgWrbJKpLOw83OLWcL/IEF6gU5cWHj91GCw/XUDlohHFwZDZeRA1EIqoQZvMVoILGQjQIOhx5TjJAKhlLgZZMyJKrLYoh7NVyy8S2OJvO14HzAjEiXVZMSGWiNYORfyx3Ijh0ZJJZdSarHSSi+jppprqbVaFSgOS5adFatm1qzbaKnlVlpt1lrrbfTYE6BZeu3WW+99DJ45uPPg24MLxphxpplncbNOm232ORbps/Iqqy5bbfU1dtxpgx+7bttt9z1OOKTSyaeceuy008+4pNpN7uZbbr122+13/Ixa+Crb33//G1ELX1GLL1K60H5GjXfNftwiCE6KYkbAYJFAxE0hIKGjYuZbyDkqcoqZ75GqKJFBFsVsB0WMCOYTYrnhR+xc/ERUkft/xc1Z/kvc4v81ck6h+zcj9/e4fRe1LRpaL2KfKtSi+kT18flpI7Yhsvvbq/v7B5CHiRf9iLNYhqN3nWmsNSKwxIP7MEuPseymcRh9Ot4xlD2uhXkWYSrRLgMOTMuO8NFbXzuUs7jZnamzcpsMrHNn1iExm9hmukzNx5n50tqd21YjmWY/ADagzXR2hyOtUdblXK93ZqqF+6da52l2pq/cptbhRrDEo9qZa6S7g5UyYxeup+zXvGvySmFsZMtIh2tYsFV3DXPW3Vci3nqA67eCuYsvXvLUQhnttDBm7aRsG3esWltZSs1cLVwNsdYNAgHNLeUiuVBCdj9++D++AlWkT97mjHiEeuGMLhjzpYTVCWAbg7F2zSaMtXck2xj9jPnGadkzx7FtRmuC7Vgc15C/muXK58RAXu9FRVksb+5zmSIYOqEdvL3SHvk2fs66dalAMdMX09qaPKDMM9sZlTQiHRdpMNM+o1ifZPLKyUrgFpmFWq0xCbsjnN4gFZ5RltshnZCI8Mw8awcW2a8bY7vzGjmZU78l9NVDYTUkw1aroZ19GPJgmZhBrqG4Iloftr9fThbsegJPnbRhbdfc1gUFQveVwlUZkKHG1F28jfyE4nc8hHaTTiT72VuTYKWBm7Wtk5W8X23uUpn6XuSE9bAPGXxsje5CHqmF0ygaI3bl5jcB0JZ0LaNQ0T1YJl72Ygb8sVZrrISSGLukcSdQdt1FXrZ7YmWN6h2zNJIV5AVQTvOEd7ZorDkpy5rfBiSy5q2Lu9PY3JxQ5Z5cPbt7BrRbXxpT1LxXATCIZsjoDCKwRluEFPAG8kix+vf1dP86bzdAqXwIo7FwSUhLhFLYPdtaN3VGbcPfArCpxvkZSCcNWBkAD+0LKNZ182rIJ0sxD+b6vlBuUTbPE4BjcrpPBB1p6fIkD5qY73RQORxSl+LN5YRx/eLNw8BifZi2KI7G0gIjQGiLJ5uxiv3e4Mb+A3Z+Xsfc5N6mGHalSAg5wC8gQCnFJNug/B91uqnavGAieZQ3WiJOyq36fDz0xUzTNi4m2wSGAB9ZxtCZPs+4sZe8J8P2bnbUTQwogTmaXwZFKf+Piu5Qf4S/sW77JH6IufQY+DK+YHRqpUCpXpw13SnhtgIO13hY0ATjVhFcuFQfKwBMdkRm7oexMfxjoDdAualiUHqTwbeRgA4ALJ0HkhovVVJXXtc/EQ+0cRtP8Z1lvzwA3l7UlTnyvY5+reXTeLm7z7ABdQpszXliVOgG0a47nzz9BhPXKsiECvkdMlS66jTHOI9RZ2nVcvxmxKuC8mQ6IJ5HzWGtFM9eqP9bNaYGqI/T5qrhUOslIeYHUdtogBBOYXVuJPmpwTP3vGGBHtSRLy/Xv38tgyfnM/t2ZeIdKvyCegGP0Zu7vPKfdnwj07dyv+1E+bGuM5dFEp3aUSibwECs1MO+rlnKutTP2rYnI4YxI1acaWCMq5+Bwji3dK6/c4XEd32//mRkTeuhs9JAHXlEWti+tlimq7zIJ86x/1W643JWKVymFY1SU2c46hp5MNB3JOwp61Ka/JuqB/iAnmIEO09W/6Rmc3TZh8LrSmQKfEzGwD4kJIUNJbQ5SyIvDhwGPyCt0FeJG1fdF+6q4KGdwuKZBk92Kl6wItKDCEYXEzjA6mU9JQLUTyok6uQStti/ImSmZTGoA8I83mCO1DYDPd1kq6sDWmoKlG0ESCQPJvAtlDwQACoXX4biPB02Hv30neKa9ZJZBy8iuUYEGpDqIEtbpR+PQj2UiJ5GEGwWXb5ZtAPGU29o4DMvSVuoNkAq3mBw+klykmk76dxOxWTKqAL4ZaTK05BA9e7vUxGuE43jEMVc8DSrmd2FRHqetsn7TPBLQIxRa7dTbEaMSUpK3Boou0FTMGLASAdRHQK0DpxFySbs+jVAvT8ZwXTkVDGuJB/6xKOF4f86yBe+HXgE4JENzXvjY7/oJW3IdhcijL9yZ0Vg8s76R6EJittTIlJdUqVPpP7P6x2RxQxiuahgndjdm9macQDMQBJzg6q5LYiUKA4l2fVqVo368m1UNCH5Sop0VFOCpMdIs7u7gOQLY0yka6R4LyCMfYQXSYsAZslLUoYSV+pfUCPzGw50X6pjkcAATkP9E4kYCcp4DmAUjBAZDqyHxnJelqxy7cMEJC5CJMxq28FWR95oTJQ5EvOyTIFFIqv2Rpvzb6ZMIgMAZeoNps3kcRJV6QIsxqzwg89WNmoQEggR1OWPGOIgpFtP89YirbLHTL8v+Y9XHuhZI8YnN0bqsHDcQ8YqBOIYOnoHzDZfpx8P+XFAC7ZOZyegmVkChXWBJHM4BJAUNsA+pPiRT2AmT6iUHAZBK0Ge4boA/TSAMoQbCIPGhnQO96ZidJnj9rFCBcJ6RMDB66PqJmEkhX1k0oGlBVDRsQhXyv578eIMfczCMuRK1U3ZaGbSSs4o5DsrhMMndQJiJkZAhUuTnAiFW6WU0BT4vYsX2Vs6pCDLQerTp4mUkgpukIks1pDFSTIWFD/4YxGZ3KGLGRHJDAGAuo5bS+iv/WswuArwnWNqjRHVhFldqVddmScs9aEqJVt5LmgX+3GEqpJ3KL7QtEIoRj56xX6YFoIJ0Riqqpg6mflu2WhyRV6nyPbc5/AdEj+PJ9l+JQuE72YR0SgfcxIRJxKUvuYLiyx8QkTEsawIfpwt4YfAKdFMVZCAHhwQuIJ6+Z6CLT4TtHnfqQk+yNxnZ0TSPkiPs8ukAC4e4zoYhSd4JbPWqkdMNE5oTOz7QQoOljVT3Xsz8Y6V3L9YLCwJD1sLv+I2xicWFNhhQlU1CbQVXENWQQrJJdfE3BnNhUBFbtqFkHdtJAuaAlEWenYbr4XJPXh++6CkXGzgiYa3Bpiz5MSERRmw3OpB9xN1+dgFoQFW0ifLgaqQfowHGwoaJjB0Cfk9U6DyQ6/+NRSBNdmwcdCiIa8nVGGTPZB7MNFwDSe2q1QcdwI2UbwlsfQDwa+0oRYXgN+gJyI1J1Y4I4p8Q51WpAkTAiUatQb5akpXaTs69conBE9tDgBywVQpo1jqYpkRbR6lsr1cM3ENZD4YPwtr1NDmVy3ZSFXgJlUmLAsRHdgs2RNYWYb9E60mGXt+oztbZ8Nr5Lzm6Sc+hyxdeDwfrWIkz/JWKuotUm1TZpj0Kxg6qW3NAeRbnmXarYFH8DjVlKtguIPQPaql8nQ2QglJzm/80RTFoJSpQZLrySTQGRAEDM8MjjEwEJItqzOMDcLn5ZvRauGlQ8UNLRJt4mDWvpAS/IBrkKTYUG1vGFPMnNO+Qu6fPsgTIcT4r6Aco3pLDdjwn2RH0GBpS8diM6wDGIEXTuKqAIOjMH/ArEpyqnmN+58YrMuUtCAXYQisQrELaeahSt6pPdp+GvU6UAHq7JIvsiAR6vmCq9dhQ2A/QKwa9AKX0xNioNRpb+oU10N2MFty7mTVxLhAwpIuIw/hSFVoYpnwA9iAV0DA+WooMGztepq/He3lzIJfG0GIVeRnse0IHKJIzW+C4k8AMfK2eqWlIVUIC3joxFOq62EkfMJMHAiNi6agJDWxOpS64Z+LGiDg1i0zMft1EPANdJ6sPwNjHfgo3SZmETtl97oCuG+PJ5XvZ4mXNh5QD5FlbQqzJzK7qgQoaka/iO2Ac9B8C8XlBdfu9jhYrfM6TkORpDZJR7A6QMJDC5IQOMxz4XBArN4DbMRCprvVliuTujouguxg8o4gs0cOMAWM76WsQWTEl8qZ8mRtSQIGuEafSPn0RPSu7xWNVZwkrT4HjQND6rPBKRMPyYBO5kduW9RpKwvPy0P2mnhMCoCqSmo+9acKHGuh5cWzIWXAU8opx9xmi6FgHpYszMDZAqFYU7/rNWguqzWSIbFJIpMzCHbMA1YDmxZnIeA9kAzU7Rro/g9Ymjop+AAkjpcPzQnhyzrvtPiP0kO6BIoWib8gW/Qp1UJhXqAhYJ6wDAsxQLrBI9OoLLLvlTHo9jo7pArlQ+ywJ96tpt4QmpH4B6gjrbiv6oBxfdQ2wg4bzdKPSEzP9hmq2SivjntFcyPmCi77iXxWnCWAXySHzeqG+wF0iWY4PNeAioVPiQBm6PWIyRJZWW6N4lHVOGrqiKFZ6YKthvazunCILUb2pgLxVRDIyyp2ddGUi0gSpMNWf/jl2nIGazEkP/MYCxEGwKtnZJH6pkQpexAGWtzcrE5U9oQIlAyz4cn62zDAhngomxI740HBT5z4gRIfjPgAxJTkVEcItoU5VZEZasWQWAR/3O4ThVz49OSkHQIJSsbN0kt5oQOzenyYED7SQ7CgDU8E5cIoIzxsvbdTtJQwUKREaTKJiEkCV4+KEvgwynerW4+B2aBHk64A1EOrMJFKQiszgvvpyVLNuSAjuBNhJasZ98bvf4Rx4ytIOEGd7PmQV9zPPJEJBGQ5tIP5K+5Ewk/10WujhCVjjzaFjoz7hLhUe3cG3CXVXj8eiZoFXRDoaMhWLl6P25GKuOnHJyZsUgZQoh8V7YEWKgSnczK2yGTsO4L2FWwR37jvXEFQl3H0D7hT8gt3sY4U6oyLAmQMmbShMuVbBZynO7nMPO9XArCsRc1ZGA9/BOCfGx5KxLs8cKZePoL5BLW5CEdj+imDycUVQE2HBJpS1PBnCA00E6zOKNJiFpAVk0LWQewTfjiYPsLZTQSLvKRIy84uMtRClKYaCOqrDJYZGu9lq5FIhuLnQWntceyMwdcOScGUAB9AAs+Uy0IfDcSQlkxOp0ujU/toTyTrkC1mJBbXAt8Ud7Ab+F9Z+z5qThtiqTSSoQ1HYQNGNwEmG+p/jXyz9trl2F35w9yGsgagx8lT+guyboBgxU5Nw6feJcwmeUEPruzoPvWb8N4GphFbtHV7Jd0pva0QLpRvCCD761hTuCcTOy6B+7XbpMLdUbzV74Klx4GQUgUydkdHHqPYQTlEGSCIDuQOBhaqWAlkz6IjjD5pbQ/P5CYOWANf5XOmmm5JkWuXdO7wEVwuAbazHDRZ4BsIimQLZRI1VBk2gDEtBGgHIihcWWFwO0+mN32n4IrHNJAIhj7FMTDoqvYb1M97oiMEJDIB5xpJDJJHWEQGiP6vXH+CQSTZyBWyGvm4xCDg2+sd4Sw6HoRROtCa3JBy4gWpIKNQEAznaoMIGcrgUAbq2LNaSElmkyYGN6esnal64P/aqyMlKnhR0FoAnPxlQbcFbRRWkgXUwUJGBgsM54DhpnCoYGLS5YYeP0iHOSQ2ymCA07Ckr3wCCr2E0raVwfISL9pXOL1RVhuohMMPMc0H21a6AXvag2TdQT9wFEXIo586sliGMFcXIb72ZLVAeYmuQ75yZ4MfcKw4UfDqDNmsXk4mAwA6Ukt7xWTDQ9YVB0hHIF4Ec4ZtEacErQUcK0YGNSGYCc8EOZmx13KcIwUpGRLT1O4hz+d52h1IaVdkKCkp+T/73/tl7mfjTI2lKekIbTD00tBW3HzxbXzvZ4uF6aIQ1TV62rbJH86HJ9tNhm46bMJVWg8REgsIjHUVOvlalOVpaa8BJGdQHRAuXnoDcxgFhOsyNZGxnnn4t4SrBjf/1LhIfO8WJnkOmIGt0yAShv86kJZZn/s7Smuz9Iar8x1zVRmJsSWQkA/xKe6ovd40RmlAEJiNwMKYNWwjkQr77c6OXVp/xLeJFVKIDEmNu9vrv+A2UYv4W6Kf1H0lJOYi1NKfGsFidDVoC6i+VTEXYY+RY51LJOL5LFgTE5e1ncO71AOSRtVFaTnKF63b9usDxUrYABZcsRTLNlwa+Uk2aP+hiUKCWiFYTqTcTklNp0q95eQ6eafdGNiKQd6qbeSFsIwHlAGwgddUN4y/llF6F0g7eXAtOV8Rc5SDFejOJf6FzwD7ms7IJF4Ha+NLtSJzjfXa6qGHzSQNy3SA36BtaYYoNnz4NYdbIxDTuTa4eoI8RlJPSwRJ/sJxB1UOqAISTc0BgpIwHaZN6ZjxfxeKYYDACD4YnaztcObAYIcaRtPGp0NGYUKcZCTpXtUuGKBM8hinHrEaX5vmN6jWojZkMFoHo33EvACRtoE8xrGim8NuwJ0/2mzGhiDdAUqyLZBRXhtSsNVwUz3kRFnG7RFoJIfaaOnt9t2NVgAc9g2QrYdfb0voQBQMRNJz8viJZFIl1YE86SDd7JEzfkrbA7N0ckS1t4ltQvdcqWXBLBqEtUFig0xADoIEkatteo8GA0komWyLhUIXHbVB9hpPpvnnfyL11J4ZfV4DIP+9EH/d76eMLhjhX5mE3A4AoZMI2j+7bw/7IQQp9DnUIEvdApHAweOO9pAyw4qsR/zrftodPF4HJQb4vdFpXVKwAvQoUbWHKiAiIdey+mEgqUOv+HzG1sbahdZjA9IWsMoT1XfG6N2v8xq7lj+jy9unLQ8Y/gYxgpSfGAMmCGV+wRgdL0rxc7igNieg+dlP5l3UwcTwX0QZ5H90FgtxjbyvGZ8VoGp0nQQ8YEAJfPYTi7p+Ex5aKgO0viqhN0g9jS2HnRDfDaGCVOwBBhUlkhowNUWYsK4oDOAFfSLwZznxCUgLKgtB4qU7G6idcTP8wbUVQ0GqNNDaFAbOZ1EbARoD08Kj/uUqLgrGtI6QbLVA+rkkZCOGFTw4AhdEOHIA/VqpvXg65IbxUsPUqBfYXf7SdcRomG1RXw8rj6BSuwN4ute2DRkwAHLBMJYdUUnFoSQpp+3heiaCysZBboYtpmwIK3XvSWcrRQcqtGEQkAckuoZQowEkllr47M0LsH7JAR33yG/XhTB24plf2IdqS0rzJ9kqsz90q239vxOu+32niltRJ9hVBrq0T5LlZHDmY6lfYjqaE58AOnEX0Gx9mv5qjcECxHAGnfMqDc8Y5fW1GyX9tg3zzYOYPKjWl5AGRQj+qdGABuvJfFQjCskiy2qfARmWCpbRoZ8uOz09sM+IasxZQQRWSxufXNdyxRqGwFF7kKwNaYY8WgEq8mojWiRp+BhCan9i7r+WlvumtjCk6m107ViqtmumotGjaqtwLcB7UfDorI+R+xzH0WGva0blV/QQLnvBkNWjgt8hsVmCFByu7PM1Ml0nspY9kPs0aoAZwYg8GYoaCfrp5m1y6H41golTbMIXCGa1Ra1UhoLw+/GppqAe4hVCjrXbklGq6PfW0M2otu0lQuH3TdzxgYVcFR9Rt7A5PlDbbhtVetCB8BdeBOUMHLxRfebcIEim9foyQv8LZyw1wxaipir8xsOaDnzWXVJDPlNAB7tOwndtvzJO7VKQsFiHfMmvAa5I787UGlPCqCqp8bPjeeaj7gkFyvQDnhZ3KYunU1UkM/aOpS/aHVEpU1rl6HvakiUcPXl+XFj785Z7f/qHoQSn/ZhPX2TP1x2DqdeHR86P8L4IAXfMhEiiQp6yA9DvfXJCzsPBF/2XCH/Fl/sqwsS3P0em4w1UEv6i48eM8nuuq73b4GF8cH+q+qXzEDBnidrQQegCZfg4IIApIt/Jifi1TQy6bxDyu13F1RkzxiBUudoxEQGgUdQJh6yDPGrHhitnjNzUmjO1ZFh8ivRQuziJEyxKLiGydUb8aOsPVNCpLWUxlXLV870yOfEtb0FxtB25ERL2kNZJ5zx/HRimBKD19uF87DZOsAOChqeuBE7w1sFbM5RDdmrKIOQPCdaWJLOpF6HUaijRWmLWseS3EbJBUjJHp6SG/HbQLvgaOjAyCb+gbC+dyAO/TNsDFPuzwAT+jyc7//bq/umFv76CIdhHaQvoUFXSdkaxhVs1fR1SqhQEC4vUhOEE702Vj7s0NcPDRGQX2BqrIAagOAk9b3d0lU76EitmGmXsMIm9Fe0Q5ZokACmGqgbC1Xnvb47w7ahzJThsZ0krGGBJgB3+JmGNxUFIwlNIO+4QdeL27ZlcyTtqAUlieWBD80ADbpIhOdQ5IINWSlBOOUrtF/SVJUmjNliPGgiK9yg686kjUo05ayt3wtukFVDgwD7tXEiM4CL0fwG01/PTOQaJq39xZugvr+6fXohJaRjTXOSFUJFwRslTp296QL8Ml6a256WywlQbOOmMUQcKDgyGgJHRRwoxZe0XaEesdh2HHQBcqEfyvZSZm9NRbFZZ/SScuPaM1eFlUvpeIZmPzi0MYJwiNjWCs8Q2qGCJdJjx/R8DcbpP+n6DAD+PdgD2TS0GNPKMCfmNrsF7JUk3jzoiQag/N8CXrGNM6CS+FeJXoLW/i+NM0CLcfpoOoPR8tCV63kEJ6IIpZh2lwEgRfmR+3LGoYSlBZUhlXP0y7XFIwg81UTesRYpjG4/kCoLmc87Or7F0BnnoHJtStKw/Tu0fvjolHDHSYT9tIqnN/OuOni+kKxZs7TRlDQzhqP2ApTOIQ31OaYDhh8MX4Y7fGaR5hFwLX4vWe0oZu2u++ePV3+tTTZupEzxqD5NIfIfkUrmN47CfoIDsdeTbOmIfegssH25JrTI8h5q+aNX5L6fvvv0AdF9h60BXnUunu4BxD21hBpKaWAu7kytDAFH9nXj07T4NB7VGTBvWAwpawL+8K+wFa0M7G1d0dboSMVsTokQSjSnnQCVsRCQaypEmS5kRJtn2OSypPfGpxQ1bb6vzQM2rnd3SVAO3joRUGstDePNIU93l8meb6aLw/Usy8BGRDq9FlI+gE8E/tVzvqJNcXcCWAOvaaT+UY31bxWC2zj9pc/kfwgYvMLeONIrIiRgxB+xc1aGLm3SgKOjwrM6yaJcqqSmOPRuGTGrKdXTRmmR/Ou2RTNi9v5McSSc53H5tbW62qAiG3a/2WUYDT2eMFGoJeCGd+yd51MVVn6wSTMCo6lDfFWbE7rTn5fvWNnJknMwP4x8JfNOusNeR6o3JQjuRaVnt9PFkHbE6agrpdpDOcQmcQ1tltCWko/83I9+8UOnVyKTZzz+kPPe/XKDDleS4r3NktY9wuBGVRqnvplMcKFFyAQ5wCp+6mB0N+JN3tPcFKI5khWxoherJ2nWpqHVu1XGE+/X34uM2412HE1JniwDolGCWEoKLbC28tJqOjXge8kaH996pDSQin6PTkblyvSVXZM92JDthWj0jHLJ9uimG2lv3/T8LOIb99tgYrhq9Dz7XfOWkfj7cfgtV5Z/0GzqUKHsXQNbX5ls6NFGP71yf62wXk+V1yEr/bwW66lOhQE2TuZvDZ5cbrPO6wAl6QCKNocMQmYEzo0WJIj3B/tZlrMvWoWU8v3qjoWb1dSlDAMndGN54UfmMpMAPCFgQLOvoypyIs4R/hq0TavdcCLq0+035uD/XFTYXYez+G6g6l1v+FKbiAAABhWlDQ1BJQ0MgcHJvZmlsZQAAeJx9kT1Iw0AYht+miqIVQTtIcchQXbQgKuIoVSyChdJWaNXB5NI/aNKQpLg4Cq4FB38Wqw4uzro6uAqC4A+Im5uToouU+F1SaBHjHcc9vPe9L3ffAUK9zFSzYwJQNctIxqJiJrsqdr2iFyEM0ByTmKnHU4tpeI6ve/j4fhfhWd51f44+JWcywCcSzzHdsIg3iGc2LZ3zPnGQFSWF+Jx43KALEj9yXXb5jXPBYYFnBo10cp44SCwW2lhuY1Y0VOJp4rCiapQvZFxWOG9xVstV1rwnf2Egp62kuE5rGDEsIY4ERMioooQyLERo10gxkaTzqIc/5PgT5JLJVQIjxwIqUCE5fvA/+N1bMz816SYFokDni21/jABdu0CjZtvfx7bdOAH8z8CV1vJX6sDsJ+m1lhY+Avq3gYvrlibvAZc7wNCTLhmSI/lpCfk88H5G35QFBm+BnjW3b81znD4AaerV8g1wcAiMFih73ePd3e19+7em2b8frrtyv8gg0P8AAA2MaVRYdFhNTDpjb20uYWRvYmUueG1wAAAAAAA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/Pgo8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJYTVAgQ29yZSA0LjQuMC1FeGl2MiI+CiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogIDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiCiAgICB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIKICAgIHhtbG5zOnN0RXZ0PSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VFdmVudCMiCiAgICB4bWxuczpHSU1QPSJodHRwOi8vd3d3LmdpbXAub3JnL3htcC8iCiAgICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgeG1wTU06RG9jdW1lbnRJRD0iZ2ltcDpkb2NpZDpnaW1wOjk5OWZkNGMzLWY4YTItNGI0Yy04YjJiLTg0OTdhMmNlYjM5ZiIKICAgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpmNGMxYmRiNy01M2UwLTRhNTUtODlkOS03NmUwZjNkYTZkZDQiCiAgIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDphOWVhMTIxNS01ODdmLTQwNzEtYTIwZS03MjkyZjljMzhkMjAiCiAgIEdJTVA6QVBJPSIyLjAiCiAgIEdJTVA6UGxhdGZvcm09IldpbmRvd3MiCiAgIEdJTVA6VGltZVN0YW1wPSIxNjM3OTUwODY4MTQ4MTQ1IgogICBHSU1QOlZlcnNpb249IjIuMTAuMjgiCiAgIGRjOkZvcm1hdD0iaW1hZ2UvcG5nIgogICB0aWZmOk9yaWVudGF0aW9uPSIxIgogICB4bXA6Q3JlYXRvclRvb2w9IkdJTVAgMi4xMCI+CiAgIDx4bXBNTTpIaXN0b3J5PgogICAgPHJkZjpTZXE+CiAgICAgPHJkZjpsaQogICAgICBzdEV2dDphY3Rpb249InNhdmVkIgogICAgICBzdEV2dDpjaGFuZ2VkPSIvIgogICAgICBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOjQ0NzBjZDYxLTBmMTItNDk1MS05Mjg4LTdhYjdlNDI4YjE4NyIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iR2ltcCAyLjEwIChXaW5kb3dzKSIKICAgICAgc3RFdnQ6d2hlbj0iMjAyMS0xMS0yNlQxOToyMTowOCIvPgogICAgPC9yZGY6U2VxPgogICA8L3htcE1NOkhpc3Rvcnk+CiAgIDxkYzp0aXRsZT4KICAgIDxyZGY6QWx0PgogICAgIDxyZGY6bGkgeG1sOmxhbmc9IngtZGVmYXVsdCI+Y2FzYSBjb25lY3RhZGE8L3JkZjpsaT4KICAgIDwvcmRmOkFsdD4KICAgPC9kYzp0aXRsZT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAKPD94cGFja2V0IGVuZD0idyI/PsbPjtgAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAAHdElNRQflCxoSFQjk5b9DAAAAAmJLR0QA/4ePzL8AAAu7SURBVHja7Z15VBRHHserZ5jhGi4VEZ6KBwEF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" alt="">
            <h1 style="text-align: center;">Casa conectada</h1>
            <h3>Gracias por realizar tu pedido, '.$nombre.'</h3>
            <p>Tu número de pedido es: '.$idPedido .'</p>
            <p>Para proceder al pago de tu pedido debes realizar una transferencia bancaria a la siguiente cuenta: </p>
            <h3>IBAN: ES6621000418401234567891</h3>
            <em>Debes incluir en el concepto tu número de pedido.</em>
            <h4>Resumen de tu pedido</h4>
            
            <table style="margin-left: auto; margin-right: auto; border-collapse: collapse;">
                <tr>	
                    <th style="width: 60%; text-align:left;">Producto</th>	
                    <th style="width: 10%; text-align:right;">Precio unitario</th> 	
                    <th style="width: 10%;  text-align:right;">Cantidad</th>	
                    <th style="width: 10%;  text-align:right;">Sub total</th>
                </tr>';
                $sentencia='SELECT * FROM pedidos_productos,productos WHERE id_producto=id and id_pedido='.$idPedido;
                $resultado=mysqli_fetch_all(DB::query($sentencia),MYSQLI_ASSOC);
                // ,id_producto,cantidad,precio_unitario)';
                $total=$precio_envio;
                for($i=0;$i<count($resultado);$i++){
                    $subtotal=$resultado[$i]['precio_unitario']*$resultado[$i]['cantidad'];
                    $total+=$subtotal;
                    $message.='<tr>
                    <td>'.$resultado[$i]['nombre'].'</td>
                    <td style="text-align: right;">'.$resultado[$i]['precio_unitario'].' €</td>
                    <td style="text-align: right;">'.$resultado[$i]['cantidad'].'</td>
                    <td style="text-align: right;">'.$subtotal.' €</td>
                </tr>';
                }

                $message.='
                <tr>
                    <td colspan="3">Envío</td>
                    <td style="text-align: right;">'.$precio_envio .' €</td>
                </tr>
                <tr>
                    <td colspan="3">Total</td>
                    <td style="text-align: right;">'. $total.' €</td>
                </tr>
            </table>
            
            <p>Debe realizar la tramsferencia en las próximas 72 horas. En caso contrario, el pedido será cancelado.</p>
            <p>Si tienes alguna duda o quieres simplemente ponerte en contacto con nosotros puedes escribirnos al correo <a href="mailto:casaconectacasevilla@gmail.com">casaconectacasevilla@gmail.com</a> y te responderemos a la mayor brevedad posible.</p>
            
        </div>';

                // print($message);

            mail(
                $email,
                'Casa Conectada: Pedido realizado correctamente',
                $message,
                $headers
            );
            //print('Despues de enviar el correo');
        }catch(Exception $e){
            //print('Ex');
            $errores[]=$e->getMessage();//añado el mensaje del error
            //print('Ex2');
        }
        //se imprime un objeto json haya errores o no
        if(sizeof( $errores) > 0){
            return array('guardado'=>false,'mensaje'=>$errores);
        }else{
            return array('guardado'=>true,'idPedido'=>$idPedido);
        }
    }

    static function mostrarPedidos(){
        
        
        $sentencia = "SELECT * from pedidos";
        
        

        $result = mysqli_fetch_all(DB::query($sentencia),MYSQLI_ASSOC);
        $pedidos = Array();
        foreach($result as $ped){
            array_push($pedidos, new Pedido($ped["id"],$ped["nombre"],$ped["apellidos"],$ped["email"],$ped["direccion"],$ped["codigo_postal"],$ped["provincia"], self::$PROVINCIAS[$ped["provincia"]], $ped["precio_envio"],$ped['estatus'],null));
        }
        return json_encode($pedidos);
    
    }

    static function eliminarPedidoBbdd(){
        $idEliminar = $_REQUEST['idEliminar'];
        
        $sentencia = "DELETE FROM pedidos_productos WHERE id_pedido=$idEliminar";
        DB::query($sentencia);
        
        $sentencia = "DELETE FROM pedidos WHERE id=$idEliminar";
        DB::query($sentencia);
    
    }

    static function obtenerPedido($idPedido){
        $sentencia = "SELECT * FROM pedidos WHERE id=$idPedido";
        $result = mysqli_fetch_array(DB::query($sentencia),MYSQLI_ASSOC);
        $productos = Array();

        $sentenciaDetalle = "SELECT * FROM pedidos_productos JOIN productos ON pedidos_productos.id_producto = productos.id WHERE pedidos_productos.id_pedido=$idPedido";

        $queryDetalle = DB::query($sentenciaDetalle);
        while( $resultDetalle = mysqli_fetch_array($queryDetalle, MYSQLI_ASSOC)){
            array_push($productos, $resultDetalle);
        }

        return new Pedido($result['id'],$result['nombre'],$result['apellidos'],$result['email'],$result['direccion'],$result['codigo_postal'],$result['provincia'], self::$PROVINCIAS[$result["provincia"]], $result['precio_envio'], $result['estatus'], $productos);

    }

    static function actualizarEstado(){
        $errores=[];//se crea un array que contendrá los errores
        $estatus=$_REQUEST['estatus'];
        $id=$_REQUEST['id'];
        
        try{//se meten los datos del pedido en la bbdd
            $sentencia = "UPDATE pedidos SET estatus = '$estatus' WHERE id = $id";
    
            DB::query($sentencia);
    
        }catch(Exception $e){
            $errores[]=$e->getMessage();//añado el mensaje del error
        }

        //se imprime un objeto json haya errores o no
        if(sizeof( $errores) > 0){
            return json_encode(array('guardado'=>false,'mensaje'=>$errores));
        }else{
            return json_encode(array('guardado'=>true,'idPedido'=>$id));
        }
    }

}




//cuando esta clase se usa directamente sin llamada AJAX los if dan error porque el parametro funcion no esta en el request, para eso se añade el array_key_exist 
if(array_key_exists('funcion',$_REQUEST) && $_REQUEST['funcion']=='guardarPedido'){
    print(Pedido::guardarPedido());
}

if(array_key_exists('funcion',$_REQUEST) && $_REQUEST['funcion']=='mostrarPedidos'){
    print(Pedido::mostrarPedidos());
}

if(array_key_exists('funcion',$_REQUEST) && $_REQUEST['funcion']=='eliminarPedidoBbdd'){
    print(Pedido::eliminarPedidoBbdd());
}


if(array_key_exists('funcion',$_REQUEST) && $_REQUEST['funcion']=='actualizar'){
    print(Pedido::actualizarEstado());
}

?>