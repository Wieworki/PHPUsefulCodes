/*
PHP cuenta con una función nativa llamada "curl_init" que permite acceder a otras direccions URL.
En este archivo se plantea una función auxiliar, que facilita los llamados a otras direcciones, permitiendo
pasar parámetros por POST, y en el HEADER
*/

<?php

static function get_web_page($url,$post=[],$header=[]){
 //Array de opciones, aquí se configuran todos los parámetros a ser usados en la conexión a la URL
 $options = array(
   CURLOPT_RETURNTRANSFER => true,        //Permite recuperar la respuesta que devuelve la página web a la que accedemos
   CURLOPT_HEADER         => false,       //Con "false" no incluimos el header en el output
   CURLOPT_FOLLOWLOCATION => true,        //Con "true", php seguira todos los redireccionamientos que intente la página web accedida
   CURLOPT_MAXREDIRS      => 10,          //Numero máximo de redireccionamientos
   CURLOPT_ENCODING       => "",          //Con "" se envian todos los tipos de codificación soportados
   CURLOPT_USERAGENT      => "test",      //Contenido del header "User Agent: " que es usado en la petición HTTP
   CURLOPT_AUTOREFERER    => "true",      //Con "true" establece automáticamente el valor del campo "Referer" en las peticiones que siguen a una redirección
   CURLOPT_CONNECTTIMEOUT => 120,         //Numero máximo de segundos a esperar cuando se está intentando conectar. Con 0 se espera indefinidamente
   CURLOPT_TIMEOUT        => 120,         //Número máximo de segundos para ejecutar funciones cURL
   CURLOPT_POSTFIELDS     => $post,       //Incluye todos los datos a pasar vía HTTP "POST". 
   CURLOPT_HTTPHEADER     => $header      //Array e campos para el header HTTP
 ); 
  
  $ch = curl_init($url);            //Iniciamos una nueva sesión, y obtenemos el manipulador curl. Ya le pasamos la dirección URL a la que vamos a acceder
  curl_setopt_array($ch, $options); //Pasamos el array de opciones
  $content = curl_exec($ch);        //Realizamos el llamado para acceder a la página web. Como tenemos la opción "CURLOPT_RETURNTRANSFER" con true, obtendremos la respuesta del cliente (un JSON en una api rest)
  curl_close($ch);                  //Cerramos la sesión de curl y liberamos los recursos
  
  return $content;
}

/*
Ejemplo de uso pasando parámetros post

$postLogin = [
  'usuario' => 'admin',
  'password' => 'admin',
];

$response = get_web_page("www.apiPage.com/getData",$postLogin);
$datos = array();
$datos = json_decode($response);    //Si recuperamos un JSON, debemos hacer la conversión para poder manipularlo

//Lo que recuperemos estará como objeto, por lo que para acceder a los datos usamos la sintaxis $datos->propiedad1
*/
