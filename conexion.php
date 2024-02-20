<?php
    //crear funcion para conectarse a la base de datos
    
    function conectar(){
        $serverName = "10.100.120.7";
        $connectionInfo = array( "Database"=>"SipeDes", "UID"=>"sa", "PWD"=>"84+-blaster32","CharacterSet"=>"UTF-8");
        $con = sqlsrv_connect($serverName, $connectionInfo);
        
        return $con;
    }

?>