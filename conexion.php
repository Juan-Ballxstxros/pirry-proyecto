<?php

$servidor = "localhost";
$usuario = "root";
$password = "";
$basedatos = "usuarios";  

$conexion = mysqli_connect($servidor, $usuario, $password, $basedatos);

if(!$conexion){
    die("Error de conexión: " . mysqli_connect_error());
}

?>