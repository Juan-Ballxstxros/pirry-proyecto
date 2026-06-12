<?php
include("../conexion.php");

$token = $_POST['token'];
$password = $_POST['password'];

// VALIDACIÓN SEGURIDAD
if(
    strlen($password) < 8 ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[0-9]/', $password)
){
    die("La contraseña debe tener:
    mínimo 8 caracteres,
    una mayúscula
    y un número.");
}

// ENCRIPTAR
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// ACTUALIZAR
$sql = "UPDATE usuarios
SET contrasena='$passwordHash',
reset_token=NULL,
token_expira=NULL
WHERE reset_token='$token'";

mysqli_query($conexion, $sql);

echo "Contraseña actualizada correctamente";
?>