<?php
    //IMPORTANTE: habilitar "extension=mysqli" en php.ini
    
    /*////Para determinar el path al archivo .ini
    $inipath = php_ini_loaded_file();
    if ($inipath) {
        echo 'Ubicacion php.ini: ' . $inipath;
    } else {
        echo 'El archivo php.ini no esta cargado';
    }
    */////////
    
    //////PHP info
    //phpinfo();

    ////////Var dump para verificar que la funcion existe y mysqli está funcionando
    /*
    var_dump(function_exists('mysqli_connect'));
    */

    include "db_credentials.php"; //Necesitamos las credenciales de la base de datos para poder manipularla
    $errorNumber = 0;             //Usado para el log de errores
    $errorMessage = "";           //Usado para el log de errores

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);  //Mostramos el detalle de los errores (solo en etapa de desarrollo se debe habilitar)
    try {
        $db = new mysqli($host, $user, $pass, $dbname, $port);
        $db->set_charset($charset);                             
        $db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
    } catch (\mysqli_sql_exception $e) {
        throw new \mysqli_sql_exception($e->getMessage(), $e->getCode());
    }
    unset($host, $dbname, $user, $pass, $charset, $port); 

    //Funciones

    function prepared_query($mysqli, $sql, $params, $types = "")
    {
        global $errorMessage;                               
        $types = $types ?: str_repeat("s", count($params)); //"s" es de string

        try{
            $stmt = $mysqli->prepare($sql);
        }
        catch (Exception $e) {
            $errorNumber = $e->getCode();
            errorHandler($errorNumber, $errorMessage);
            return $errorMessage;
        }

        if(!empty($params)){                        //
            $stmt->bind_param($types, ...$params);  //Primero recibimos los tipos de los parametros, luego, los parametros
        }

        try{
            $stmt->execute();
        }
        catch (Exception $e) {
            $errorNumber = $e->getCode();
            errorHandler($errorNumber, $errorMessage);
            return $errorMessage;
        }
        return $stmt;
    }

    function prepared_select($mysqli, $sql, $params = [], $types = "") {
        $query = prepared_query($mysqli, $sql, $params, $types);
        if($query != ""){
            return $query->get_result();
        }
    }

    function errorHandler($errorNumber,&$errorMessage){
        switch($errorNumber){
            case "1052":
                $errorMessage = "error en una foreign key";
                break;
            case "1054":
                $errorMessage = "columna desconocida";
                break;
            case "1062":
                $errorMessage = "entrada duplicada";
                break;
            case "1064":
                $errorMessage = "error de sintaxis";
                break;                    
            default:
                $errorMessage = "codigo de error: ".$errorNumber;
                break;
        }
    }

 ?> 
