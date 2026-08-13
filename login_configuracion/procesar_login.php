<?php

session_start();
include("../conexion.php");

$correo = trim($_POST['correo']);
$contrasena = trim($_POST['contrasena']);

$sql = "SELECT * FROM usuarios WHERE correo='$correo' LIMIT 1";

$resultado = mysqli_query($conexion, $sql);

if(mysqli_num_rows($resultado) > 0){

    $fila = mysqli_fetch_assoc($resultado);

    // VALIDAR CONTRASEÑA
    if($contrasena == $fila['contrasena']){

        $_SESSION['id'] = $fila['id'];
        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['rol'] = $fila['rol'];

        // REDIRECCIÓN CORRECTA
        header("Location: admin_inicio.php");
        exit();

    } else {

        echo "Contraseña incorrecta";

    }

} else {

    echo "Usuario no encontrado";

}

?>